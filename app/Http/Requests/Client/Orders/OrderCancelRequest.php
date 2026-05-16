<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderCancelRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Order ID is required.',
            'id.exists' => 'The order does not exist.',
            'reason.required' => 'A cancellation reason is required.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason must not exceed 255 characters.',
        ];
    }
}
