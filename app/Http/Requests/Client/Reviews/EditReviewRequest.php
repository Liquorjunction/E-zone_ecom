<?php

namespace App\Http\Requests\Client\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:reviews,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Review ID is required.',
            'id.exists' => 'The selected review does not exist.',
        ];
    }
}
