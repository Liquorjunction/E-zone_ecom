<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Reviews\DestroyReviewRequest;
use App\Http\Requests\Client\Reviews\ReadReviewRequest;
use App\Http\Requests\Client\Reviews\StoreReviewRequest;
use App\Http\Requests\Client\Reviews\UpdateReviewRequest;
use App\Repositories\Client\Interfaces\ReviewRepositoryInterface as ReviewRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ReviewController extends Controller
{
    public function index(Request $request, ReviewRepo $reviewRepo)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('variant_id');

        if ($productId) {
            $reviews = $reviewRepo->getApprovedReviewsByProductId($productId, $variantId);
        } else {
            $reviews = $reviewRepo->getAllApprovedReviews();
        }

        return response()->json([
            'reviews' => $reviews,
        ], 200);
    }

    public function show(ReadReviewRequest $request, ReviewRepo $reviewRepo)
    {
        $id = $request->validated()['id'];
        $review = $reviewRepo->getApprovedReviewById($id);

        if (!$review) {
            return response()->json([
                'message' => 'Review not found.',
            ], 404);
        }

        return response()->json([
            'review' => $review,
        ], 200);
    }

    public function myReviews(Request $request, ReviewRepo $reviewRepo)
    {
        $reviews = $reviewRepo->getUserReviews($request->user()->id);

        return response()->json([
            'reviews' => $reviews,
        ], 200);
    }

    public function store(StoreReviewRequest $request, ReviewRepo $reviewRepo)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_approved'] = $data['is_approved'] ?? 'pending';

        try {

            Log::info('Creating review', $data);
            $review = $reviewRepo->createReview($data);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Unable to submit review.',
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }

    public function update(UpdateReviewRequest $request, ReviewRepo $reviewRepo)
    {
        $data = $request->validated();
        $userId = $request->user()->id;
        $id = $data['id'];

        try {
            $review = $reviewRepo->updateReview($id, $userId, $data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to update review.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review,
        ], 200);
    }

    public function destroy(DestroyReviewRequest $request, ReviewRepo $reviewRepo)
    {
        $userId = $request->user()->id;
        $id = $request->validated()['id'];

        try {
            $reviewRepo->deleteReview($id, $userId);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to delete review.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Review deleted successfully.',
        ], 200);
    }
}
