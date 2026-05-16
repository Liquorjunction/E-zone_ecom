<?php

namespace App\Http\Requests\Client\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $this->user()->id,
            'phone' => 'sometimes|nullable|string|max:20|unique:users,phone,' . $this->user()->id,
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff,ico,heic|max:2048',
            'is_newsletter_subscribed' => 'sometimes|boolean',
        ];
    }
}
