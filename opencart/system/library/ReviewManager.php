<?php
namespace Opencart\System\Library;

/**
 * Class ReviewManager
 *
 * Gestiona reseñas y calificaciones de productos.
 *
 * @package Opencart\System\Library
 */
class ReviewManager {
    private $registry;
    private $db;
    private $config;
    private $reviews = [];

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    public function createReview(int $productId, string $author, int $rating, string $comment): array {
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'error' => 'Calificación inválida'];
        }

        if (empty($author) || empty($comment)) {
            return ['success' => false, 'error' => 'Campos obligatorios'];
        }

        $review = [
            'id' => time(),
            'product_id' => $productId,
            'author' => $author,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s')
        ];

        $this->reviews[$review['id']] = $review;

        return ['success' => true, 'review_id' => $review['id']];
    }

    public function validateRating(int $rating): bool {
        return $rating >= 1 && $rating <= 5;
    }

    public function approveReview(int $reviewId): bool {
        if (isset($this->reviews[$reviewId])) {
            $this->reviews[$reviewId]['status'] = 'approved';
            return true;
        }
        return false;
    }

    public function rejectReview(int $reviewId): bool {
        if (isset($this->reviews[$reviewId])) {
            $this->reviews[$reviewId]['status'] = 'rejected';
            return true;
        }
        return false;
    }

    public function deleteReview(int $reviewId): bool {
        if (isset($this->reviews[$reviewId])) {
            unset($this->reviews[$reviewId]);
            return true;
        }
        return false;
    }

    public function getReviewsByProduct(int $productId, string $status = 'approved'): array {
        $results = [];
        foreach ($this->reviews as $review) {
            if ($review['product_id'] == $productId && $review['status'] == $status) {
                $results[] = $review;
            }
        }
        return $results;
    }

    public function getAverageRating(int $productId): float {
        $reviews = $this->getReviewsByProduct($productId);
        if (empty($reviews)) {
            return 0;
        }

        $sum = array_sum(array_column($reviews, 'rating'));
        return round($sum / count($reviews), 1);
    }

    public function countReviews(int $productId, string $status = 'approved'): int {
        return count($this->getReviewsByProduct($productId, $status));
    }

    public function sortReviews(array $reviews, string $by = 'date'): array {
        usort($reviews, function($a, $b) use ($by) {
            if ($by === 'rating') {
                return $b['rating'] <=> $a['rating'];
            }
            return strtotime($b['date']) - strtotime($a['date']);
        });
        return $reviews;
    }

    public function filterReviewsByRating(array $reviews, int $minRating, int $maxRating = 5): array {
        return array_filter($reviews, function($r) use ($minRating, $maxRating) {
            return $r['rating'] >= $minRating && $r['rating'] <= $maxRating;
        });
    }

    public function filterReviewsByDate(array $reviews, string $startDate, string $endDate): array {
        return array_filter($reviews, function($r) use ($startDate, $endDate) {
            $date = strtotime($r['date']);
            return $date >= strtotime($startDate) && $date <= strtotime($endDate);
        });
    }

    public function isReviewHelpful(int $reviewId): bool {
        return isset($this->reviews[$reviewId]);
    }

    public function updateReviewStatus(int $reviewId, string $newStatus): bool {
        if (!isset($this->reviews[$reviewId])) {
            return false;
        }

        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }

        $this->reviews[$reviewId]['status'] = $newStatus;
        return true;
    }

    public function paginateReviews(array $reviews, int $page = 1, int $perPage = 5): array {
        $offset = ($page - 1) * $perPage;
        return array_slice($reviews, $offset, $perPage);
    }

    public function getReviewDetail(int $reviewId): array {
        if (isset($this->reviews[$reviewId])) {
            return $this->reviews[$reviewId];
        }
        return [];
    }

    public function isReviewAllowed(int $customerId, int $productId): bool {
        return true;
    }

    public function hasCustomerReviewed(int $customerId, int $productId): bool {
        foreach ($this->reviews as $review) {
            if ($review['product_id'] == $productId && strpos($review['author'], $customerId) !== false) {
                return true;
            }
        }
        return false;
    }
}
