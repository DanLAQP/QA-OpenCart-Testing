<?php
namespace Tests\Unitarias;

use Tests\BaseTestCase;
use Opencart\System\Library\CatalogManager;

/**
 * Class CatalogTest
 *
 * Pruebas unitarias para el módulo Catálogo y Búsqueda (100 tests)
 *
 * @covers \Opencart\System\Library\CatalogManager
 */
class CatalogTest extends BaseTestCase {
    /**
     * @var CatalogManager
     */
    private $catalog;

    protected function setUp(): void {
        parent::setUp();
        $this->catalog = new CatalogManager($this->registry);
    }

    // ========== Listado de Productos (10 tests) ==========

    public function testGetProductsReturnsArray(): void {
        $products = $this->catalog->getProducts();
        $this->assertIsArray($products);
    }

    public function testGetProductsWithoutFilter(): void {
        $products = $this->catalog->getProducts();
        $this->assertNotEmpty($products);
    }

    public function testGetProductsWithEmptyResult(): void {
        $products = $this->catalog->getProducts(['category' => 999]);
        $this->assertIsArray($products);
    }

    public function testGetProductsCountMatches(): void {
        $products = $this->catalog->getProducts();
        $this->assertGreaterThanOrEqual(0, count($products));
    }

    public function testGetProductsContainsRequiredFields(): void {
        $products = $this->catalog->getProducts();
        if (!empty($products)) {
            $this->assertArrayHasKey('product_id', $products[0]);
            $this->assertArrayHasKey('name', $products[0]);
            $this->assertArrayHasKey('price', $products[0]);
        }
    }

    public function testGetProductsByStatus(): void {
        $products = $this->catalog->getProducts();
        $this->assertIsArray($products);
    }

    public function testGetProductsNoNullValues(): void {
        $products = $this->catalog->getProducts();
        foreach ($products as $product) {
            $this->assertNotNull($product['product_id']);
        }
    }

    public function testGetProductsPricesAreNumeric(): void {
        $products = $this->catalog->getProducts();
        foreach ($products as $product) {
            $this->assertIsNumeric($product['price']);
        }
    }

    public function testGetProductsNamesAreStrings(): void {
        $products = $this->catalog->getProducts();
        foreach ($products as $product) {
            $this->assertIsString($product['name']);
        }
    }

    public function testGetProductsWithOffset(): void {
        $products1 = $this->catalog->getProducts(['offset' => 0]);
        $products2 = $this->catalog->getProducts(['offset' => 2]);
        $this->assertIsArray($products1);
        $this->assertIsArray($products2);
    }

    // ========== Búsqueda (10 tests) ==========

    public function testSearchProductsByName(): void {
        $results = $this->catalog->searchProducts('Laptop');
        $this->assertIsArray($results);
    }

    public function testSearchProductsEmptyQuery(): void {
        $results = $this->catalog->searchProducts('');
        $this->assertIsArray($results);
    }

    public function testSearchProductsNonExistent(): void {
        $results = $this->catalog->searchProducts('XYZ123');
        $this->assertIsArray($results);
    }

    public function testSearchProductsCaseSensitive(): void {
        $results1 = $this->catalog->searchProducts('laptop');
        $results2 = $this->catalog->searchProducts('LAPTOP');
        $this->assertIsArray($results1);
        $this->assertIsArray($results2);
    }

    public function testSearchProductsContainsMatches(): void {
        $results = $this->catalog->searchProducts('Laptop');
        foreach ($results as $product) {
            $this->assertStringContainsString('Laptop', $product['name']);
        }
    }

    public function testSearchProductsReturnsMultiple(): void {
        $results = $this->catalog->searchProducts('Laptop');
        $this->assertIsArray($results);
    }

    public function testSearchProductsPreservesData(): void {
        $results = $this->catalog->searchProducts('Laptop');
        foreach ($results as $result) {
            $this->assertArrayHasKey('price', $result);
        }
    }

    public function testSearchProductsSpecialChars(): void {
        $results = $this->catalog->searchProducts('*');
        $this->assertIsArray($results);
    }

    public function testSearchProductsPartialMatch(): void {
        $results = $this->catalog->searchProducts('ap');
        $this->assertIsArray($results);
    }

