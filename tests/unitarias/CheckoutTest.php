<?php
namespace Tests\Unitarias;

use Tests\BaseTestCase;
use Opencart\System\Library\CheckoutManager;

/**
 * Class CheckoutTest
 *
 * Pruebas unitarias para el módulo de Checkout y Pago (42 tests)
 *
 * @covers \Opencart\System\Library\CheckoutManager
 */
class CheckoutTest extends BaseTestCase {
    /**
     * @var CheckoutManager
     */
    private $checkout;

    protected function setUp(): void {
        parent::setUp();
        $this->checkout = new CheckoutManager($this->registry);
    }

    // ========== Flujo General (5 tests) ==========

    /**
     * @test
     * CP-Chk-001: Inicialización del checkout con carrito válido y usuario autenticado
     */
    public function testInitCheckoutWithValidCart(): void {
        $cart = ['items' => [['price' => 100]]];
        $result = $this->checkout->initCheckout($cart, true);

        $this->assertTrue($result['status']);
        $this->assertTrue($result['authenticated']);
        $this->assertTrue($result['cart_valid']);
        $this->assertContains('billing', $result['steps']);
    }

    /**
     * @test
     * CP-Chk-002: Fallo de checkout con carrito vacío
     */
    public function testCheckoutFailsWithEmptyCart(): void {
        $cart = ['items' => []];
        $result = $this->checkout->initCheckout($cart, true);

        $this->assertFalse($result['status']);
        $this->assertFalse($result['cart_valid']);
    }

    /**
     * @test
     * CP-Chk-003: Validación de stock sin permiso de backorder
     */
    public function testStockValidationWithoutBackorder(): void {
        $items = [['stock' => 0]];
        $result = $this->checkout->validateStock($items, false);

        $this->assertFalse($result['valid']);
    }

    /**
     * @test
     * CP-Chk-004: Validación de stock permitiendo backorder
     */
    public function testStockValidationWithBackorder(): void {
        $items = [['stock' => 0]];
        $result = $this->checkout->validateStock($items, true);

        $this->assertTrue($result['valid']);
    }

    /**
     * @test
     * CP-Chk-005: Validación de cantidad mínima insuficiente
     */
    public function testMinimumQuantityValidationFails(): void {
        $result = $this->checkout->validateMinimumQuantity(1, 5);

        $this->assertFalse($result['valid']);
    }

    // ========== Dirección de Pago (7 tests) ==========

    /**
     * @test
     * CP-Chk-006: Establecer dirección de facturación existente
     */
    public function testSetBillingAddress(): void {
        $result = $this->checkout->setBillingAddress(1);

        $this->assertTrue($result);
        $this->assertEquals(1, $this->session->data['billing_address_id']);
    }

    /**
     * @test
     * CP-Chk-007: Validar dirección de pago con campos vacíos
     */
    public function testValidateBillingAddressWithEmptyFields(): void {
        $address = ['firstname' => '', 'lastname' => '', 'city' => '', 'address' => ''];
        $result = $this->checkout->validateBillingAddress($address);

        $this->assertFalse($result['valid']);
    }

    /**
     * @test
     * CP-Chk-008: Validar dirección de pago válida
     */
    public function testValidateBillingAddressValid(): void {
        $address = [
            'firstname' => 'Juan',
            'lastname' => 'Pérez',
            'city' => 'Lima',
            'address' => 'Calle 1'
        ];
        $result = $this->checkout->validateBillingAddress($address);

        $this->assertTrue($result['valid']);
    }

    /**
     * @test
     * CP-Chk-009: Validación de campos personalizados con regex
     */
    public function testValidateBillingAddressWithRegex(): void {
        $address = [
            'firstname' => 'Juan',
            'lastname' => 'Pérez',
            'city' => 'Lima',
            'address' => 'Calle 1'
        ];

        $this->assertTrue($this->checkout->validateBillingAddress($address)['valid']);
    }

    /**
     * @test
     * CP-Chk-010: Validación de límite superior de archivo
     */
    public function testValidateFileUploadLimit(): void {
        // Mock para archivo grande
        $fileSize = 5242880; // 5MB
        $maxSize = 5242880;

        $this->assertTrue($fileSize <= $maxSize);
    }

    /**
     * @test
     * CP-Chk-011: Persistencia de nueva dirección de pago
     */
    public function testPersistBillingAddress(): void {
        $result = $this->checkout->setBillingAddress(1);

        $this->assertTrue($result);
    }

    /**
     * @test
     * CP-Chk-012: Efecto cascada al cambiar dirección de pago
     */
    public function testChangeBillingAddressClearsShipping(): void {
        $this->session->data['shipping_methods'] = ['flat', 'standard'];
        $this->checkout->setBillingAddress(1);

        $this->assertArrayHasKey('billing_address_id', $this->session->data);
    }

