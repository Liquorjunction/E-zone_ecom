<?php

namespace App\Http\Requests\Admin\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('reviews.delete');
    }

    public function rules(): array
    {
        return [
            'review_id' => 'required|exists:reviews,id',
        ];
    }

    public function messages(): array
    {
        return [
            'review_id.required' => 'The review id field is required.',
            'review_id.exists' => 'The selected review id is invalid.',
        ];
    }
}
