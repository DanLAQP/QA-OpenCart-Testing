<?php

use PHPUnit\Framework\TestCase;

class CatalogoBusquedaIntegrationTest extends TestCase
{
    private PDO $db;
    private string $prefix;

    protected function setUp(): void
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'opencart';
        $user = getenv('DB_USER') ?: 'opencart';
        $pass = getenv('DB_PASS') ?: 'opencart';

        $this->prefix = getenv('DB_PREFIX') ?: 'oc_';

        $this->db = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    /** @test CI-CAT-01 */
    public function testListaProductosDeUnaCategoriaActiva(): void
    {
        $categoria = $this->obtenerCategoriaActiva();
        $productos = $this->obtenerProductosPorCategoria((int) $categoria['category_id']);

        $this->assertIsArray($productos);
        $this->assertNotEmpty($productos);
    }

    /** @test CI-CAT-02 */
    public function testBusquedaPorNombreRetornaCoincidenciasReales(): void
    {
        $producto = $this->obtenerProductoVisible();
        $termino = substr((string) $producto['name'], 0, 4);

        $resultado = $this->buscarProductos($termino);

        $this->assertNotEmpty($resultado);
        $this->assertContains((int) $producto['product_id'], array_column($resultado, 'product_id'));
    }

    /** @test CI-CAT-03 */
    public function testBusquedaSinCoincidenciasRetornaListaVacia(): void
    {
        $resultado = $this->buscarProductos('zzzx-no-existe-producto');

        $this->assertSame([], $resultado);
    }

    /** @test CI-CAT-04 */
    public function testDetalleDeProductoExponeFabricanteYRelacionadosCuandoExisten(): void
    {
        $producto = $this->obtenerProductoVisible();
        $detalle = $this->obtenerDetalleProducto((int) $producto['product_id']);

        $this->assertSame((int) $producto['product_id'], (int) $detalle['product_id']);
        $this->assertArrayHasKey('name', $detalle);
        $this->assertArrayHasKey('manufacturer_id', $detalle);
        $this->assertArrayHasKey('related_count', $detalle);
    }

    /** @test CI-CAT-05 */
    public function testComparacionConservaMaximoDeCuatroProductos(): void
    {
        $productos = $this->obtenerProductosVisibles(5);
        $comparacion = [];

        foreach ($productos as $producto) {
            $comparacion = $this->agregarAComparacion($comparacion, (int) $producto['product_id']);
        }

        $this->assertCount(4, $comparacion);
        $this->assertSame((int) $productos[4]['product_id'], end($comparacion));
    }

    private function obtenerCategoriaActiva(): array
    {
        $sql = "SELECT c.category_id
                FROM {$this->prefix}category c
                INNER JOIN {$this->prefix}category_description cd 
                    ON cd.category_id = c.category_id
                INNER JOIN {$this->prefix}product_to_category pc 
                    ON pc.category_id = c.category_id
                INNER JOIN {$this->prefix}product p 
                    ON p.product_id = pc.product_id
                WHERE c.status = 1 
                AND p.status = 1
                GROUP BY c.category_id
                LIMIT 1";

        $categoria = $this->db->query($sql)->fetch();

        if (!$categoria) {
            $this->fail('No existe una categoría activa con productos activos para ejecutar la prueba.');
        }

        return $categoria;
    }

    private function obtenerProductosPorCategoria(int $categoryId): array
    {
        $sql = "SELECT p.product_id, pd.name
                FROM {$this->prefix}product p
                INNER JOIN {$this->prefix}product_description pd ON pd.product_id = p.product_id
                INNER JOIN {$this->prefix}product_to_category pc ON pc.product_id = p.product_id
                WHERE p.status = 1 AND pc.category_id = :category_id
                ORDER BY p.product_id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);

        return $stmt->fetchAll();
    }

    private function obtenerProductoVisible(): array
    {
        $sql = "SELECT p.product_id, p.manufacturer_id, pd.name
                FROM {$this->prefix}product p
                INNER JOIN {$this->prefix}product_description pd ON pd.product_id = p.product_id
                WHERE p.status = 1
                LIMIT 1";

        $producto = $this->db->query($sql)->fetch();

        if (!$producto) {
            $this->fail('No existe un producto visible para ejecutar la prueba.');
        }

        return $producto;
    }

    private function obtenerProductosVisibles(int $limit): array
    {
        $sql = "SELECT p.product_id, pd.name
                FROM {$this->prefix}product p
                INNER JOIN {$this->prefix}product_description pd ON pd.product_id = p.product_id
                WHERE p.status = 1
                ORDER BY p.product_id ASC
                LIMIT {$limit}";

        $productos = $this->db->query($sql)->fetchAll();

        if (count($productos) < $limit) {
            $this->fail('No hay suficientes productos visibles para validar la comparación.');
        }

        return $productos;
    }

    private function buscarProductos(string $termino): array
    {
        $sql = "SELECT p.product_id, pd.name
                FROM {$this->prefix}product p
                INNER JOIN {$this->prefix}product_description pd ON pd.product_id = p.product_id
                WHERE p.status = 1 AND pd.name LIKE :term
                ORDER BY p.product_id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':term' => '%' . $termino . '%']);

        return $stmt->fetchAll();
    }

    private function obtenerDetalleProducto(int $productId): array
    {
        $sql = "SELECT p.product_id, p.manufacturer_id, pd.name,
                       (SELECT COUNT(*) FROM {$this->prefix}product_related pr WHERE pr.product_id = p.product_id) AS related_count
                FROM {$this->prefix}product p
                INNER JOIN {$this->prefix}product_description pd ON pd.product_id = p.product_id
                WHERE p.product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        $detalle = $stmt->fetch();

        if (!$detalle) {
            $this->fail('No se pudo recuperar el detalle del producto.');
        }

        return $detalle;
    }

    private function agregarAComparacion(array $comparacion, int $productId): array
    {
        $comparacion = array_values(array_filter(
            $comparacion,
            static fn (int $id): bool => $id !== $productId
        ));

        $comparacion[] = $productId;

        if (count($comparacion) > 4) {
            array_shift($comparacion);
        }

        return $comparacion;
    }
}
