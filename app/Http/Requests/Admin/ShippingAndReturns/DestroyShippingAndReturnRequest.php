<?php

namespace App\Http\Requests\Admin\ShippingAndReturns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyShippingAndReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('shipping_return.delete');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:shipping_and_returns,id',
        ];
    }

    protected function prepareForValidation()
    {
        // Helper to grab ID from URL if not in body
        if ($this->route('shipping_return') && !$this->has('id')) {
            $this->merge(['id' => $this->route('shipping_return')]);
        }
        elseif ($this->route('id') && !$this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
