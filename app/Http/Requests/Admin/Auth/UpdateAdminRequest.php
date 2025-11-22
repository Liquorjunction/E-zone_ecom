<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Checks if the logged-in admin has the 'edit any admin' permission
        return Auth::guard('admin')->user()->can('user management.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
       $adminId = $this->input('id');

        return [
            'id' => 'required',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('admin', 'email')->ignore($adminId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name,guard_name,admin',
            'roles.*' => 'string|exists:roles,name,guard_name,admin',
            'status' => 'required|string|in:active,inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
