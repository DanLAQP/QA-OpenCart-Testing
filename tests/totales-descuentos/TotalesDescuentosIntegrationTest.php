<?php

use PHPUnit\Framework\TestCase;

class TotalesDescuentosIntegrationTest extends TestCase
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
    public function testPersisteSubtotalEImpuesto(): void
    {
        $orderId = $this->crearOrdenConTotales(100.00, 18.00, 0.00, 0.00);

        $this->assertEquals(100.00, $this->obtenerValor($orderId, 'sub_total'));
        $this->assertEquals(18.00, $this->obtenerValor($orderId, 'tax'));
    }

    /** @test */
    public function testAplicaDescuentoPorCupon(): void
    {
        $orderId = $this->crearOrdenConTotales(100.00, 18.00, -10.00, 0.00);

        $this->assertEquals(-10.00, $this->obtenerValor($orderId, 'coupon'));
        $this->assertEquals(108.00, $this->obtenerValor($orderId, 'total'));
    }

    /** @test */
    public function testAplicaVoucher(): void
    {
        $orderId = $this->crearOrdenConTotales(100.00, 18.00, 0.00, -20.00);

        $this->assertEquals(-20.00, $this->obtenerValor($orderId, 'voucher'));
        $this->assertEquals(98.00, $this->obtenerValor($orderId, 'total'));
    }

    /** @test */
    public function testTotalFinalEsConsistenteConTodosLosComponentes(): void
    {
        $orderId = $this->crearOrdenConTotales(100.00, 18.00, -10.00, -20.00);

        $esperado = 100.00 + 18.00 - 10.00 - 20.00;
        $this->assertEquals($esperado, $this->obtenerValor($orderId, 'total'));
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

    private function crearOrdenConTotales(float $subtotal, float $tax, float $coupon, float $voucher): int
    {
        $total = $subtotal + $tax + $coupon + $voucher;

        $sql = "INSERT INTO {$this->prefix}order
            (invoice_no, invoice_prefix, store_id, store_name, store_url, customer_id, customer_group_id,
             firstname, lastname, email, telephone, payment_firstname, payment_lastname, payment_company,
             payment_address_1, payment_address_2, payment_city, payment_postcode, payment_zone,
             payment_zone_id, payment_country, payment_country_id, payment_address_format, payment_custom_field,
             payment_method, payment_code, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1,
             shipping_address_2, shipping_city, shipping_postcode, shipping_zone, shipping_zone_id, shipping_country,
             shipping_country_id, shipping_address_format, shipping_custom_field, shipping_method, shipping_code, comment,
             total, order_status_id, affiliate_id, commission, marketing_id, tracking, language_id, currency_id,
             currency_code, currency_value, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified)
            VALUES
            (0, '', 0, 'OpenCart', 'http://127.0.0.1:8000/', 0, 1,
             'Total', 'Tester', 'total@test.com', '900000000', 'Total', 'Tester', '',
             'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru', 0, '', '',
             'Cash On Delivery', 'cod', 'Total', 'Tester', '', 'Street 1', '', 'Lima', '15001', 'Lima', 0, 'Peru',
             0, '', '', 'Flat Shipping Rate', 'flat.flat', '', :total, 1, 0, 0.0000, 0, '', 1, 1,
             'USD', 1.00000000, '127.0.0.1', '', 'PHPUnit', 'es', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':total' => $total]);
        $orderId = (int) $this->db->lastInsertId();

        $this->insertTotal($orderId, 'sub_total', 'Sub-Total', $subtotal, 1);
        $this->insertTotal($orderId, 'tax', 'Tax', $tax, 5);
        if ($coupon !== 0.0) {
            $this->insertTotal($orderId, 'coupon', 'Coupon', $coupon, 6);
        }
        if ($voucher !== 0.0) {
            $this->insertTotal($orderId, 'voucher', 'Voucher', $voucher, 7);
        }
        $this->insertTotal($orderId, 'total', 'Total', $total, 9);

        return $orderId;
    }

    private function insertTotal(int $orderId, string $code, string $title, float $value, int $sortOrder): void
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->prefix}order_total (order_id, extension, code, title, value, sort_order) VALUES (:order_id, :code, :code, :title, :value, :sort_order)");
        $stmt->execute([
            ':order_id' => $orderId,
            ':code' => $code,
            ':title' => $title,
            ':value' => $value,
            ':sort_order' => $sortOrder,
        ]);
    }

    private function obtenerValor(int $orderId, string $code): float
    {
        $stmt = $this->db->prepare("SELECT value FROM {$this->prefix}order_total WHERE order_id = :order_id AND code = :code LIMIT 1");
        $stmt->execute([':order_id' => $orderId, ':code' => $code]);
        $value = $stmt->fetchColumn();

        if ($value === false) {
            $this->fail('No se encontró el total solicitado.');
        }

        return (float) $value;
    }
}
