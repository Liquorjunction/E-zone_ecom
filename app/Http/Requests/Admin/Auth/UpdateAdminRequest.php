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
                'string',
                'email',
                'max:255',
                Rule::unique('admin', 'email')->ignore($adminId),
            ],
            'username' => [
                'string',
                'max:255',
                Rule::unique('admin', 'username')->ignore($adminId),
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'remarks' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string',
            'role' => 'required|string|exists:roles,name,guard_name,admin',
            'roles.*' => 'string|exists:roles,name,guard_name,admin',
            'status' => 'required|string|in:active,inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The admin ID is required.',
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username has already been taken.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
            'profile_image.image' => 'The profile image must be an image file.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpeg, png, jpg, gif.',
            'profile_image.max' => 'The profile image may not be greater than 2MB.',
        ];
    }
}
