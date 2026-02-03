<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Interfaces\ReviewRepositoryInterface as ReviewRepo;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(ReviewRepo $reviewRepo)
    {
        $reviews = $reviewRepo->getAllReviews();

        return response()->json([
            'reviews' => $reviews,
        ], 200);
    }

    public function pending(ReviewRepo $reviewRepo)
    {
        $reviews = $reviewRepo->getPendingReviews();

        return response()->json([
            'reviews' => $reviews,
        ], 200);
    }

    public function show(Request $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $review = $reviewRepo->getReviewById($id);

            return response()->json([
                'review' => $review,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Review not found.',
            ], 404);
        }
    }

    public function approve(Request $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $review = $reviewRepo->approveReview($id);

            return response()->json([
                'message' => 'Review approved successfully.',
                'review' => $review,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to approve review.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function reject(Request $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $review = $reviewRepo->rejectReview($id);

            return response()->json([
                'message' => 'Review rejected successfully.',
                'review' => $review,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to reject review.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $reviewRepo->deleteReview($id);

            return response()->json([
                'message' => 'Review deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to delete review.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
