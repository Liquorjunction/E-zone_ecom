<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Just check if they are logged in.
        // The repository will verify they are editing their own profile.
        return Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // 'current_password' is only required if 'password' is being filled
            'current_password' => 'nullable|string|required_with:password',

            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
