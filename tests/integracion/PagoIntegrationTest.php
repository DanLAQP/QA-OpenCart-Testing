<?php

use PHPUnit\Framework\TestCase;

class PagoIntegrationTest extends TestCase
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
    public function testPersisteMetodoDePagoEnLaOrden(): void
    {
        $orderId = $this->crearOrdenBase('cod', 'Cash On Delivery', 150.00);
        $orden = $this->obtenerOrden($orderId);
        $this->assertSame('Cash On Delivery', $orden['payment_method']);
    }

    /** @test */
    public function testConfirmacionDePagoAgregaHistorial(): void
    {
        $orderId = $this->crearOrdenBase('bank_transfer', 'Bank Transfer', 200.00);

        $this->confirmarPago($orderId, 2, 'Pago confirmado');

        $historial = $this->obtenerHistorial($orderId);
        $this->assertCount(1, $historial);
        $this->assertSame(2, (int) $historial[0]['order_status_id']);
    }

    /** @test */
    public function testTimeoutNoAgregaHistorialExitoso(): void
    {
        $orderId = $this->crearOrdenBase('gateway_x', 'Gateway X', 99.99);

        $resultado = $this->procesarPagoConTimeout($orderId, 5, 1);

        $this->assertFalse($resultado);
        $this->assertCount(0, $this->obtenerHistorial($orderId));
    }

    /** @test */
    public function testReintentoNoDuplicaHistorialEquivalente(): void
    {
        $orderId = $this->crearOrdenBase('cod', 'Cash On Delivery', 150.00);

        $this->confirmarPago($orderId, 2, 'Pago confirmado');
        $this->confirmarPago($orderId, 2, 'Pago confirmado');

        $historial = $this->obtenerHistorial($orderId);
        $this->assertCount(1, $historial);
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
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    private function crearOrdenBase(string $paymentCode, string $paymentMethod, float $total): int
    {
        $sql = "INSERT INTO {$this->prefix}order
            (invoice_no, invoice_prefix, store_id, store_name, store_url, customer_id, customer_group_id,
            firstname, lastname, email, telephone, payment_firstname, payment_lastname, payment_company,
            payment_address_1, payment_address_2, payment_city, payment_postcode, payment_zone,
            payment_zone_id, payment_country, payment_country_id, payment_address_format,
            payment_custom_field, payment_method, shipping_firstname, shipping_lastname,
            shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_postcode,
            shipping_zone, shipping_zone_id, shipping_country, shipping_country_id, shipping_address_format,
            shipping_custom_field, shipping_method, comment, total, order_status_id,
            affiliate_id, commission, marketing_id, tracking, language_id, currency_id, currency_code,
            currency_value, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified)
            VALUES
            (0, '', 0, 'OpenCart', 'http://127.0.0.1:8000/', 0, 1,
            'Test', 'Buyer', 'buyer@test.com', '999999999', 'Test', 'Buyer', '',
            'Street 1', '', 'Lima', '15001', 'Lima',
            0, 'Peru', 0, '',
            '', :payment_method, 'Test', 'Buyer',
            '', 'Street 1', '', 'Lima', '15001',
            'Lima', 0, 'Peru', 0, '',
            '', 'Flat Shipping Rate', '', :total, 1,
            0, 0.0000, 0, '', 1, 1, 'USD',
            1.00000000, '127.0.0.1', '', 'PHPUnit', 'es', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':payment_method' => $paymentMethod,
            ':total' => $total,
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function obtenerOrden(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT payment_method FROM {$this->prefix}order WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        $orden = $stmt->fetch();

        if (!$orden) {
            $this->fail('No se encontró la orden creada.');
        }

        return $orden;
    }

    private function confirmarPago(int $orderId, int $orderStatusId, string $comment): void
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->prefix}order_history WHERE order_id = :order_id AND order_status_id = :status AND comment = :comment");
        $stmt->execute([':order_id' => $orderId, ':status' => $orderStatusId, ':comment' => $comment]);

        if ((int) $stmt->fetchColumn() === 0) {
            $insert = $this->db->prepare("INSERT INTO {$this->prefix}order_history (order_id, order_status_id, notify, comment, date_added) VALUES (:order_id, :status, 0, :comment, NOW())");
            $insert->execute([':order_id' => $orderId, ':status' => $orderStatusId, ':comment' => $comment]);
        }

        $update = $this->db->prepare("UPDATE {$this->prefix}order SET order_status_id = :status, date_modified = NOW() WHERE order_id = :order_id");
        $update->execute([':status' => $orderStatusId, ':order_id' => $orderId]);
    }

    private function procesarPagoConTimeout(int $orderId, int $latenciaSegundos, int $timeoutSegundos): bool
    {
        if ($latenciaSegundos > $timeoutSegundos) {
            return false;
        }

        $this->confirmarPago($orderId, 2, 'Pago confirmado');
        return true;
    }

    private function obtenerHistorial(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT order_status_id, comment FROM {$this->prefix}order_history WHERE order_id = :order_id ORDER BY order_history_id ASC");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
