<?php

namespace App\Http\Requests\Admin\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('warehouse.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_name' => 'required|array',
            'warehouse_name.en' => 'required|string|max:255',
            'warehouse_name.fr' => 'nullable|string|max:255',
            'warehouse_name.ar' => 'nullable|string|max:255',
            'location' => 'required|array',
            'location.en' => 'required|string|max:255',
            'location.fr' => 'nullable|string|max:255',
            'location.ar' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'warehouse_name.en.required' => 'warehouse name (English) is required',
            'location.en.required' => 'location (English) is required',
        ];
    }

    public function attributes(): array
    {
        return [
            'warehouse_name.en' => 'warehouse name (English)',
            'warehouse_name.fr' => 'warehouse name (French)',
            'warehouse_name.ar' => 'warehouse name (Arabic)',
            'location.en' => 'location (English)',
            'location.fr' => 'location (French)',
            'location.ar' => 'location (Arabic)',
        ];
    }
}
