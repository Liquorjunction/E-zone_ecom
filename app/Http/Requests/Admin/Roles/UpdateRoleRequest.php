<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('role.update');
    }

    public function rules(): array
    {
        $roleId = $this->route('id'); // Get ID from route parameter
        return [
            'name' => 'required|string|max:255|unique:roles,name,'.$roleId.',id,guard_name,admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
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
