<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('inventories.delete');
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
        ];
    }

    /**
     * Merge route parameter into request data so route-based requests validate correctly.
     */
    protected function prepareForValidation(): void
    {
        $inventoryId = $this->route('inventory');
        if ($inventoryId) {
            $this->merge(['id' => $inventoryId]);
        }
    }
}
