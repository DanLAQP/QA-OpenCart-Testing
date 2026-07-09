<?php

use PHPUnit\Framework\TestCase;

class PedidoIntegrationTest extends TestCase
{
    private PDO $db;
    private string $prefix;

    protected function setUp(): void
    {
        $this->db = $this->connect();
        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    /** @test */
    public function testCreacionCompletaDePedidoConLineasYTotales(): void
    {
        $orderId = $this->crearPedidoCompleto();

        $this->assertGreaterThan(0, $orderId);
        $this->assertCount(1, $this->obtenerLineas($orderId));
        $this->assertNotEmpty($this->obtenerTotales($orderId));
    }

    /** @test */
    public function testCambioDeEstadoAgregaHistorial(): void
    {
        $orderId = $this->crearPedidoCompleto();
        $this->actualizarEstado($orderId, 3, 'Pedido procesado');

        $historial = $this->obtenerHistorial($orderId);
        $this->assertCount(1, $historial);
        $this->assertSame(3, (int) $historial[0]['order_status_id']);
    }

    /** @test */
    public function testHistorialDelPedidoSeRecuperaEnOrdenCronologico(): void
    {
        $orderId = $this->crearPedidoCompleto();
        $this->actualizarEstado($orderId, 2, 'Creado');
        $this->actualizarEstado($orderId, 5, 'Completado');

        $historial = $this->obtenerHistorial($orderId);
        $this->assertCount(2, $historial);
        $this->assertSame('Creado', $historial[0]['comment']);
        $this->assertSame('Completado', $historial[1]['comment']);
    }

    /** @test */
    public function testConsistenciaEntreLineasYTotalRegistrado(): void
    {
        $orderId = $this->crearPedidoCompleto();

        $lineas = $this->obtenerLineas($orderId);
        $totales = $this->obtenerTotales($orderId);
        $sumaLineas = array_sum(array_map(static fn(array $linea): float => (float) $linea['total'], $lineas));
        $totalFinal = $this->obtenerValorTotal($totales, 'total');

        $this->assertEquals(round($sumaLineas, 4), round($totalFinal, 4));
    }

    private function connect(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'opencart';
        $user = getenv('DB_USER') ?: 'opencart';
        $pass = getenv('DB_PASS') ?: 'opencart';
        $this->prefix = getenv('DB_PREFIX') ?: 'oc_';

        return new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }

    private function crearPedidoCompleto(): int
    {
        $orderId = $this->crearOrdenBase(250.00);

        $insertProduct = $this->db->prepare("INSERT INTO {$this->prefix}order_product (order_id, product_id, name, model, quantity, price, total, tax, reward) VALUES (:order_id, 1, 'Producto Test', 'MODEL-1', 2, 125.00, 250.00, 0.00, 0)");
        $insertProduct->execute([':order_id' => $orderId]);

        $insertTotal = $this->db->prepare("INSERT INTO {$this->prefix}order_total (order_id, extension, code, title, value, sort_order) VALUES (:order_id, 'total', 'total', 'Total', 250.00, 9)");
        $insertTotal->execute([':order_id' => $orderId]);

        return $orderId;
    }

    private function crearOrdenBase(float $total): int
    {
        $sql = "INSERT INTO {$this->prefix}order
            (invoice_no, invoice_prefix, store_id, store_name, store_url, customer_id, customer_group_id,
            firstname, lastname, email, telephone, payment_firstname, payment_lastname, payment_company,
            payment_address_1, payment_address_2, payment_city, payment_postcode, payment_zone,
            payment_zone_id, payment_country, payment_country_id, payment_address_format, payment_custom_field,
            payment_method, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1,
            shipping_address_2, shipping_city, shipping_postcode, shipping_zone, shipping_zone_id, shipping_country,
            shipping_country_id, shipping_address_format, shipping_custom_field, shipping_method, comment,
            total, order_status_id, affiliate_id, commission, marketing_id, tracking, language_id, currency_id,
            currency_code, currency_value, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified)
            VALUES
            (0, '', 0, 'OpenCart', 'http://127.0.0.1:8000/', 0, 1,
            'Order', 'Tester', 'order@test.com', '900000000', 'Order', 'Tester', '',
            'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru', 0, '', '',
            'Cash On Delivery', 'Order', 'Tester', '', 'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru',
            0, '', '', 'Flat Shipping Rate', '', :total, 1, 0, 0.0000, 0, '', 1, 1,
            'USD', 1.00000000, '127.0.0.1', '', 'PHPUnit', 'es', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':total' => $total]);

        return (int) $this->db->lastInsertId();
    }

    private function actualizarEstado(int $orderId, int $statusId, string $comment): void
    {
        $history = $this->db->prepare("INSERT INTO {$this->prefix}order_history (order_id, order_status_id, notify, comment, date_added) VALUES (:order_id, :status, 0, :comment, NOW())");
        $history->execute([':order_id' => $orderId, ':status' => $statusId, ':comment' => $comment]);

        $update = $this->db->prepare("UPDATE {$this->prefix}order SET order_status_id = :status WHERE order_id = :order_id");
        $update->execute([':status' => $statusId, ':order_id' => $orderId]);
    }

    private function obtenerLineas(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT total FROM {$this->prefix}order_product WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    private function obtenerTotales(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT code, value FROM {$this->prefix}order_total WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    private function obtenerHistorial(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT order_status_id, comment FROM {$this->prefix}order_history WHERE order_id = :order_id ORDER BY order_history_id ASC");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    private function obtenerValorTotal(array $totales, string $code): float
    {
        foreach ($totales as $total) {
            if ($total['code'] === $code) {
                return (float) $total['value'];
            }
        }

        $this->fail('No se encontró el total solicitado.');
    }
}
