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
            'order_id' => 'required|exists:orders,id',
            'dispatch_method' => 'required|in:courier,pickup',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dispatch_from_store_id' => 'required|exists:stores,id',
            'items.*.dispatch_from_warehouse_id' => 'required|exists:warehouses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'dispatch_method.required' => 'Dispatch method is required.',
            'dispatch_method.in' => 'Dispatch method must be either courier or pickup.',
            'remarks.string' => 'Remarks must be a string.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.*.order_item_id.required' => 'Order Item ID is required for each item.',
            'items.*.order_item_id.exists' => 'The specified order item does not exist.',
            'items.*.inventory_id.required' => 'Inventory ID is required for each item.',
            'items.*.inventory_id.exists' => 'The specified inventory does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be an integer for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1 for each item.',
            'items.*.dispatch_from_store_id.required' => 'Store ID is required for each item.',
            'items.*.dispatch_from_store_id.exists' => 'The specified store does not exist.',
            'items.*.dispatch_from_warehouse_id.required' => 'Warehouse ID is required for each item.',
            'items.*.dispatch_from_warehouse_id.exists' => 'The specified warehouse does not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'order ID',
            'dispatch_method' => 'dispatch method',
            'remarks' => 'remarks',
            'items' => 'items',
            'items.*.order_item_id' => 'order item ID',
            'items.*.inventory_id' => 'inventory ID',
            'items.*.quantity' => 'quantity',
            'items.*.dispatch_from_store_id' => 'store ID',
            'items.*.dispatch_from_warehouse_id' => 'warehouse ID',
        ];
    }

    // No need to merge id, as we now use order_id and items array
}
