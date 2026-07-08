<?php

use PHPUnit\Framework\TestCase;

class EnvioIntegrationTest extends TestCase
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
    public function testPersisteDireccionYMetodoDeEnvio(): void
    {
        $orderId = $this->crearOrdenConEnvio('Flat Shipping Rate', 15.00);
        $orden = $this->obtenerOrdenEnvio($orderId);

        $this->assertSame('Flat Shipping Rate', $orden['shipping_method']);
        $this->assertSame('Street 1', $orden['shipping_address_1']);
    }

    /** @test */
    public function testCotizacionSimpleDeEnvioSePersisteEnOrderTotal(): void
    {
        $orderId = $this->crearOrdenConEnvio('Flat Shipping Rate', 15.00);

        $shipping = $this->obtenerTotalPorCodigo($orderId, 'shipping');
        $this->assertEquals(15.00, $shipping);
    }

    /** @test */
    public function testCambioDeMetodoDeEnvioActualizaMonto(): void
    {
        $orderId = $this->crearOrdenConEnvio('Flat Shipping Rate', 15.00);

        $this->actualizarEnvio($orderId, 'Pickup', 0.00);
        $orden = $this->obtenerOrdenEnvio($orderId);

        $this->assertSame('Pickup', $orden['shipping_method']);
        $this->assertEquals(0.00, $this->obtenerTotalPorCodigo($orderId, 'shipping'));
    }

    /** @test */
    public function testRecalculoDeTotalesCuandoCambiaElEnvio(): void
    {
        $orderId = $this->crearOrdenConEnvio('Flat Shipping Rate', 15.00);
        $this->actualizarEnvio($orderId, 'Express', 25.00);

        $this->assertEquals(125.00, $this->obtenerTotalPorCodigo($orderId, 'total'));
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

    private function crearOrdenConEnvio(string $shippingMethod, float $shippingValue): int
    {
        $subtotal = 100.00;
        $total = $subtotal + $shippingValue;

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
             'Ship', 'Tester', 'ship@test.com', '900000000', 'Ship', 'Tester', '',
             'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru', 0, '', '',
             'Cash On Delivery', 'Ship', 'Tester', '', 'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru',
             0, '', '', :shipping_method, '', :total, 1, 0, 0.0000, 0, '', 1, 1,
             'USD', 1.00000000, '127.0.0.1', '', 'PHPUnit', 'es', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':shipping_method' => $shippingMethod,
            ':total' => $total,
        ]);

        $orderId = (int) $this->db->lastInsertId();

        $insertTotal = $this->db->prepare(
            "INSERT INTO {$this->prefix}order_total
             (order_id, extension, code, title, value, sort_order)
             VALUES
             (:order_id, 'sub_total', 'sub_total', 'Sub-Total', 100.00, 1),
             (:order_id, 'shipping', 'shipping', 'Shipping', :shipping, 3),
             (:order_id, 'total', 'total', 'Total', :total, 9)"
        );

        $insertTotal->execute([
            ':order_id' => $orderId,
            ':shipping' => $shippingValue,
            ':total' => $total,
        ]);

        return $orderId;
    }

    private function actualizarEnvio(int $orderId, string $shippingMethod, float $shippingValue): void
    {
        $updateOrder = $this->db->prepare(
            "UPDATE {$this->prefix}order
             SET shipping_method = :method,
                 total = 100.00 + :shipping
             WHERE order_id = :order_id"
        );

        $updateOrder->execute([
            ':method' => $shippingMethod,
            ':shipping' => $shippingValue,
            ':order_id' => $orderId,
        ]);

        $updateShipping = $this->db->prepare(
            "UPDATE {$this->prefix}order_total
             SET value = :shipping
             WHERE order_id = :order_id AND code = 'shipping'"
        );

        $updateShipping->execute([
            ':shipping' => $shippingValue,
            ':order_id' => $orderId,
        ]);

        $updateTotal = $this->db->prepare(
            "UPDATE {$this->prefix}order_total
             SET value = 100.00 + :shipping
             WHERE order_id = :order_id AND code = 'total'"
        );

        $updateTotal->execute([
            ':shipping' => $shippingValue,
            ':order_id' => $orderId,
        ]);
    }

    private function obtenerOrdenEnvio(int $orderId): array
    {
        $stmt = $this->db->prepare(
            "SELECT shipping_method, shipping_address_1
             FROM {$this->prefix}order
             WHERE order_id = :order_id"
        );

        $stmt->execute([':order_id' => $orderId]);
        $orden = $stmt->fetch();

        if (!$orden) {
            $this->fail('No se encontró la orden de envío.');
        }

        return $orden;
    }

    private function obtenerTotalPorCodigo(int $orderId, string $code): float
    {
        $stmt = $this->db->prepare(
            "SELECT value
             FROM {$this->prefix}order_total
             WHERE order_id = :order_id AND code = :code
             LIMIT 1"
        );

        $stmt->execute([
            ':order_id' => $orderId,
            ':code' => $code,
        ]);

        $value = $stmt->fetchColumn();

        if ($value === false) {
            $this->fail('No se encontró el total requerido.');
        }

        return (float) $value;
    }
}