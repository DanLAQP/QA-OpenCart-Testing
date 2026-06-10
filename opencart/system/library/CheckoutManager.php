<?php
namespace Opencart\System\Library;

/**
 * Class CheckoutManager
 *
 * Gestiona el flujo completo de checkout incluyendo direcciones,
 * métodos de envío, métodos de pago y confirmación de órdenes.
 *
 * @package Opencart\System\Library
 */
class CheckoutManager {
    /**
     * @var \Opencart\System\Engine\Registry
     */
    private $registry;

    /**
     * @var \Opencart\System\Engine\Db
     */
    private $db;

    /**
     * @var \Opencart\System\Engine\Session
     */
    private $session;

    /**
     * Constructor
     */
    public function __construct(\Opencart\System\Engine\Registry $registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->session = $registry->get('session');
    }

    /**
     * initCheckout
     *
     * Inicializa el estado del checkout.
     *
     * @param array $cart
     * @param bool $isAuthenticated
     * @return array
     */
    public function initCheckout(array $cart, bool $isAuthenticated): array {
        return [
            'status' => !empty($cart['items']),
            'steps' => ['billing', 'shipping', 'payment', 'confirm'],
            'current_step' => 'billing',
            'authenticated' => $isAuthenticated,
            'cart_valid' => $this->isCartValid($cart)
        ];
    }

