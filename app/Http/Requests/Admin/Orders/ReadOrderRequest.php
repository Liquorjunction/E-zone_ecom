<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('order.read');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'sometimes|exists:orders,id',
        ];
    }

    /**
     * Merge route parameter into request data so route-based requests validate correctly.
     */
    protected function prepareForValidation(): void
    {
        $orderId = $this->route('order');
        if ($orderId) {
            $this->merge(['id' => $orderId]);
        }
    }
}
