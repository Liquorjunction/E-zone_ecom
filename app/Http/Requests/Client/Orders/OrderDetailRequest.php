<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'order' => 'required|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order.required' => 'Order ID is required.',
            'order.exists' => 'The order does not exist.',
        ];
    }
}
