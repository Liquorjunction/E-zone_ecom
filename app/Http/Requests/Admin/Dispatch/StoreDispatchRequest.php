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
            
            'order_item_id' => 'required|array',
            'order_item_id.*' => 'required|exists:order_items,id',
            
            'inventory_id' => 'required|array',
            'inventory_id.*' => 'nullable|exists:inventories,id', 
            
            'quantity' => 'required|array',
            'quantity.*' => 'nullable|numeric|min:0',
            
            'dispatch_method' => 'nullable|in:courier,pickup',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'inventory_id.*.exists' => 'The selected inventory location is invalid.',
            'quantity.*.min' => 'Dispatch quantity must be at least 1 for selected items.',
        ];
    }
}