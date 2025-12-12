<?php

namespace App\Http\Requests\Admin\ShippingAndReturns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadShippingAndReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('shipping_return.read');
    }

    public function rules(): array
    {
        return [
            'id' => 'sometimes|exists:shipping_and_returns,id',
        ];
    }
}
