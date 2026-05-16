<?php

namespace App\Http\Requests\Admin\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('reviews.read');
    }

    public function rules(): array
    {
        return [
            'id' => 'exists:reviews,id|exists:reviews,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.exists' => 'The selected review id is invalid.',
        ];
    }
}
