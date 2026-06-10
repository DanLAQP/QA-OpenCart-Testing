<?php
namespace Opencart\System\Library;

/**
 * Class InventoryManager
 *
 * Gestiona la lógica de inventario/stock de productos en OpenCart.
 * Maneja validaciones de stock, opciones, variantes y disponibilidad.
 *
 * @package Opencart\System\Library
 */
class InventoryManager {
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
     * validateProductQuantity
     *
     * Valida que la cantidad solicitada esté disponible en stock.
     *
     * @param int $product_id
     * @param int $quantity Cantidad solicitada
     * @return array ['valid' => bool, 'message' => string, 'available' => int]
     */
    public function validateProductQuantity(int $product_id, int $quantity): array {
        $product = $this->getProductStock($product_id);

        if (empty($product)) {
            return [
                'valid' => false,
                'message' => 'Producto no encontrado',
                'available' => 0
            ];
        }

        $availableQuantity = (int)$product['quantity'];

        // Verificar si está permitido vender sin stock
        $allow_out_of_stock = $this->config->get('config_allow_out_of_stock') ?? false;

        if (!$allow_out_of_stock && $availableQuantity < $quantity) {
            return [
                'valid' => false,
                'message' => 'Cantidad insuficiente en stock',
                'available' => $availableQuantity
            ];
        }

        return [
            'valid' => true,
            'message' => 'Stock disponible',
            'available' => $availableQuantity
        ];
    }

    /**
     * validateProductMinimum
     *
     * Valida que la cantidad cumple con el mínimo requerido.
     *
     * @param int $product_id
     * @param int $quantity
     * @return array ['valid' => bool, 'message' => string, 'minimum' => int]
     */
    public function validateProductMinimum(int $product_id, int $quantity): array {
        $product = $this->getProductStock($product_id);

        if (empty($product)) {
            return [
                'valid' => false,
                'message' => 'Producto no encontrado',
                'minimum' => 0
            ];
        }

        $minimum = (int)$product['minimum'];

        if ($minimum > 0 && $quantity < $minimum) {
            return [
                'valid' => false,
                'message' => sprintf('Cantidad mínima requerida: %d', $minimum),
                'minimum' => $minimum
            ];
        }

        return [
            'valid' => true,
            'message' => 'Cantidad válida',
            'minimum' => $minimum
        ];
    }

    /**
     * isProductAvailable
     *
     * Determina si un producto está disponible para compra.
     *
     * @param int $product_id
     * @return bool
     */
    public function isProductAvailable(int $product_id): bool {
        $product = $this->getProductStock($product_id);

        if (empty($product)) {
            return false;
        }

        // Verificar estado del producto
        if ((int)$product['status'] !== 1) {
            return false;
        }

        // Verificar fecha de disponibilidad
        if ($product['date_available'] && $product['date_available'] > date('Y-m-d')) {
            return false;
        }

        // Verificar stock
        $allow_out_of_stock = $this->config->get('config_allow_out_of_stock') ?? false;
        if (!$allow_out_of_stock && (int)$product['quantity'] === 0) {
            return false;
        }

        return true;
    }

