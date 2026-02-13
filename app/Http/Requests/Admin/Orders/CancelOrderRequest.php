<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('order.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:orders,id',
            'cancel_reason' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        $orderId = $this->route('order');
        if ($orderId) {
            $this->merge(['id' => $orderId]);
        }
    }
}