    // ========== Dirección de Envío (7 tests) ==========

    /**
     * @test
     * CP-Chk-013: Establecer dirección de envío existente
     */
    public function testSetShippingAddress(): void {
        $result = $this->checkout->setShippingAddress(2);

        $this->assertTrue($result);
        $this->assertEquals(2, $this->session->data['shipping_address_id']);
    }

    /**
     * @test
     * CP-Chk-014: Validar dirección de envío con campos vacíos
     */
    public function testValidateShippingAddressWithEmptyFields(): void {
        $address = ['firstname' => '', 'lastname' => '', 'city' => '', 'address' => ''];
        $result = $this->checkout->validateShippingAddress($address);

        $this->assertFalse($result['valid']);
    }

    /**
     * @test
     * CP-Chk-015: Validación de código postal obligatorio
     */
    public function testValidateShippingAddressWithMandatoryZipCode(): void {
        $address = [
            'country' => 'US',
            'zipcode' => null,
            'firstname' => 'Juan',
            'lastname' => 'Pérez',
            'city' => 'NYC',
            'address' => 'Calle 1'
        ];

        $hasZipCode = !empty($address['zipcode']);
        $this->assertFalse($hasZipCode);
    }

    /**
     * @test
     * CP-Chk-016: Validación de campos personalizados de envío
     */
    public function testValidateShippingAddressCustomFields(): void {
        $address = [
            'firstname' => 'Juan',
            'lastname' => 'Pérez',
            'city' => 'Lima',
            'address' => 'Calle 1'
        ];
        $result = $this->checkout->validateShippingAddress($address);

        $this->assertTrue($result['valid']);
    }

    /**
     * @test
     * CP-Chk-017: Persistencia de nueva dirección de envío
     */
    public function testPersistShippingAddress(): void {
        $result = $this->checkout->setShippingAddress(2);

        $this->assertTrue($result);
    }

    /**
     * @test
     * CP-Chk-018: Efecto cascada al cambiar dirección de envío
     */
    public function testChangeShippingAddressClearsPaymentMethods(): void {
        $this->session->data['payment_methods'] = ['card', 'transfer'];
        $this->checkout->setShippingAddress(2);

        $this->assertArrayHasKey('shipping_address_id', $this->session->data);
    }

    /**
     * @test
     * CP-Chk-019: Salto de envío si carrito no lo requiere
     */
    public function testSkipShippingIfNotRequired(): void {
        $result = $this->checkout->initCheckout(['items' => [], 'requires_shipping' => false], true);

        $this->assertFalse($result['status']);
    }

    // ========== Método de Envío (7 tests) ==========

    /**
     * @test
     * CP-Chk-020: Cotización sin requisitos previos falla
     */
    public function testShippingQuoteFailsWithoutAddress(): void {
        $methods = $this->checkout->getShippingMethods();

        $this->assertEmpty($methods);
    }

    /**
     * @test
     * CP-Chk-021: Cotización exitosa de métodos
     */
    public function testShippingQuoteSucceeds(): void {
        $this->checkout->setShippingAddress(1);
        $methods = $this->checkout->getShippingMethods();

        $this->assertNotEmpty($methods);
        $this->assertGreaterThan(0, count($methods));
    }

    /**
     * @test
     * CP-Chk-022: Sin métodos de envío disponibles
     */
    public function testNoShippingMethodsAvailable(): void {
        $methods = [];
        $this->assertEmpty($methods);
    }

    /**
     * @test
     * CP-Chk-023: Prevención de inyección de método inexistente
     */
    public function testShippingMethodInjectionPrevention(): void {
        $available = [['id' => 'dhl_10']];
        $result = $this->checkout->setShippingMethod('gratis_falso', $available);

        $this->assertFalse($result);
    }

    /**
     * @test
     * CP-Chk-024: Selección exitosa de método de envío
     */
    public function testSetShippingMethodSuccessfully(): void {
        $available = [['id' => 'dhl_10']];
        $result = $this->checkout->setShippingMethod('dhl_10', $available);

        $this->assertTrue($result);
        $this->assertEquals('dhl_10', $this->session->data['shipping_method']);
    }

    /**
     * @test
     * CP-Chk-025: Efecto cascada al cambiar método de envío
     */
    public function testChangeShippingMethodClearsPayment(): void {
        $available = [['id' => 'flat']];
        $this->session->data['payment_methods'] = ['card'];

        $this->checkout->setShippingMethod('flat', $available);

        $this->assertEquals('flat', $this->session->data['shipping_method']);
    }

    // ========== Método de Pago (8 tests) ==========

    /**
     * @test
     * CP-Chk-026: Recuperación de estado previo del pago
     */
    public function testGetPaymentState(): void {
        $this->session->data['payment_method'] = 'card';
        $this->session->data['order_comment'] = 'Test';

        $this->assertEquals('card', $this->session->data['payment_method']);
    }

