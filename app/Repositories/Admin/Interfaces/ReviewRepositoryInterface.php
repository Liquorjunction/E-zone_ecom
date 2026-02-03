<?php

namespace App\Repositories\Admin\Interfaces;

interface ReviewRepositoryInterface
{
    public function getAllReviews();
    public function getPendingReviews();
    public function getReviewById(int $id);
    public function approveReview(int $id);
    public function rejectReview(int $id);
    public function deleteReview(int $id);
}
