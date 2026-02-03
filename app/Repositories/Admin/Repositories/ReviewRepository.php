<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Review;
use App\Repositories\Admin\Interfaces\ReviewRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function getAllReviews()
    {
        return Review::with(['product', 'user', 'images', 'orderItem'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingReviews()
    {
        return Review::where('is_approved', false)
            ->with(['product', 'user', 'images', 'orderItem'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getReviewById(int $id)
    {
        return Review::with(['product', 'user', 'images', 'orderItem', 'order'])
            ->findOrFail($id);
    }

    public function approveReview(int $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);
        return $review;
    }

    public function rejectReview(int $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => false]);
        return $review;
    }

    public function deleteReview(int $id)
    {
        return DB::transaction(function () use ($id) {
            $review = Review::findOrFail($id);
            $review->images()->delete();
            return $review->delete();
        });
    }
}
