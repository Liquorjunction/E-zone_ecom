<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('role.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Validate IDs
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The role name has already been taken.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Role Name',
        ];
    }
}