    /**
     * getProductStock
     *
     * Obtiene la información de stock de un producto.
     *
     * @param int $product_id
     * @return array
     */
    public function getProductStock(int $product_id): array {
        $query = $this->db->query("
            SELECT `product_id`, `quantity`, `minimum`, `status`, `date_available`
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
     * updateProductQuantity
     *
     * Actualiza la cantidad de stock de un producto.
     *
     * @param int $product_id
     * @param int $quantity
     * @return bool
     */
    public function updateProductQuantity(int $product_id, int $quantity): bool {
        try {
            $this->db->query("
                UPDATE `" . DB_PREFIX . "product`
                SET `quantity` = '" . (int)$quantity . "'
                WHERE `product_id` = '" . (int)$product_id . "'
            ");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * decreaseProductQuantity
     *
     * Disminuye la cantidad de stock de un producto.
     *
     * @param int $product_id
     * @param int $quantity Cantidad a restar
     * @return bool
     */
    public function decreaseProductQuantity(int $product_id, int $quantity): bool {
        return $this->updateProductQuantity($product_id, max(0, $this->getProductQuantity($product_id) - $quantity));
    }

    /**
     * getProductQuantity
     *
     * Obtiene la cantidad actual de un producto.
     *
     * @param int $product_id
     * @return int
     */
    public function getProductQuantity(int $product_id): int {
        $product = $this->getProductStock($product_id);
        return isset($product['quantity']) ? (int)$product['quantity'] : 0;
    }

    /**
     * getProductStatus
     *
     * Obtiene el estado de un producto.
     *
     * @param int $product_id
     * @return int
     */
    public function getProductStatus(int $product_id): int {
        $product = $this->getProductStock($product_id);
        return isset($product['status']) ? (int)$product['status'] : 0;
    }

    /**
     * validateOptionQuantity
     *
     * Valida el stock de una opción específica.
     *
     * @param int $product_option_id
     * @param int $product_option_value_id
     * @param int $quantity
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateOptionQuantity(int $product_option_id, int $product_option_value_id, int $quantity): array {
        $query = $this->db->query("
            SELECT `quantity`
            FROM `" . DB_PREFIX . "product_option_value`
            WHERE `product_option_id` = '" . (int)$product_option_id . "'
            AND `product_option_value_id` = '" . (int)$product_option_value_id . "'
        ");

        if (!$query->num_rows) {
            return [
                'valid' => false,
                'message' => 'Opción no encontrada'
            ];
        }

        $optionQuantity = (int)$query->row['quantity'];

        if ($optionQuantity < $quantity) {
            return [
                'valid' => false,
                'message' => 'Stock insuficiente para la opción seleccionada'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Stock disponible para la opción'
        ];
    }

    /**
     * isProductMaster
     *
     * Determina si un producto es maestro (tiene variantes).
     *
     * @param int $product_id
     * @return bool
     */
    public function isProductMaster(int $product_id): bool {
        $query = $this->db->query("
            SELECT COUNT(*) as count
            FROM `" . DB_PREFIX . "product_variant`
            WHERE `master_id` = '" . (int)$product_id . "'
        ");

        return $query->row['count'] > 0;
    }

    /**
     * getProductVariants
     *
     * Obtiene todas las variantes de un producto maestro.
     *
     * @param int $product_id
     * @return array
     */
    public function getProductVariants(int $product_id): array {
        $query = $this->db->query("
            SELECT *
            FROM `" . DB_PREFIX . "product_variant`
            WHERE `master_id` = '" . (int)$product_id . "'
        ");

        return $query->rows ?? [];
    }

    /**
     * getStockStatus
     *
     * Obtiene el estado de disponibilidad de un producto.
     *
     * @param int $product_id
     * @return string
     */
    public function getStockStatus(int $product_id): string {
        $product = $this->getProductStock($product_id);

        if (empty($product)) {
            return 'unknown';
        }

        if ((int)$product['quantity'] > 0) {
            return 'in_stock';
        }

        return 'out_of_stock';
    }

    /**
     * validateCheckoutQuantity
     *
     * Valida la cantidad total en carrito para checkout.
     *
     * @param int $product_id
     * @param int $cartQuantity
     * @param int $currentStock
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateCheckoutQuantity(int $product_id, int $cartQuantity, int $currentStock = null): array {
        if ($currentStock === null) {
            $currentStock = $this->getProductQuantity($product_id);
        }

        if ($cartQuantity > $currentStock) {
            return [
                'valid' => false,
                'message' => 'Stock ha cambiado, cantidad insuficiente para completar la compra'
            ];
        }

        // Validar mínimo
        $minimumValidation = $this->validateProductMinimum($product_id, $cartQuantity);

        if (!$minimumValidation['valid']) {
            return $minimumValidation;
        }

        return [
            'valid' => true,
            'message' => 'Cantidad válida para checkout'
        ];
    }
}
