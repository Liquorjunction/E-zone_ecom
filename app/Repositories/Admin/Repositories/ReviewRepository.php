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

    public function getStatusList()
    {
        return Review::select('is_approved')
            ->distinct()
            ->orderBy('is_approved')
            ->pluck('is_approved')
            ->values()
            ->all();
    }

    public function getRatingList()
    {
        return Review::select('rating')
            ->distinct()
            ->orderBy('rating')
            ->pluck('rating')
            ->values()
            ->all();
    }

    public function getDataForDataTable($request)
    {
        $filters = is_array($request) ? $request : [];

        $reviews = Review::query()
            ->with(['order', 'product', 'productVariant', 'user'])
            ->select('reviews.*')
            ->orderBy('created_at', 'desc');

        // is_approved filter
        if (!empty($filters['status'])) {
            $reviews->where('is_approved', $filters['status']);
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $reviews->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $reviews->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Rating filter
        if (!empty($filters['rating'])) {
            $reviews->where('rating', $filters['rating']);
        }

        // Sorting
        if (!empty($filters['sort_by']) && !empty($filters['sort_order'])) {
            $reviews->orderBy($filters['sort_by'], $filters['sort_order']);
        }

        return $reviews;
    }
}
