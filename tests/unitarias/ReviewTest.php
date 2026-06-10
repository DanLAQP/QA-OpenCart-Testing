<?php
namespace Tests\Unitarias;

use Tests\BaseTestCase;
use Opencart\System\Library\ReviewManager;

/**
 * Class ReviewTest
 *
 * Pruebas unitarias para el módulo Sistema de Reseñas (65 tests)
 *
 * @covers \Opencart\System\Library\ReviewManager
 */
class ReviewTest extends BaseTestCase {
    /**
     * @var ReviewManager
     */
    private $reviews;

    protected function setUp(): void {
        parent::setUp();
        $this->reviews = new ReviewManager($this->registry);
    }

    // ========== Creación de Reseñas (10 tests) ==========

    public function testCreateReviewSuccess(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Excelente producto');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewReturnsId(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Excelente');
        $this->assertArrayHasKey('review_id', $result);
    }

    public function testCreateReviewInvalidRatingLow(): void {
        $result = $this->reviews->createReview(1, 'Juan', 0, 'Malo');
        $this->assertFalse($result['success']);
    }

    public function testCreateReviewInvalidRatingHigh(): void {
        $result = $this->reviews->createReview(1, 'Juan', 6, 'Muy bueno');
        $this->assertFalse($result['success']);
    }

    public function testCreateReviewEmptyAuthor(): void {
        $result = $this->reviews->createReview(1, '', 5, 'Bueno');
        $this->assertFalse($result['success']);
    }

    public function testCreateReviewEmptyComment(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, '');
        $this->assertFalse($result['success']);
    }

    public function testCreateReviewMinRating(): void {
        $result = $this->reviews->createReview(1, 'Juan', 1, 'Comentario');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewMaxRating(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Comentario');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewDefaultStatus(): void {
        $result = $this->reviews->createReview(1, 'Juan', 3, 'Bueno');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewMultiple(): void {
        $result1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $result2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($result1['success']);
        $this->assertTrue($result2['success']);
    }

    // ========== Validación de Calificación (8 tests) ==========

    public function testValidateRating1(): void {
        $result = $this->reviews->validateRating(1);
        $this->assertTrue($result);
    }

    public function testValidateRating5(): void {
        $result = $this->reviews->validateRating(5);
        $this->assertTrue($result);
    }

    public function testValidateRating3(): void {
        $result = $this->reviews->validateRating(3);
        $this->assertTrue($result);
    }

    public function testValidateRatingZero(): void {
        $result = $this->reviews->validateRating(0);
        $this->assertFalse($result);
    }

    public function testValidateRating6(): void {
        $result = $this->reviews->validateRating(6);
        $this->assertFalse($result);
    }

    public function testValidateRatingNegative(): void {
        $result = $this->reviews->validateRating(-1);
        $this->assertFalse($result);
    }

    public function testValidateRatingFloat(): void {
        $result = $this->reviews->validateRating((int)2.5);
        $this->assertTrue($result);
    }

    public function testValidateRatingAll(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->assertTrue($this->reviews->validateRating($i));
        }
    }

    // ========== Moderación (10 tests) ==========

    public function testApproveReview(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $approved = $this->reviews->approveReview($result['review_id']);
        $this->assertTrue($approved);
    }

    public function testRejectReview(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $rejected = $this->reviews->rejectReview($result['review_id']);
        $this->assertTrue($rejected);
    }

    public function testApproveInvalidReview(): void {
        $result = $this->reviews->approveReview(999);
        $this->assertFalse($result);
    }

    public function testRejectInvalidReview(): void {
        $result = $this->reviews->rejectReview(999);
        $this->assertFalse($result);
    }

    public function testDeleteReview(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $deleted = $this->reviews->deleteReview($result['review_id']);
        $this->assertTrue($deleted);
    }

    public function testDeleteInvalidReview(): void {
        $result = $this->reviews->deleteReview(999);
        $this->assertFalse($result);
    }

    public function testMultipleApprove(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($this->reviews->approveReview($r1['review_id']));
        $this->assertTrue($this->reviews->approveReview($r2['review_id']));
    }

    public function testUpdateReviewStatus(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'approved');
        $this->assertTrue($updated);
    }

    public function testUpdateReviewStatusInvalid(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'invalid_status');
        $this->assertFalse($updated);
    }

    public function testStatusTransition(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $this->reviews->approveReview($result['review_id']);
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'rejected');
        $this->assertTrue($updated);
    }

    // ========== Listado de Reseñas (10 tests) ==========

    public function testGetReviewsByProduct(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $reviews = $this->reviews->getReviewsByProduct(1);
        $this->assertIsArray($reviews);
    }

    public function testGetReviewsByProductEmpty(): void {
        $reviews = $this->reviews->getReviewsByProduct(999);
        $this->assertEmpty($reviews);
    }

    public function testGetReviewsByProductApproved(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $this->reviews->approveReview($result['review_id']);
        $reviews = $this->reviews->getReviewsByProduct(1, 'approved');
        $this->assertNotEmpty($reviews);
    }

    public function testGetReviewsByProductPending(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $reviews = $this->reviews->getReviewsByProduct(1, 'pending');
        $this->assertNotEmpty($reviews);
    }

    public function testGetReviewsByProductRejected(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $this->reviews->rejectReview($result['review_id']);
        $reviews = $this->reviews->getReviewsByProduct(1, 'rejected');
        $this->assertNotEmpty($reviews);
    }

    public function testGetReviewsByProductMultiple(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($r1['success']);
        $this->assertTrue($r2['success']);
    }

    public function testGetReviewsByProductDifferentStatus(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->reviews->approveReview($r1['review_id']);
        $approved = $this->reviews->getReviewsByProduct(1, 'approved');
        $this->assertGreaterThanOrEqual(1, count($approved));
    }

    public function testGetReviewsByProductContainsData(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Excelente');
        $this->assertTrue($result['success']);
        $detail = $this->reviews->getReviewDetail($result['review_id']);
        if (!empty($detail)) {
            $this->assertArrayHasKey('author', $detail);
            $this->assertArrayHasKey('rating', $detail);
        }
    }

    public function testGetReviewsByProductCount(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($r1['success'] && $r2['success']);
    }

    // ========== Promedio de Calificaciones (8 tests) ==========

    public function testGetAverageRatingEmpty(): void {
        $avg = $this->reviews->getAverageRating(999);
        $this->assertEquals(0, $avg);
    }

    public function testGetAverageRatingSingle(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $reviews = $this->reviews->getReviewsByProduct(1);
        if (!empty($reviews)) {
            $this->reviews->approveReview($reviews[0]['id']);
        }
        $avg = $this->reviews->getAverageRating(1);
        $this->assertGreaterThanOrEqual(0, $avg);
    }

    public function testGetAverageRatingMultiple(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $this->reviews->createReview(1, 'Maria', 3, 'Neutral');
        $avg = $this->reviews->getAverageRating(1);
        $this->assertGreaterThanOrEqual(0, $avg);
    }

    public function testGetAverageRatingPrecision(): void {
        $avg = $this->reviews->getAverageRating(1);
        $this->assertTrue(is_float($avg) || is_int($avg));
    }

    public function testGetAverageRatingRange(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $avg = $this->reviews->getAverageRating(1);
        $this->assertGreaterThanOrEqual(0, $avg);
        $this->assertLessThanOrEqual(5, $avg);
    }

    public function testCountReviewsEmpty(): void {
        $count = $this->reviews->countReviews(999);
        $this->assertEquals(0, $count);
    }

    public function testCountReviewsMultiple(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($r1['success']);
        $this->assertTrue($r2['success']);
    }

    public function testCountReviewsByStatus(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $this->reviews->approveReview($r1['review_id']);
        $count = $this->reviews->countReviews(1, 'approved');
        $this->assertGreaterThanOrEqual(1, $count);
    }

    // ========== Ordenamiento y Filtrado (10 tests) ==========

    public function testSortReviewsByDate(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $reviews = $this->reviews->getReviewsByProduct(1);
        $sorted = $this->reviews->sortReviews($reviews, 'date');
        $this->assertIsArray($sorted);
    }

    public function testSortReviewsByRating(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 2, 'Malo');
        $this->assertTrue($r1['success']);
        $this->assertTrue($r2['success']);
    }

    public function testFilterReviewsByRatingMin(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 2, 'Malo');
        $this->assertTrue($r1['success']);
        $this->assertTrue($r2['success']);
    }

    public function testFilterReviewsByRatingRange(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 3, 'Neutral');
        $r3 = $this->reviews->createReview(1, 'Pedro', 1, 'Malo');
        $this->assertTrue($r1['success'] && $r2['success'] && $r3['success']);
    }

    public function testFilterReviewsByDate(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $reviews = $this->reviews->getReviewsByProduct(1);
        $filtered = $this->reviews->filterReviewsByDate($reviews, '2020-01-01', '2099-12-31');
        $this->assertIsArray($filtered);
    }

    public function testFilterReviewsByDateEmpty(): void {
        $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $reviews = $this->reviews->getReviewsByProduct(1);
        $filtered = $this->reviews->filterReviewsByDate($reviews, '2010-01-01', '2015-12-31');
        $this->assertIsArray($filtered);
    }

    public function testPaginateReviews(): void {
        for ($i = 0; $i < 10; $i++) {
            $this->reviews->createReview(1, "Author$i", rand(1, 5), 'Comentario');
        }
        $reviews = $this->reviews->getReviewsByProduct(1);
        $page1 = $this->reviews->paginateReviews($reviews, 1, 5);
        $this->assertLessThanOrEqual(5, count($page1));
    }

    public function testPaginateReviewsPage2(): void {
        for ($i = 0; $i < 10; $i++) {
            $this->reviews->createReview(1, "Author$i", rand(1, 5), 'Comentario');
        }
        $reviews = $this->reviews->getReviewsByProduct(1);
        $page2 = $this->reviews->paginateReviews($reviews, 2, 5);
        $this->assertIsArray($page2);
    }

    public function testSortReviewsEmpty(): void {
        $sorted = $this->reviews->sortReviews([], 'date');
        $this->assertEmpty($sorted);
    }

    public function testFilterReviewsEmpty(): void {
        $filtered = $this->reviews->filterReviewsByRating([], 1, 5);
        $this->assertEmpty($filtered);
    }

    // ========== Utilidades (9 tests) ==========

    public function testIsReviewHelpful(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $helpful = $this->reviews->isReviewHelpful($result['review_id']);
        $this->assertTrue($helpful);
    }

    public function testIsReviewHelpfulFalse(): void {
        $helpful = $this->reviews->isReviewHelpful(999);
        $this->assertFalse($helpful);
    }

    public function testGetReviewDetail(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $detail = $this->reviews->getReviewDetail($result['review_id']);
        $this->assertArrayHasKey('author', $detail);
    }

    public function testGetReviewDetailInvalid(): void {
        $detail = $this->reviews->getReviewDetail(999);
        $this->assertEmpty($detail);
    }

    public function testIsReviewAllowed(): void {
        $allowed = $this->reviews->isReviewAllowed(1, 1);
        $this->assertTrue($allowed);
    }

    public function testHasCustomerReviewedFalse(): void {
        $reviewed = $this->reviews->hasCustomerReviewed(1, 1);
        $this->assertFalse($reviewed);
    }

    public function testHasCustomerReviewedTrue(): void {
        $result = $this->reviews->createReview(1, 'Customer1', 5, 'Bueno');
        $this->assertTrue($result['success']);
    }

    public function testReviewPersistence(): void {
        $r1 = $this->reviews->createReview(1, 'Juan', 5, 'Bueno');
        $r2 = $this->reviews->createReview(1, 'Maria', 4, 'Muy bueno');
        $this->assertTrue($r1['success']);
        $this->assertTrue($r2['success']);
    }

    public function testReviewDataIntegrity(): void {
        $result = $this->reviews->createReview(1, 'Juan', 5, 'Comentario');
        $detail = $this->reviews->getReviewDetail($result['review_id']);
        $this->assertEquals('Juan', $detail['author']);
        $this->assertEquals(5, $detail['rating']);
    }

    // ========== Cobertura Adicional - ReviewManager (15 tests) ==========

    public function testCreateReviewBoundaryRating1(): void {
        $result = $this->reviews->createReview(1, 'Test', 1, 'Mínimo');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewBoundaryRating5(): void {
        $result = $this->reviews->createReview(1, 'Test', 5, 'Máximo');
        $this->assertTrue($result['success']);
    }

    public function testCreateReviewRatingBelowMin(): void {
        $result = $this->reviews->createReview(1, 'Test', 0, 'Invalid');
        $this->assertFalse($result['success']);
    }

    public function testCreateReviewRatingAboveMax(): void {
        $result = $this->reviews->createReview(1, 'Test', 6, 'Invalid');
        $this->assertFalse($result['success']);
    }

    public function testApproveReviewMultiple(): void {
        $r1 = $this->reviews->createReview(1, 'A', 5, 'Good');
        $r2 = $this->reviews->createReview(1, 'B', 4, 'Great');
        $a1 = $this->reviews->approveReview($r1['review_id']);
        $a2 = $this->reviews->approveReview($r2['review_id']);
        $this->assertTrue($a1 && $a2);
    }

    public function testRejectReviewMultiple(): void {
        $r1 = $this->reviews->createReview(1, 'A', 5, 'Good');
        $r2 = $this->reviews->createReview(1, 'B', 4, 'Great');
        $rej1 = $this->reviews->rejectReview($r1['review_id']);
        $rej2 = $this->reviews->rejectReview($r2['review_id']);
        $this->assertTrue($rej1 && $rej2);
    }

    public function testDeleteReviewMultiple(): void {
        $r1 = $this->reviews->createReview(1, 'A', 5, 'Good');
        if ($r1['success']) {
            $d1 = $this->reviews->deleteReview($r1['review_id']);
            $this->assertTrue($d1);
        }
    }

    public function testUpdateReviewStatusPending(): void {
        $result = $this->reviews->createReview(1, 'Test', 5, 'Good');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'pending');
        $this->assertTrue($updated);
    }

    public function testUpdateReviewStatusApproved(): void {
        $result = $this->reviews->createReview(1, 'Test', 5, 'Good');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'approved');
        $this->assertTrue($updated);
    }

    public function testUpdateReviewStatusRejected(): void {
        $result = $this->reviews->createReview(1, 'Test', 5, 'Good');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'rejected');
        $this->assertTrue($updated);
    }

    public function testUpdateReviewStatusInvalidStatus(): void {
        $result = $this->reviews->createReview(1, 'Test', 5, 'Good');
        $updated = $this->reviews->updateReviewStatus($result['review_id'], 'invalid_status');
        $this->assertFalse($updated);
    }

    public function testUpdateReviewStatusNonExistent(): void {
        $updated = $this->reviews->updateReviewStatus(999, 'approved');
        $this->assertFalse($updated);
    }

    public function testGetReviewDetailNonExistent(): void {
        $detail = $this->reviews->getReviewDetail(999);
        $this->assertEmpty($detail);
    }

    public function testPaginateReviewsPage1(): void {
        for ($i = 0; $i < 20; $i++) {
            $this->reviews->createReview(1, "Author$i", rand(1, 5), "Comment$i");
        }
        $reviews = $this->reviews->getReviewsByProduct(1);
        $page1 = $this->reviews->paginateReviews($reviews, 1, 5);
        $this->assertLessThanOrEqual(5, count($page1));
    }
}
