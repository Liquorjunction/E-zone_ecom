<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Reviews\StoreReviewRequest;
use App\Repositories\Client\Interfaces\ReviewRepositoryInterface as ReviewRepo;
use Illuminate\Validation\ValidationException;


class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, ReviewRepo $reviewRepo)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_approved'] = $data['is_approved'] ?? false;

        try {
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
}
