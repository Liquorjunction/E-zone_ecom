<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Order ID is required.',
            'id.exists' => 'The order does not exist.',
        ];
    }
}
