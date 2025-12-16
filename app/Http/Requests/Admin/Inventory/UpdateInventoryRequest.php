<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('inventories.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:inventories,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'location_type' => 'required|in:warehouse,store',
            'store_id' => 'nullable|exists:stores,id|required_without:warehouse_id|prohibited_with:warehouse_id',
            'warehouse_id' => 'nullable|exists:warehouses,id|required_without:store_id|prohibited_with:store_id',
            'quantity' => 'required|numeric|min:0',
            'batch_no' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,depleted',
            'notes' => 'nullable|string',
        ];
        return [
            'id' => 'required|exists:inventories,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'location_type' => 'required|in:warehouse,store',
            'store_id' => 'nullable|exists:stores,id|required_without:warehouse_id|prohibited_with:warehouse_id',
            'warehouse_id' => 'nullable|exists:warehouses,id|required_without:store_id|prohibited_with:store_id',
            'cost_price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'batch_no' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,depleted',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The id field is required.',
            'id.exists' => 'The selected id is invalid.',
            'product_id.required' => 'The product field is required.',
            'product_id.exists' => 'The selected product is invalid.',
            'product_variant_id.exists' => 'The selected product variant is invalid.',
            'store_id.exists' => 'The selected store is invalid.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',
            'warehouse_id.prohibited_with' => 'Please provide either a warehouse or a store, not both.',
            'store_id.prohibited_with' => 'Please provide either a warehouse or a store, not both.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.min' => 'The quantity must be at least 0.',
            'batch_no.max' => 'The batch no may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
        return [
            'id.required' => 'The id field is required.',
            'id.exists' => 'The selected id is invalid.',
            'product_id.required' => 'The product field is required.',
            'product_id.exists' => 'The selected product is invalid.',
            'product_variant_id.exists' => 'The selected product variant is invalid.',
            'location_type.required' => 'The location type field is required.',
            'location_type.in' => 'The location type must be either warehouse or store.',
            'store_id.exists' => 'The selected store is invalid.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',
            'warehouse_id.prohibited_with' => 'Please provide either a warehouse or a store, not both.',
            'store_id.prohibited_with' => 'Please provide either a warehouse or a store, not both.',
            'cost_price.required' => 'The cost price field is required.',
            'cost_price.numeric' => 'The cost price must be a number.',
            'cost_price.min' => 'The cost price must be at least 0.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.min' => 'The quantity must be at least 0.',
            'batch_no.max' => 'The batch no may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }

    public function attributes()
    {
        return [
            'product_id' => 'product',
            'product_variant_id' => 'product variant',
            'location_type' => 'location type',
            'store_id' => 'store',
            'warehouse_id' => 'warehouse',
            'cost_price' => 'cost price',
            'quantity' => 'quantity',
            'batch_no' => 'batch no',
            'status' => 'status',
            'notes' => 'notes',
        ];
    }
}