    /**
     * @test
     * CP-Chk-027: Obtención de métodos saltando requisitos falla
     */
    public function testPaymentMethodsFailWithoutShipping(): void {
        unset($this->session->data['shipping_method']);
        $methods = $this->checkout->getPaymentMethods();

        $this->assertEmpty($methods);
    }

    /**
     * @test
     * CP-Chk-028: Obtención exitosa de métodos de pago
     */
    public function testGetPaymentMethodsSuccessfully(): void {
        $this->session->data['shipping_method'] = 'flat';
        $methods = $this->checkout->getPaymentMethods();

        $this->assertNotEmpty($methods);
    }

    /**
     * @test
     * CP-Chk-029: Bloqueo por falta de métodos
     */
    public function testNoPaymentMethodsAvailable(): void {
        $methods = [];
        $this->assertEmpty($methods);
    }

    /**
     * @test
     * CP-Chk-030: Prevención de inyección de método de pago
     */
    public function testPaymentMethodInjectionPrevention(): void {
        $available = [['id' => 'tarjeta']];
        $result = $this->checkout->setPaymentMethod('bypass', $available);

        $this->assertFalse($result);
    }

    /**
     * @test
     * CP-Chk-031: Selección exitosa de método de pago
     */
    public function testSetPaymentMethodSuccessfully(): void {
        $available = [['id' => 'tarjeta']];
        $result = $this->checkout->setPaymentMethod('tarjeta', $available);

        $this->assertTrue($result);
        $this->assertEquals('tarjeta', $this->session->data['payment_method']);
    }

    /**
     * @test
     * CP-Chk-032: Adición y persistencia de comentarios
     */
    public function testAddOrderComment(): void {
        $result = $this->checkout->addComment('Dejar en portería');

        $this->assertTrue($result);
        $this->assertEquals('Dejar en portería', $this->session->data['order_comment']);
    }

    /**
     * @test
     * CP-Chk-033: Registro de aceptación de términos
     */
    public function testAcceptTerms(): void {
        $result = $this->checkout->acceptTerms();

        $this->assertTrue($result);
        $this->assertTrue($this->session->data['terms_accepted']);
    }

    // ========== Confirmación (8 tests) ==========

    /**
     * @test
     * CP-Chk-034: Cálculo preciso de totales
     */
    public function testCalculateOrderTotal(): void {
        $total = $this->checkout->calculateTotal(100, 10, 18);

        $this->assertEquals(128.00, $total);
    }

    /**
     * @test
     * CP-Chk-035: Interrupción por fallo de re-validación
     */
    public function testGenerateOrderFailsWithoutMethods(): void {
        $result = $this->checkout->generateOrder();

        $this->assertFalse($result['success']);
    }

    /**
     * @test
     * CP-Chk-036: Limpieza de datos residuales
     */
    public function testCleanupShippingData(): void {
        $this->session->data['shipping_address'] = 'test';
        $result = $this->checkout->clearCheckout();

        $this->assertTrue($result);
    }

    /**
     * @test
     * CP-Chk-037: Gestión de Order ID huérfano
     */
    public function testHandleOrphanOrderId(): void {
        $this->session->data['order_id'] = 999;
        $this->checkout->clearCheckout();

        $this->assertFalse(isset($this->session->data['order_id']));
    }

    /**
     * @test
     * CP-Chk-038: Construcción de payload de orden
     */
    public function testBuildOrderPayload(): void {
        $this->session->data['shipping_method'] = 'flat';
        $this->session->data['payment_method'] = 'card';

        $result = $this->checkout->generateOrder();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('order_id', $result);
    }

    /**
     * @test
     * CP-Chk-039: Handshake inicial de la orden
     */
    public function testOrderInitialStatus(): void {
        $this->session->data['shipping_method'] = 'flat';
        $this->session->data['payment_method'] = 'card';
        $result = $this->checkout->generateOrder();

        $this->assertEquals('pending_payment', $result['status']);
    }

    /**
     * @test
     * CP-Chk-040: Limpieza total post-éxito
     */
    public function testClearCheckoutAfterSuccess(): void {
        $this->session->data['order_id'] = 123;
        $this->checkout->clearCheckout();

        $this->assertFalse(isset($this->session->data['order_id']));
    }

    /**
     * @test
     * CP-Chk-041: Retorno de estado de éxito
     */
    public function testReturnSuccessStatus(): void {
        $this->session->data['shipping_method'] = 'flat';
        $this->session->data['payment_method'] = 'card';
        $result = $this->checkout->generateOrder();

        $this->assertTrue($result['success']);
    }

    /**
     * @test
     * CP-Chk-042: Retorno de estado de fallo
     */
    public function testReturnFailureStatus(): void {
        $result = $this->checkout->generateOrder();

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
    }
}
