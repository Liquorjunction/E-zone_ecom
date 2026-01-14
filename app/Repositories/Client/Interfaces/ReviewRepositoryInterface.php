<?php

namespace App\Repositories\Client\Interfaces;

interface ReviewRepositoryInterface
{
    public function getAllApprovedReviews();
    public function getApprovedReviewById(int $id);
    public function getApprovedReviewsByProductId(int $productId);
    public function createReview(array $data);
}