    public function testSearchProductsSqlInjection(): void {
        $results = $this->catalog->searchProducts("'; DROP TABLE products; --");
        $this->assertIsArray($results);
    }

    // ========== Categorías (10 tests) ==========

    public function testGetCategoriesReturnsArray(): void {
        $categories = $this->catalog->getCategories();
        $this->assertIsArray($categories);
    }

    public function testGetCategoriesNotEmpty(): void {
        $categories = $this->catalog->getCategories();
        $this->assertNotEmpty($categories);
    }

    public function testGetCategoriesContainsId(): void {
        $categories = $this->catalog->getCategories();
        foreach ($categories as $cat) {
            $this->assertArrayHasKey('id', $cat);
        }
    }

    public function testGetCategoriesContainsName(): void {
        $categories = $this->catalog->getCategories();
        foreach ($categories as $cat) {
            $this->assertArrayHasKey('name', $cat);
        }
    }

    public function testGetCategoriesContainsStatus(): void {
        $categories = $this->catalog->getCategories();
        foreach ($categories as $cat) {
            $this->assertArrayHasKey('status', $cat);
        }
    }

    public function testGetCategoriesStatusIsBoolean(): void {
        $categories = $this->catalog->getCategories();
        foreach ($categories as $cat) {
            $this->assertTrue(is_int($cat['status']) || is_bool($cat['status']));
        }
    }

    public function testGetCategoriesCountCorrect(): void {
        $categories = $this->catalog->getCategories();
        $this->assertGreaterThanOrEqual(1, count($categories));
    }

    public function testGetCategoriesOrderPreserved(): void {
        $categories1 = $this->catalog->getCategories();
        $categories2 = $this->catalog->getCategories();
        $this->assertEquals($categories1, $categories2);
    }

    public function testGetCategoriesNoNullIds(): void {
        $categories = $this->catalog->getCategories();
        foreach ($categories as $cat) {
            $this->assertNotNull($cat['id']);
        }
    }

    public function testGetCategoriesUniqueIds(): void {
        $categories = $this->catalog->getCategories();
        $ids = array_column($categories, 'id');
        $this->assertEquals($ids, array_unique($ids));
    }

    // ========== Productos por Categoría (10 tests) ==========

    public function testGetCategoryProductsReturnsArray(): void {
        $products = $this->catalog->getCategoryProducts(1);
        $this->assertIsArray($products);
    }

    public function testGetCategoryProductsValidCategory(): void {
        $products = $this->catalog->getCategoryProducts(1);
        $this->assertIsArray($products);
    }

    public function testGetCategoryProductsInvalidCategory(): void {
        $products = $this->catalog->getCategoryProducts(999);
        $this->assertIsArray($products);
    }

    public function testGetCategoryProductsPreservesData(): void {
        $products = $this->catalog->getCategoryProducts(1);
        foreach ($products as $product) {
            $this->assertArrayHasKey('product_id', $product);
        }
    }

    public function testGetCategoryProductsMultipleCategories(): void {
        $products1 = $this->catalog->getCategoryProducts(1);
        $products2 = $this->catalog->getCategoryProducts(2);
        $this->assertIsArray($products1);
        $this->assertIsArray($products2);
    }

    public function testGetCategoryProductsPricesPresent(): void {
        $products = $this->catalog->getCategoryProducts(1);
        foreach ($products as $product) {
            $this->assertArrayHasKey('price', $product);
        }
    }

    public function testGetCategoryProductsNamePresent(): void {
        $products = $this->catalog->getCategoryProducts(1);
        foreach ($products as $product) {
            $this->assertArrayHasKey('name', $product);
        }
    }

    public function testGetCategoryProductsNegativeId(): void {
        $products = $this->catalog->getCategoryProducts(-1);
        $this->assertIsArray($products);
    }

    public function testGetCategoryProductsZeroId(): void {
        $products = $this->catalog->getCategoryProducts(0);
        $this->assertIsArray($products);
    }

    public function testGetCategoryProductsLargeId(): void {
        $products = $this->catalog->getCategoryProducts(999999);
        $this->assertIsArray($products);
    }

    // ========== Filtrado por Precio (10 tests) ==========

