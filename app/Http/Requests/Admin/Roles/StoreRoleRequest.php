<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Checks if the logged-in admin has the 'create roles' permission
        return Auth::guard('admin')->user()->can('create roles');
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'guard_name' => 'admin',
        ]);
    }
}
