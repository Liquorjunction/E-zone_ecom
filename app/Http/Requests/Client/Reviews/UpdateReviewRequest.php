<?php

namespace App\Http\Requests\Client\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:reviews,id',
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
            'id.required' => 'Review ID is required.',
            'id.exists' => 'The selected review does not exist.',
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
            'id' => 'Review ID',
            'rating' => 'Rating',
            'title' => 'Title',
            'review_text' => 'Review Text',
            'reviewer_name' => 'Reviewer Name',
            'reviewer_email' => 'Reviewer Email',
            'images.*' => 'Images',
        ];
    }
}
