<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('order.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:orders,id',
            'status' => 'sometimes|string|in:pending,processing,completed,cancelled',
            'tracking_number' => 'sometimes|nullable|string|max:255',
            // Add other fields as necessary
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
