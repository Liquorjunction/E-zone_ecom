<?php

namespace App\Http\Requests\Admin\dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDispatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('dispatch.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:order_dispatch,id',
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'inventory_id' => 'required|exists:inventory,id',
            'quantity' => 'required|integer|min:1',
            'store_id' => 'required|exists:stores,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'dispatch_method' => 'required|in:courier,pickup',
            'dispatched_by' => 'required|exists:admins,id',
            'dispatched_at' => 'required|date_format:Y-m-d H:i:s',
            'remarks' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Dispatch ID is required.',
            'id.exists' => 'The specified dispatch does not exist.',
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'order_item_id.required' => 'Order Item ID is required.',
            'order_item_id.exists' => 'The specified order item does not exist.',
            'inventory_id.required' => 'Inventory ID is required.',
            'inventory_id.exists' => 'The specified inventory does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
            'store_id.required' => 'Store ID is required.',
            'store_id.exists' => 'The specified store does not exist.',
            'warehouse_id.required' => 'Warehouse ID is required.',
            'warehouse_id.exists' => 'The specified warehouse does not exist.',
            'dispatch_method.required' => 'Dispatch method is required.',
            'dispatch_method.in' => 'Dispatch method must be either courier or pickup.',
            'dispatched_by.required' => 'Dispatched by is required.',
            'dispatched_by.exists' => 'The specified admin does not exist.',
            'dispatched_at.required' => 'Dispatched at is required.',
            'dispatched_at.date_format' => 'Dispatched at must be in the format Y-m-d H:i:s.',
            'remarks.string' => 'Remarks must be a string.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'dispatch ID',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id') ?? $this->input('id'),
        ]);
    }
}
