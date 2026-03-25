<?php

namespace App\Http\Requests\Admin\Dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDispatchRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('dispatch.create');
    }

    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',

            // New structure: flat array of split rows, each with item + location + qty
            'dispatches'                  => 'required|array|min:1',
            'dispatches.*.order_item_id'  => 'required|exists:order_items,id',
            'dispatches.*.inventory_id'   => 'required|exists:inventories,id',
            'dispatches.*.quantity'       => 'required|integer|min:1',

            'dispatch_method' => 'nullable|in:courier,pickup',
            'remarks'         => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'dispatches.required'                 => 'Please allocate at least one item before submitting.',
            'dispatches.*.order_item_id.required' => 'Each dispatch row must reference a valid order item.',
            'dispatches.*.inventory_id.required'  => 'Please select a location for every dispatch row.',
            'dispatches.*.inventory_id.exists'    => 'The selected inventory location is invalid.',
            'dispatches.*.quantity.min'           => 'Dispatch quantity must be at least 1.',
        ];
    }
}
