<?php
namespace Opencart\System\Library;

/**
 * Class CartManager
 *
 * Gestiona las operaciones del carrito de compras en OpenCart.
 * Incluye agregar, actualizar, eliminar productos y cálculo de totales.
 *
 * @package Opencart\System\Library
 */
class CartManager {
    /**
     * @var \Opencart\System\Engine\Registry
     */
    private $registry;

    /**
     * @var \Opencart\System\Engine\Db
     */
    private $db;

    /**
     * @var \Opencart\System\Engine\Config
     */
    private $config;

    /**
     * @var array
     */
    private $products = [];

    /**
     * Constructor
     *
     * @param \Opencart\System\Engine\Registry $registry
     */
    public function __construct(\Opencart\System\Engine\Registry $registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    /**
     * addProduct
     *
     * Agrega un producto al carrito.
     *
     * @param int $product_id
     * @param int $quantity
     * @param array $options
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function addProduct(int $product_id, int $quantity, array $options = []): array {
        // Validar producto existe
        $product = $this->getProduct($product_id);

        if (empty($product)) {
            return [
                'success' => false,
                'error' => 'Producto no encontrado'
            ];
        }

        // Validar stock
        if ((int)$product['quantity'] < $quantity) {
            return [
                'success' => false,
                'error' => 'Stock insuficiente'
            ];
        }

        // Agregar producto
        $key = $this->getProductKey($product_id, $options);

        if (isset($this->products[$key])) {
            $this->products[$key]['quantity'] += $quantity;
        } else {
            $this->products[$key] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'image' => $product['image'] ?? 'placeholder.png',
                'options' => $options
            ];
        }

        return ['success' => true];
    }

    /**
     * removeProduct
     *
     * Elimina un producto del carrito.
     *
     * @param int $product_id
     * @return array ['success' => bool]
     */
    public function removeProduct(int $product_id): array {
        foreach ($this->products as $key => $product) {
            if ((int)$product['product_id'] === $product_id) {
                unset($this->products[$key]);
                return ['success' => true];
            }
        }

        return ['success' => false];
    }

    /**
     * updateProductQuantity
     *
     * Actualiza la cantidad de un producto en el carrito.
     *
     * @param int $product_id
     * @param int $quantity
     * @return array ['success' => bool]
     */
    public function updateProductQuantity(int $product_id, int $quantity): array {
        foreach ($this->products as $key => $product) {
            if ((int)$product['product_id'] === $product_id) {
                if ($quantity <= 0) {
                    unset($this->products[$key]);
                } else {
                    $this->products[$key]['quantity'] = $quantity;
                }
                return ['success' => true];
            }
        }

        return ['success' => false];
    }

    /**
     * getProducts
     *
     * Obtiene todos los productos en el carrito.
     *
     * @return array
     */
    public function getProducts(): array {
        return array_values($this->products);
    }

    /**
     * setProducts
     *
     * Establece la lista de productos en el carrito.
     *
     * @param array $products
     */
    public function setProducts(array $products): void {
        $this->products = [];
        foreach ($products as $product) {
            $key = md5(json_encode($product));
            $this->products[$key] = $product;
        }
    }

    /**
     * getProduct
     *
     * Obtiene los datos de un producto de la BD.
     *
     * @param int $product_id
     * @return array
     */
    private function getProduct(int $product_id): array {
        $query = $this->db->query("
            SELECT `product_id`, `name`, `price`, `quantity`, `image`
            FROM `" . DB_PREFIX . "product`
            WHERE `product_id` = '" . (int)$product_id . "'
            LIMIT 1
        ");

        if ($query->num_rows) {
            return $query->row;
        }

        return [];
    }

    /**
     * getProductKey
     *
     * Genera una clave única para un producto + opciones.
     *
     * @param int $product_id
     * @param array $options
     * @return string
     */
    private function getProductKey(int $product_id, array $options = []): string {
        $data = [
            'product_id' => $product_id,
            'options' => $options
        ];

        return md5(json_encode($data));
    }

    /**
     * calculateTotals
     *
     * Calcula los totales del carrito (subtotal, impuestos, total).
     *
     * @return array ['subtotal' => float, 'tax' => float, 'total' => float]
     */
    public function calculateTotals(): array {
        $subtotal = 0;
        $tax = 0;

        foreach ($this->products as $product) {
            $subtotal += ($product['price'] * $product['quantity']);

            // Simular cálculo de impuestos (16% por defecto)
            $taxRate = $product['tax_rate'] ?? 0.16;
            $tax += ($product['price'] * $product['quantity'] * $taxRate);
        }

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax
        ];
    }

    /**
     * getSubtotal
     *
     * Obtiene el subtotal del carrito.
     *
     * @return float
     */
    public function getSubtotal(): float {
        $subtotal = 0;

        foreach ($this->products as $product) {
            $subtotal += ($product['price'] * $product['quantity']);
        }

        return $subtotal;
    }

    /**
     * isCheckoutAvailable
     *
     * Verifica si el checkout está disponible (carrito no vacío).
     *
     * @return bool
     */
    public function isCheckoutAvailable(): bool {
        return !empty($this->products);
    }

    /**
     * getContinueShoppingUrl
     *
     * Obtiene el URL para continuar comprando.
     *
     * @return string
     */
    public function getContinueShoppingUrl(): string {
        return 'index.php?route=common/home';
    }

    /**
     * getProductStockStatus
     *
     * Obtiene el estado del stock de un producto.
     *
     * @param array $product
     * @return string
     */
    public function getProductStockStatus(array $product): string {
        if ((int)$product['quantity'] > 0) {
            return 'in_stock';
        }

        return 'out_of_stock';
    }

    /**
     * getAndClearErrorMessage
     *
     * Obtiene y limpia el mensaje de error de la sesión.
     *
     * @return string|null
     */
    public function getAndClearErrorMessage(): ?string {
        $session = $this->registry->get('session');
        $error = $session->data['error'] ?? null;

        if ($error) {
            unset($session->data['error']);
        }

        return $error;
    }

    /**
     * getAndClearSuccessMessage
     *
     * Obtiene y limpia el mensaje de éxito de la sesión.
     *
     * @return string|null
     */
    public function getAndClearSuccessMessage(): ?string {
        $session = $this->registry->get('session');
        $success = $session->data['success'] ?? null;

        if ($success) {
            unset($session->data['success']);
        }

        return $success;
    }

    /**
     * getCartItemCount
     *
     * Obtiene el número total de artículos en el carrito.
     *
     * @return int
     */
    public function getCartItemCount(): int {
        $count = 0;

        foreach ($this->products as $product) {
            $count += (int)$product['quantity'];
        }

        return $count;
    }

    /**
     * clear
     *
     * Vacía completamente el carrito.
     */
    public function clear(): void {
        $this->products = [];
    }
}
