<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Repositories\Client\Interfaces\ReviewRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function getAllApprovedReviews()
    {
        return Review::where('is_approved', 'approved')->get();
    }

    public function getApprovedReviewById(int $id)
    {
        return Review::where('id', $id)->where('is_approved', 'approved')->first();
    }

    public function getReviewsByStarRating(int $starRating)
    {
        return Review::where('rating', $starRating)->where('is_approved', 'approved')->get();
    }

    public function getApprovedReviewsByProductId(int $productId, ?int $variantId)
    {
        $query = Review::where('product_id', $productId)->where('is_approved', 'approved');

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }
        return $query->with(['user', 'images'])->orderBy('created_at', 'desc')->get();
    }

    public function createReview(array $data)
    {
        $userId = $data['user_id'] ?? null;

        if (! $userId) {
            throw ValidationException::withMessages(['user_id' => 'User context is required to create a review.']);
        }

        return DB::transaction(function () use ($data, $userId) {
            $orderItem = $this->getDeliveredOrderItem($data, $userId);

            $review = Review::create([
                'product_id' => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'user_id' => $userId,
                'order_id' => $orderItem->order_id,
                'order_item_id' => $orderItem->id,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'review_text' => $data['review_text'] ?? null,
                'reviewer_name' => $data['reviewer_name'] ?? null,
                'reviewer_email' => $data['reviewer_email'] ?? null,
                'is_verified_purchase' => true,
                'is_approved' => $data['is_approved'] ?? 'pending',
            ]);

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imagePath) {
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            return $review;
        });
    }

    protected function getDeliveredOrderItem(array $data, int $userId): OrderItem
    {
        $orderId = $data['order_id'] ?? null;
        $orderItemId = $data['order_item_id'] ?? null;
        $productId = $data['product_id'] ?? null;
        $variantId = $data['product_variant_id'] ?? null;

        $deliveredStatuses = ['delivered', 'completed'];

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->whereIn('status', $deliveredStatuses)
            ->first();

        if (! $order) {
            throw ValidationException::withMessages([
                'order_id' => 'Only delivered orders can be reviewed.',
            ]);
        }

        $orderItem = OrderItem::where('id', $orderItemId)
            ->where('order_id', $order->id)
            ->where('product_id', $productId)
            ->when($variantId, function ($query) use ($variantId) {
                $query->where('variant_id', $variantId);
            })
            ->first();

        if (! $orderItem) {
            throw ValidationException::withMessages([
                'order_item_id' => 'The order item must match the delivered product and variant.',
            ]);
        }

        return $orderItem;
    }

    public function getUserReviews(int $userId)
    {
        return Review::where('user_id', $userId)
            ->with(['product', 'images', 'orderItem'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserReviewById(int $id, int $userId)
    {
        return Review::where('id', $id)
            ->where('user_id', $userId)
            ->with(['product', 'images'])
            ->first();
    }

    public function getUserReviewByOrderItem(int $userId, int $orderItemId)
    {
        return Review::where('user_id', $userId)
            ->where('order_item_id', $orderItemId)
            ->with(['images'])
            ->first();
    }

    public function updateReview(int $id, int $userId, array $data)
    {
        return DB::transaction(function () use ($id, $userId, $data) {
            $review = Review::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $review->update([
                'rating' => $data['rating'] ?? $review->rating,
                'title' => $data['title'] ?? $review->title,
                'review_text' => $data['review_text'] ?? $review->review_text,
                'reviewer_name' => $data['reviewer_name'] ?? $review->reviewer_name,
                'reviewer_email' => $data['reviewer_email'] ?? $review->reviewer_email,
            ]);

            // Handle image updates if provided
            if (isset($data['images']) && is_array($data['images'])) {
                // Delete old images
                $review->images()->delete();

                // Add new images
                foreach ($data['images'] as $imagePath) {
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            return $review->fresh(['images']);
        });
    }

    public function deleteReview(int $id, int $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $review = Review::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $review->images()->delete();
            return $review->delete();
        });
    }
}
