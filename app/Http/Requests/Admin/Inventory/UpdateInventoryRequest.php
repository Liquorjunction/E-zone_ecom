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
            'store_id' => 'nullable|exists:stores,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
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
            'store_id' => 'store',
            'warehouse_id' => 'warehouse',
            'quantity' => 'quantity',
            'batch_no' => 'batch no',
            'status' => 'status',
            'notes' => 'notes',
        ];
    }
}