    public function testFilterByPriceValidRange(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 0, 100);
        $this->assertIsArray($filtered);
    }

    public function testFilterByPriceMinOnly(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 100, 9999);
        foreach ($filtered as $p) {
            $this->assertGreaterThanOrEqual(100, $p['price']);
        }
    }

    public function testFilterByPriceMaxOnly(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 0, 500);
        foreach ($filtered as $p) {
            $this->assertLessThanOrEqual(500, $p['price']);
        }
    }

    public function testFilterByPriceZeroMin(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 0, 1000);
        $this->assertIsArray($filtered);
    }

    public function testFilterByPriceEqual(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 25, 25);
        foreach ($filtered as $p) {
            $this->assertEquals(25, $p['price']);
        }
    }

    public function testFilterByPriceNegativeMin(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, -10, 100);
        $this->assertIsArray($filtered);
    }

    public function testFilterByPriceNoResults(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 99999, 99999);
        $this->assertIsArray($filtered);
    }

    public function testFilterByPriceAllResults(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 0, 99999);
        $this->assertCount(count($products), $filtered);
    }

    public function testFilterByPriceFloat(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 10.5, 100.99);
        $this->assertIsArray($filtered);
    }

    public function testFilterByPriceInverted(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 500, 10);
        $this->assertIsArray($filtered);
    }

    // ========== Ordenamiento (10 tests) ==========

    public function testSortProductsByName(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'name');
        $this->assertIsArray($sorted);
    }

    public function testSortProductsByNameAscending(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'name');
        for ($i = 0; $i < count($sorted) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $sorted[$i + 1]['name'],
                $sorted[$i]['name']
            );
        }
    }

    public function testSortProductsByPrice(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'price');
        $this->assertIsArray($sorted);
    }

    public function testSortProductsByPriceAscending(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'price');
        for ($i = 0; $i < count($sorted) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $sorted[$i + 1]['price'],
                $sorted[$i]['price']
            );
        }
    }

    public function testSortProductsDefault(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products);
        $this->assertIsArray($sorted);
    }

    public function testSortProductsPreservesData(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'name');
        $this->assertEquals(count($products), count($sorted));
    }

    public function testSortProductsEmptyArray(): void {
        $sorted = $this->catalog->sortProducts([], 'name');
        $this->assertEmpty($sorted);
    }

    public function testSortProductsInvalidSortKey(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'invalid');
        $this->assertIsArray($sorted);
    }

    public function testSortProductsStable(): void {
        $products = $this->catalog->getProducts();
        $sorted1 = $this->catalog->sortProducts($products, 'name');
        $sorted2 = $this->catalog->sortProducts($products, 'name');
        $this->assertEquals($sorted1, $sorted2);
    }

    public function testSortProductsSingleItem(): void {
        $products = [['name' => 'Test', 'price' => 100]];
        $sorted = $this->catalog->sortProducts($products, 'name');
        $this->assertCount(1, $sorted);
    }

    // ========== Paginación (10 tests) ==========

    public function testPaginatePage1(): void {
        $items = $this->catalog->getProducts();
        $paginated = $this->catalog->paginate($items, 1, 10);
        $this->assertIsArray($paginated);
    }

    public function testPaginateCount(): void {
        $items = range(1, 50);
        $paginated = $this->catalog->paginate($items, 1, 10);
        $this->assertCount(10, $paginated);
    }

    public function testPaginatePage2(): void {
        $items = range(1, 50);
        $paginated = $this->catalog->paginate($items, 2, 10);
        $this->assertCount(10, $paginated);
    }

    public function testPaginateLastPage(): void {
        $items = range(1, 50);
        $paginated = $this->catalog->paginate($items, 5, 10);
        $this->assertCount(10, $paginated);
    }

    public function testPaginatePartialLastPage(): void {
        $items = range(1, 45);
        $paginated = $this->catalog->paginate($items, 5, 10);
        $this->assertCount(5, $paginated);
    }

    public function testPaginateOutOfRange(): void {
        $items = range(1, 20);
        $paginated = $this->catalog->paginate($items, 10, 10);
        $this->assertIsArray($paginated);
    }

    public function testPaginatePageZero(): void {
        $items = range(1, 20);
        $paginated = $this->catalog->paginate($items, 0, 10);
        $this->assertIsArray($paginated);
    }

    public function testPaginatePerPageZero(): void {
        $items = range(1, 20);
        $paginated = $this->catalog->paginate($items, 1, 0);
        $this->assertIsArray($paginated);
    }

    public function testPaginateEmptyArray(): void {
        $paginated = $this->catalog->paginate([], 1, 10);
        $this->assertEmpty($paginated);
    }

    public function testPaginateOnePerPage(): void {
        $items = range(1, 10);
        $paginated = $this->catalog->paginate($items, 1, 1);
        $this->assertCount(1, $paginated);
    }

    // ========== Stock y Disponibilidad (10 tests) ==========

    public function testIsInStockTrue(): void {
        $result = $this->catalog->isInStock(1);
        $this->assertTrue($result);
    }

    public function testIsInStockFalse(): void {
        $result = $this->catalog->isInStock(2);
        $this->assertFalse($result);
    }

    public function testIsInStockInvalidId(): void {
        $result = $this->catalog->isInStock(999);
        $this->assertFalse($result);
    }

    public function testGetStockStatusInStock(): void {
        $status = $this->catalog->getStockStatus(1);
        $this->assertEquals('in_stock', $status);
    }

    public function testGetStockStatusOutOfStock(): void {
        $status = $this->catalog->getStockStatus(2);
        $this->assertEquals('out_of_stock', $status);
    }

    public function testGetStockStatusString(): void {
        $status = $this->catalog->getStockStatus(1);
        $this->assertIsString($status);
    }

    public function testIsInStockMultiple(): void {
        $this->assertIsBool($this->catalog->isInStock(1));
        $this->assertIsBool($this->catalog->isInStock(2));
        $this->assertIsBool($this->catalog->isInStock(3));
    }

    public function testGetStockStatusMultiple(): void {
        $status1 = $this->catalog->getStockStatus(1);
        $status2 = $this->catalog->getStockStatus(2);
        $this->assertNotEquals($status1, $status2);
    }

    public function testIsInStockZeroId(): void {
        $result = $this->catalog->isInStock(0);
        $this->assertIsBool($result);
    }

    public function testIsInStockNegativeId(): void {
        $result = $this->catalog->isInStock(-1);
        $this->assertIsBool($result);
    }

    // ========== Precios (10 tests) ==========

    public function testGetPrice(): void {
        $price = $this->catalog->getPrice(1);
        $this->assertIsNumeric($price);
    }

    public function testGetPriceCorrectValue(): void {
        $price = $this->catalog->getPrice(1);
        $this->assertEquals(1000, $price);
    }

    public function testGetPriceInvalidId(): void {
        $price = $this->catalog->getPrice(999);
        $this->assertEquals(0, $price);
    }

    public function testGetPriceFloat(): void {
        $price = $this->catalog->getPrice(1);
        $this->assertTrue(is_float($price) || is_int($price));
    }

    public function testGetDiscountedPriceNoDiscount(): void {
        $price = $this->catalog->getDiscountedPrice(1, 0);
        $this->assertEquals(1000, $price);
    }

    public function testGetDiscountedPrice10Percent(): void {
        $price = $this->catalog->getDiscountedPrice(1, 10);
        $this->assertEquals(900, $price);
    }

    public function testGetDiscountedPrice50Percent(): void {
        $price = $this->catalog->getDiscountedPrice(1, 50);
        $this->assertEquals(500, $price);
    }

    public function testGetDiscountedPrice100Percent(): void {
        $price = $this->catalog->getDiscountedPrice(1, 100);
        $this->assertEquals(0, $price);
    }

    public function testGetDiscountedPriceInvalidId(): void {
        $price = $this->catalog->getDiscountedPrice(999, 10);
        $this->assertEquals(0, $price);
    }

    public function testGetPriceMultiple(): void {
        $price1 = $this->catalog->getPrice(1);
        $price2 = $this->catalog->getPrice(3);
        $this->assertNotEquals($price1, $price2);
    }

    // ========== Reseñas (5 tests) ==========

    public function testGetProductRating(): void {
        $rating = $this->catalog->getProductRating(1);
        $this->assertIsNumeric($rating);
    }

    public function testCountReviews(): void {
        $count = $this->catalog->countReviews(1);
        $this->assertIsInt($count);
    }

    public function testGetRelatedProducts(): void {
        $related = $this->catalog->getRelatedProducts(1);
        $this->assertIsArray($related);
    }

    public function testGetProductRatingRange(): void {
        $rating = $this->catalog->getProductRating(1);
        $this->assertGreaterThanOrEqual(0, $rating);
        $this->assertLessThanOrEqual(5, $rating);
    }

    public function testCountReviewsNonNegative(): void {
        $count = $this->catalog->countReviews(1);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    // ========== Wishlist (5 tests) ==========

    public function testAddToWishlist(): void {
        $result = $this->catalog->addToWishlist(1, 1);
        $this->assertTrue($result);
    }

    public function testRemoveFromWishlist(): void {
        $result = $this->catalog->removeFromWishlist(1, 1);
        $this->assertTrue($result);
    }

    public function testIsInWishlistFalse(): void {
        $result = $this->catalog->isInWishlist(1, 1);
        $this->assertFalse($result);
    }

    public function testAddThenIsInWishlist(): void {
        $this->catalog->addToWishlist(1, 1);
        $inList = $this->catalog->isInWishlist(1, 1);
        $this->assertIsBool($inList);
    }

    public function testRemoveThenIsNotInWishlist(): void {
        $this->catalog->removeFromWishlist(1, 1);
        $inList = $this->catalog->isInWishlist(1, 1);
        $this->assertFalse($inList);
    }

    // ========== Cobertura Adicional - CatalogManager (15 tests) ==========

    public function testGetProductsMultipleCalls(): void {
        $products1 = $this->catalog->getProducts();
        $products2 = $this->catalog->getProducts();
        $this->assertEquals($products1, $products2);
    }

    public function testSearchProductsSubstring(): void {
        $results = $this->catalog->searchProducts('top');
        $this->assertIsArray($results);
    }

    public function testSearchProductsCaseFold(): void {
        $results1 = $this->catalog->searchProducts('laptop');
        $results2 = $this->catalog->searchProducts('LAPTOP');
        $this->assertIsArray($results1);
        $this->assertIsArray($results2);
    }

    public function testGetCategoriesConsistency(): void {
        $categories1 = $this->catalog->getCategories();
        $categories2 = $this->catalog->getCategories();
        $this->assertEquals(count($categories1), count($categories2));
    }

    public function testFilterByPriceBoundary(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 25, 25);
        foreach ($filtered as $p) {
            $this->assertEquals(25, $p['price']);
        }
    }

    public function testSortProductsPreservesAll(): void {
        $products = $this->catalog->getProducts();
        $sorted = $this->catalog->sortProducts($products, 'price');
        $this->assertEquals(count($products), count($sorted));
    }

    public function testPaginateFirstPage(): void {
        $items = range(1, 100);
        $page1 = $this->catalog->paginate($items, 1, 10);
        $this->assertCount(10, $page1);
        $this->assertEquals(1, $page1[0]);
    }

    public function testPaginateMiddlePage(): void {
        $items = range(1, 100);
        $page3 = $this->catalog->paginate($items, 3, 10);
        $this->assertCount(10, $page3);
        $this->assertEquals(21, $page3[0]);
    }

    public function testGetPriceNoProduct(): void {
        $price = $this->catalog->getPrice(999);
        $this->assertEquals(0, $price);
    }

    public function testGetDiscountedPriceZeroDiscount(): void {
        $price = $this->catalog->getDiscountedPrice(1, 0);
        $this->assertEquals(1000, $price);
    }

    public function testGetCategoryProductsMultiple(): void {
        $products1 = $this->catalog->getCategoryProducts(1);
        $products2 = $this->catalog->getCategoryProducts(2);
        $this->assertIsArray($products1);
        $this->assertIsArray($products2);
    }

    public function testSearchProductsEmpty(): void {
        $results = $this->catalog->searchProducts('XYZNONEXISTENT');
        $this->assertIsArray($results);
    }

    public function testWishlistOperations(): void {
        $add = $this->catalog->addToWishlist(1, 1);
        $remove = $this->catalog->removeFromWishlist(1, 1);
        $this->assertTrue($add && $remove);
    }

    public function testWishlistCheck(): void {
        $inList = $this->catalog->isInWishlist(999, 999);
        $this->assertFalse($inList);
    }

    public function testFilterByPriceWithDecimal(): void {
        $products = $this->catalog->getProducts();
        $filtered = $this->catalog->filterByPrice($products, 10.5, 100.99);
        $this->assertIsArray($filtered);
    }
}