    /**
     * isCartValid
     *
     * Valida que el carrito sea válido.
     *
     * @param array $cart
     * @return bool
     */
    public function isCartValid(array $cart): bool {
        if (empty($cart['items'])) {
            return false;
        }

        foreach ($cart['items'] as $item) {
            if (!isset($item['price']) || $item['price'] < 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * validateStock
     *
     * Valida el stock de los artículos en el carrito.
     *
     * @param array $cartItems
     * @param bool $allowBackorder
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateStock(array $cartItems, bool $allowBackorder = false): array {
        foreach ($cartItems as $item) {
            if ($item['stock'] <= 0 && !$allowBackorder) {
                return [
                    'valid' => false,
                    'message' => 'Stock insuficiente'
                ];
            }
        }

        return ['valid' => true, 'message' => 'Stock disponible'];
    }

    /**
     * validateMinimumQuantity
     *
     * Valida cantidad mínima en carrito.
     *
     * @param int $qty
     * @param int $minimum
     * @return array
     */
    public function validateMinimumQuantity(int $qty, int $minimum): array {
        if ($qty < $minimum) {
            return [
                'valid' => false,
                'message' => "Cantidad mínima requerida: $minimum"
            ];
        }

        return ['valid' => true, 'message' => 'Cantidad válida'];
    }

    /**
     * setBillingAddress
     *
     * Establece la dirección de facturación.
     *
     * @param int $addressId
     * @return bool
     */
    public function setBillingAddress(int $addressId): bool {
        $this->session->data['billing_address_id'] = $addressId;
        return true;
    }

    /**
     * validateBillingAddress
     *
     * Valida dirección de facturación.
     *
     * @param array $address
     * @return array
     */
    public function validateBillingAddress(array $address): array {
        if (empty($address['firstname']) || empty($address['lastname']) ||
            empty($address['city']) || empty($address['address'])) {
            return [
                'valid' => false,
                'message' => 'Campos obligatorios incompletos'
            ];
        }

        return ['valid' => true, 'message' => 'Dirección válida'];
    }

    /**
     * setShippingAddress
     *
     * Establece la dirección de envío.
     *
     * @param int $addressId
     * @return bool
     */
    public function setShippingAddress(int $addressId): bool {
        $this->session->data['shipping_address_id'] = $addressId;
        return true;
    }

    /**
     * validateShippingAddress
     *
     * Valida dirección de envío.
     *
     * @param array $address
     * @return array
     */
    public function validateShippingAddress(array $address): array {
        if (empty($address['firstname']) || empty($address['lastname']) ||
            empty($address['city']) || empty($address['address'])) {
            return [
                'valid' => false,
                'message' => 'Campos de envío obligatorios'
            ];
        }

        return ['valid' => true, 'message' => 'Dirección de envío válida'];
    }

    /**
     * getShippingMethods
     *
     * Obtiene métodos de envío disponibles.
     *
     * @return array
     */
    public function getShippingMethods(): array {
        if (empty($this->session->data['shipping_address_id'])) {
            return [];
        }

        return [
            ['id' => 'flat', 'name' => 'Tarifa plana', 'cost' => 10],
            ['id' => 'standard', 'name' => 'Envío estándar', 'cost' => 5],
            ['id' => 'express', 'name' => 'Envío express', 'cost' => 20]
        ];
    }

    /**
     * setShippingMethod
     *
     * Establece el método de envío.
     *
     * @param string $method
     * @param array $availableMethods
     * @return bool
     */
    public function setShippingMethod(string $method, array $availableMethods): bool {
        $methodIds = array_column($availableMethods, 'id');

        if (!in_array($method, $methodIds)) {
            return false;
        }

        $this->session->data['shipping_method'] = $method;
        return true;
    }

    /**
     * getPaymentMethods
     *
     * Obtiene métodos de pago disponibles.
     *
     * @return array
     */
    public function getPaymentMethods(): array {
        if (empty($this->session->data['shipping_method'])) {
            return [];
        }

        return [
            ['id' => 'tarjeta', 'name' => 'Tarjeta de crédito'],
            ['id' => 'transferencia', 'name' => 'Transferencia bancaria'],
            ['id' => 'paypal', 'name' => 'PayPal']
        ];
    }

    /**
     * setPaymentMethod
     *
     * Establece el método de pago.
     *
     * @param string $method
     * @param array $availableMethods
     * @return bool
     */
    public function setPaymentMethod(string $method, array $availableMethods): bool {
        $methodIds = array_column($availableMethods, 'id');

        if (!in_array($method, $methodIds)) {
            return false;
        }

        $this->session->data['payment_method'] = $method;
        return true;
    }

    /**
     * addComment
     *
     * Agrega comentario a la orden.
     *
     * @param string $comment
     * @return bool
     */
    public function addComment(string $comment): bool {
        $this->session->data['order_comment'] = $comment;
        return true;
    }

    /**
     * acceptTerms
     *
     * Marca los términos como aceptados.
     *
     * @return bool
     */
    public function acceptTerms(): bool {
        $this->session->data['terms_accepted'] = true;
        return true;
    }

    /**
     * calculateTotal
     *
     * Calcula el total de la orden.
     *
     * @param float $subtotal
     * @param float $shipping
     * @param float $taxRate
     * @return float
     */
    public function calculateTotal(float $subtotal, float $shipping, float $taxRate): float {
        $tax = $subtotal * ($taxRate / 100);
        return round($subtotal + $shipping + $tax, 2);
    }

    /**
     * generateOrder
     *
     * Genera una nueva orden.
     *
     * @return array
     */
    public function generateOrder(): array {
        if (empty($this->session->data['shipping_method']) ||
            empty($this->session->data['payment_method'])) {
            return [
                'success' => false,
                'errors' => ['Métodos de envío y pago requeridos']
            ];
        }

        $orderId = time();
        $this->session->data['order_id'] = $orderId;

        return [
            'success' => true,
            'order_id' => $orderId,
            'status' => 'pending_payment'
        ];
    }

    /**
     * clearCheckout
     *
     * Limpia los datos de checkout tras completar la orden.
     *
     * @return bool
     */
    public function clearCheckout(): bool {
        unset(
            $this->session->data['billing_address_id'],
            $this->session->data['shipping_address_id'],
            $this->session->data['shipping_method'],
            $this->session->data['payment_method'],
            $this->session->data['order_comment'],
            $this->session->data['terms_accepted'],
            $this->session->data['order_id']
        );

        return true;
    }

    /**
     * getOrderStatus
     *
     * Obtiene el estado de una orden.
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderStatus(int $orderId): string {
        return 'pending_payment';
    }
}
