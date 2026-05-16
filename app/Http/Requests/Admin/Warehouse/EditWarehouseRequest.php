<?php

namespace App\Http\Requests\Admin\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('warehouse.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:warehouses,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID is required',
            'id.exists' => 'ID does not exist in warehouses table',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID of the warehouse',
        ];
    }
}
