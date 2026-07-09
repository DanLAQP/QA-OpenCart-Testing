<?php

use PHPUnit\Framework\TestCase;

class GestionInventarioIntegrationTest extends TestCase
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

    /** @test CI-INV-01 */
    public function testActualizacionDeStockSePersisteEnProducto(): void
    {
        $producto = $this->obtenerProductoEditable();

        $this->actualizarStock((int) $producto['product_id'], 12);

        $this->assertSame(12, $this->obtenerStock((int) $producto['product_id']));
    }

    /** @test CI-INV-02 */
    public function testProductoActivoYDisponibleSeMarcaComoComprable(): void
    {
        $producto = $this->obtenerProductoEditable();
        $this->configurarProducto((int) $producto['product_id'], 5, 1, date('Y-m-d', strtotime('-1 day')));

        $this->assertTrue($this->esComprable((int) $producto['product_id']));
    }

    /** @test CI-INV-03 */
    public function testProductoConFechaFuturaNoEsComprable(): void
    {
        $producto = $this->obtenerProductoEditable();
        $this->configurarProducto((int) $producto['product_id'], 5, 1, date('Y-m-d', strtotime('+2 day')));

        $this->assertFalse($this->esComprable((int) $producto['product_id']));
    }

    /** @test CI-INV-04 */
    public function testCantidadExactaAlStockDisponibleEsAceptada(): void
    {
        $producto = $this->obtenerProductoEditable();
        $this->actualizarStock((int) $producto['product_id'], 3);

        $this->assertTrue($this->validarSolicitudDeCantidad((int) $producto['product_id'], 3));
    }

    /** @test CI-INV-05 */
    public function testCantidadSuperiorAlStockDisponibleEsRechazada(): void
    {
        $producto = $this->obtenerProductoEditable();
        $this->actualizarStock((int) $producto['product_id'], 3);

        $this->assertFalse($this->validarSolicitudDeCantidad((int) $producto['product_id'], 4));
    }

    private function obtenerProductoEditable(): array
    {
        $sql = "SELECT product_id, quantity, status, date_available
                FROM {$this->prefix}product
                LIMIT 1";

        $producto = $this->db->query($sql)->fetch();

        if (!$producto) {
            $this->fail('No existe un producto para validar inventario.');
        }

        return $producto;
    }

    private function actualizarStock(int $productId, int $stock): void
    {
        $sql = "UPDATE {$this->prefix}product
                SET quantity = :quantity
                WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':quantity' => $stock,
            ':product_id' => $productId,
        ]);
    }

    private function obtenerStock(int $productId): int
    {
        $sql = "SELECT quantity FROM {$this->prefix}product WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);

        return (int) $stmt->fetchColumn();
    }

    private function configurarProducto(int $productId, int $stock, int $status, string $availableDate): void
    {
        $sql = "UPDATE {$this->prefix}product
                SET quantity = :quantity,
                    status = :status,
                    date_available = :date_available
                WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':quantity' => $stock,
            ':status' => $status,
            ':date_available' => $availableDate,
            ':product_id' => $productId,
        ]);
    }

    private function esComprable(int $productId): bool
    {
        $sql = "SELECT quantity, status, date_available
                FROM {$this->prefix}product
                WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        $producto = $stmt->fetch();

        if (!$producto) {
            return false;
        }

        return (int) $producto['status'] === 1
            && $producto['date_available'] <= date('Y-m-d')
            && (int) $producto['quantity'] > 0;
    }

    private function validarSolicitudDeCantidad(int $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que cero.');
        }

        return $quantity <= $this->obtenerStock($productId);
    }
}
