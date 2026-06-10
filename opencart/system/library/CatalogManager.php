<?php
namespace Opencart\System\Library;

/**
 * Class CatalogManager
 *
 * Gestiona catálogo, búsqueda, filtrado y detalles de productos.
 *
 * @package Opencart\System\Library
 */
class CatalogManager {
    private $registry;
    private $db;
    private $config;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    public function getProducts(array $filter = []): array {
        return $this->mockProducts();
    }

    public function getProductById(int $id): array {
        $products = $this->mockProducts();
        foreach ($products as $p) {
            if ($p['product_id'] == $id) return $p;
        }
        return [];
    }

    public function searchProducts(string $query): array {
        $results = [];
        $products = $this->mockProducts();
        foreach ($products as $p) {
            if (stripos($p['name'], $query) !== false) {
                $results[] = $p;
            }
        }
        return $results;
    }

    public function getCategories(): array {
        return [
            ['id' => 1, 'name' => 'Electrónica', 'status' => 1],
            ['id' => 2, 'name' => 'Ropa', 'status' => 1],
            ['id' => 3, 'name' => 'Libros', 'status' => 0]
        ];
    }

    public function getCategoryProducts(int $categoryId): array {
        return $this->mockProducts();
    }

    public function filterByPrice(array $products, float $min, float $max): array {
        return array_filter($products, function($p) use ($min, $max) {
            return $p['price'] >= $min && $p['price'] <= $max;
        });
    }

    public function sortProducts(array $products, string $by = 'name'): array {
        usort($products, function($a, $b) use ($by) {
            if ($by === 'price') {
                return $a['price'] <=> $b['price'];
            }
            return strcmp($a['name'], $b['name']);
        });
        return $products;
    }

    public function paginate(array $items, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return array_slice($items, $offset, $perPage);
    }

    public function isInStock(int $productId): bool {
        $product = $this->getProductById($productId);
        return isset($product['quantity']) && $product['quantity'] > 0;
    }

    public function getStockStatus(int $productId): string {
        if ($this->isInStock($productId)) {
            return 'in_stock';
        }
        return 'out_of_stock';
    }

    public function getPrice(int $productId): float {
        $product = $this->getProductById($productId);
        return $product['price'] ?? 0;
    }

    public function getDiscountedPrice(int $productId, float $discount = 0): float {
        $price = $this->getPrice($productId);
        return $price * (1 - $discount / 100);
    }

    public function getProductRating(int $productId): float {
        return 4.5;
    }

    public function countReviews(int $productId): int {
        return 10;
    }

    public function getRelatedProducts(int $productId): array {
        return array_slice($this->mockProducts(), 0, 3);
    }

    public function addToWishlist(int $customerId, int $productId): bool {
        return true;
    }

    public function removeFromWishlist(int $customerId, int $productId): bool {
        return true;
    }

    public function isInWishlist(int $customerId, int $productId): bool {
        return false;
    }

    private function mockProducts(): array {
        return [
            ['product_id' => 1, 'name' => 'Laptop', 'price' => 1000, 'quantity' => 5, 'status' => 1],
            ['product_id' => 2, 'name' => 'Mouse', 'price' => 25, 'quantity' => 0, 'status' => 1],
            ['product_id' => 3, 'name' => 'Monitor', 'price' => 300, 'quantity' => 10, 'status' => 1],
            ['product_id' => 4, 'name' => 'Teclado', 'price' => 75, 'quantity' => 8, 'status' => 0]
        ];
    }
}
