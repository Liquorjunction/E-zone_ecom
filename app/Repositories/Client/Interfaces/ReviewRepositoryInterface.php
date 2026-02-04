<?php

namespace App\Repositories\Client\Interfaces;

interface ReviewRepositoryInterface
{
    public function getAllApprovedReviews();
    public function getApprovedReviewById(int $id);
    public function getApprovedReviewsByProductId(int $productId, ?int $variantId);
    public function getReviewsByStarRating(int $starRating);
    public function createReview(array $data);
    public function getUserReviews(int $userId);
    public function getUserReviewById(int $id, int $userId);
    public function getUserReviewByOrderItem(int $userId, int $orderItemId);
    public function updateReview(int $id, int $userId, array $data);
    public function deleteReview(int $id, int $userId);
}
