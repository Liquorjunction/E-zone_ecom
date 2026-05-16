<?php

namespace App\Repositories\Admin\Interfaces;

interface ReviewRepositoryInterface
{
    public function getAllReviews();
    public function getPendingReviews();
    public function getReviewById($id);
    public function approveReview($id);
    public function rejectReview($id);
    public function deleteReview($id);
    public function getStatusList();
    public function getRatingList();
    public function getDataForDataTable($request);
}
