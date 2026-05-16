<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBatchInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('inventories.create');
    }

    public function rules(): array
    {
        return [
            // Global Location
            'location_type' => 'required|in:warehouse,store',
            'warehouse_id'  => 'nullable|required_if:location_type,warehouse|exists:warehouses,id',
            'store_id'      => 'nullable|required_if:location_type,store|exists:stores,id',
            'notes'         => 'nullable|string|max:500',

            // Items List
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity'           => 'required|numeric|min:0.01',
            'items.*.cost_price'         => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Please add products to the list.',
            'items.*.cost_price.required' => 'Cost price is required for valuation.',
        ];
    }
}
