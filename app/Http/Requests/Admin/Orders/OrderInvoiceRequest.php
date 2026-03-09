<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = Auth::guard('admin')->user();

        return $admin ? $admin->can('order.read') : false;
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
