<?php

namespace App\Http\Requests\Admin\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ApproveReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('reviews.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:reviews,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The review id field is required.',
            'id.exists' => 'The selected review id is invalid.',
        ];
    }
}
