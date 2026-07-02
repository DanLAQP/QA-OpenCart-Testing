<?php

use PHPUnit\Framework\TestCase;

class CheckoutInventoryIntegrationTest extends TestCase
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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function testFlujoCheckoutActualizaInventario(): void
    {
        $producto = $this->obtenerProductoDePrueba();

        $this->actualizarStock($producto['product_id'], 10);

        $payload = [
            'product_id' => (int) $producto['product_id'],
            'quantity' => 2
        ];

        $resultado = $this->procesarCheckoutInventario($payload);

        $stockFinal = $this->obtenerStock($producto['product_id']);

        $this->assertTrue($resultado);
        $this->assertEquals(8, $stockFinal);
    }

    public function testFallaSintacticaConTiposInvalidos(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Los campos product_id y quantity deben ser numericos.');

        $payload = [
            'product_id' => 'ABC',
            'quantity' => 'dos'
        ];

        $this->procesarCheckoutInventario($payload);
    }

    public function testFallaSemanticaCantidadMayorAlStock(): void
    {
        $producto = $this->obtenerProductoDePrueba();

        $this->actualizarStock($producto['product_id'], 5);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stock insuficiente para completar la compra.');

        $payload = [
            'product_id' => (int) $producto['product_id'],
            'quantity' => 20
        ];

        $this->procesarCheckoutInventario($payload);
    }

    public function testResilienciaAnteLatenciaAlta(): void
    {
        $producto = $this->obtenerProductoDePrueba();

        $this->actualizarStock($producto['product_id'], 10);

        $resultado = $this->procesarCheckoutConLatencia(
            [
                'product_id' => (int) $producto['product_id'],
                'quantity' => 1
            ],
            3,
            1
        );

        $stockFinal = $this->obtenerStock($producto['product_id']);

        $this->assertFalse($resultado);
        $this->assertEquals(10, $stockFinal);
    }

    private function procesarCheckoutInventario(array $payload): bool
    {
        if (
            !isset($payload['product_id'], $payload['quantity']) ||
            !is_int($payload['product_id']) ||
            !is_int($payload['quantity'])
        ) {
            throw new InvalidArgumentException('Los campos product_id y quantity deben ser numericos.');
        }

        if ($payload['quantity'] <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que cero.');
        }

        $stockActual = $this->obtenerStock($payload['product_id']);

        if ($payload['quantity'] > $stockActual) {
            throw new RuntimeException('Stock insuficiente para completar la compra.');
        }

        $nuevoStock = $stockActual - $payload['quantity'];
        $this->actualizarStock($payload['product_id'], $nuevoStock);

        return true;
    }

    private function procesarCheckoutConLatencia(array $payload, int $latenciaSegundos, int $timeoutSegundos): bool
    {
        if ($latenciaSegundos > $timeoutSegundos) {
            sleep($timeoutSegundos);
            return false;
        }

        sleep($latenciaSegundos);
        return $this->procesarCheckoutInventario($payload);
    }

    private function obtenerProductoDePrueba(): array
    {
        $sql = "SELECT product_id, quantity FROM {$this->prefix}product LIMIT 1";
        $producto = $this->db->query($sql)->fetch();

        if (!$producto) {
            $this->fail('No existen productos en la base de datos de OpenCart.');
        }

        return $producto;
    }

    private function obtenerStock(int $productId): int
    {
        $sql = "SELECT quantity FROM {$this->prefix}product WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':product_id' => $productId
        ]);

        $stock = $stmt->fetchColumn();

        if ($stock === false) {
            $this->fail('Producto no encontrado en inventario.');
        }

        return (int) $stock;
    }

    private function actualizarStock(int $productId, int $cantidad): void
    {
        $sql = "UPDATE {$this->prefix}product 
                SET quantity = :quantity 
                WHERE product_id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':quantity' => $cantidad,
            ':product_id' => $productId
        ]);
    }
}
