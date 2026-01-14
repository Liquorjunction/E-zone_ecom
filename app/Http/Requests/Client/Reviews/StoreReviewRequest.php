<?php

namespace App\Http\Requests\Client\Reviews;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review_text' => 'nullable|string',
            'reviewer_name' => 'nullable|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'images.*' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg,webp,bmp,ico,tiff,avif',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'product_variant_id.required' => 'Product Variant ID is required.',
            'product_variant_id.exists' => 'The selected product variant does not exist.',
            'order_id.required' => 'Order ID is required to verify delivery.',
            'order_id.exists' => 'The selected order does not exist.',
            'order_item_id.required' => 'Order Item ID is required to verify delivery.',
            'order_item_id.exists' => 'The selected order item does not exist.',
            'rating.required' => 'Rating is required.',
            'rating.numeric' => 'Rating must be a number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating may not be greater than 5.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'reviewer_name.max' => 'Reviewer name may not be greater than 255 characters.',
            'reviewer_email.email' => 'Reviewer email must be a valid email address.',
            'reviewer_email.max' => 'Reviewer email may not be greater than 255 characters.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg, webp, bmp, ico, tiff, avif.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userId = $this->user()?->id;
            $orderId = $this->input('order_id');
            $orderItemId = $this->input('order_item_id');
            $productId = $this->input('product_id');
            $variantId = $this->input('product_variant_id');

            if (! $userId || ! $orderId || ! $orderItemId || ! $productId) {
                return;
            }

            $deliveredStatuses = ['delivered', 'completed'];

            $order = Order::where('id', $orderId)
                ->where('user_id', $userId)
                ->whereIn('status', $deliveredStatuses)
                ->first();

            if (! $order) {
                $validator->errors()->add('order_id', 'You can only review products from delivered orders.');
                return;
            }

            $orderItem = OrderItem::where('id', $orderItemId)
                ->where('order_id', $orderId)
                ->where('product_id', $productId)
                ->when($variantId, function ($query) use ($variantId) {
                    $query->where('variant_id', $variantId);
                })
                ->first();

            if (! $orderItem) {
                $validator->errors()->add('order_item_id', 'The selected order item does not match the product or variant.');
            }
        });
    }

    public function attributes()
    {
        return [
            'product_id' => 'Product ID',
            'product_variant_id' => 'Product Variant ID',
            'order_id' => 'Order ID',
            'order_item_id' => 'Order Item ID',
            'rating' => 'Rating',
            'title' => 'Title',
            'review_text' => 'Review Text',
            'reviewer_name' => 'Reviewer Name',
            'reviewer_email' => 'Reviewer Email',
            'images.*' => 'Images',
        ];
    }
}
