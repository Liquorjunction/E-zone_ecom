<?php

namespace App\Http\Requests\Client\Reviews;

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
            'order_id' => 'nullable|exists:orders,id',
            'order_item_id' => 'nullable|exists:order_items,id',
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
            'order_id.exists' => 'The selected order does not exist.',
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

    public function attributes()
    {
        return [
            'product_id' => 'Product ID',
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
