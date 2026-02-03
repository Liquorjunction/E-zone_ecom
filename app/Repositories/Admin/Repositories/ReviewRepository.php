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
        return Review::where('is_approved', 'pending')
            ->with(['product', 'user', 'images', 'orderItem'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getReviewById($id)
    {
        return Review::with(['order', 'product', 'productVariant', 'user'])
            ->select('reviews.*')
            ->orderBy('created_at', 'desc')
            ->findOrFail($id);
    }

    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => 'approved']);
        return $review;
    }

    public function rejectReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => 'rejected']);
        return $review;
    }

    public function deleteReview($id)
    {
        return DB::transaction(function () use ($id) {
            $review = Review::findOrFail($id);
            $review->images()->delete();
            return $review->delete();
        });
    }

    public function getDataForDataTable()
    {
        return Review::with(['order', 'product', 'productVariant', 'user'])
            ->select('reviews.*')
            ->orderBy('created_at', 'desc');
    }
}
