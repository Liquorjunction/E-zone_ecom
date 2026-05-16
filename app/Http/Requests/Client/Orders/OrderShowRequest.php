<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderShowRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'step' => 'required|integer|min:1|max:3',
            'order_id' => 'nullable|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'step.required' => 'The checkout step is required.',
            'step.integer' => 'The checkout step must be a number.',
            'step.min' => 'The minimum step is 1.',
            'step.max' => 'The maximum step is 3.',
            'order_id.exists' => 'The order does not exist.',
        ];
    }
}
