<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SeedAllInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('inventories.create');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'quantity' => $this->input('quantity', 100),
        ]);
    }

    public function rules(): array
    {
        return [
            'location_type' => 'required|in:warehouse,store',
            'warehouse_id' => 'nullable|required_if:location_type,warehouse|exists:warehouses,id',
            'store_id' => 'nullable|required_if:location_type,store|exists:stores,id',
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'location_type.required' => 'The location type field is required.',
            'location_type.in' => 'The location type must be either warehouse or store.',
            'warehouse_id.required_if' => 'The warehouse field is required when location type is warehouse.',
            'store_id.required_if' => 'The store field is required when location type is store.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}
