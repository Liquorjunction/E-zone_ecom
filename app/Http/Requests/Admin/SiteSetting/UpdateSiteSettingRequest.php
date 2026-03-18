<?php

namespace App\Http\Requests\Admin\SiteSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('site_status.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'infotext' => 'nullable|array',
            'infotext.*' => 'nullable|string|max:1000',
            'site_status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The site name is required.',
            'name.string' => 'The site name must be a string.',
            'name.max' => 'The site name may not be greater than 255 characters.',
            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be greater than 500 characters.',
            'facebook.url' => 'The facebook url must be a valid url.',
            'facebook.max' => 'The facebook url may not be greater than 255 characters.',
            'twitter.url' => 'The twitter url must be a valid url.',
            'twitter.max' => 'The twitter url may not be greater than 255 characters.',
            'instagram.url' => 'The instagram url must be a valid url.',
            'instagram.max' => 'The instagram url may not be greater than 255 characters.',
            'infotext.string' => 'The info text must be a string.',
            'infotext.max' => 'The info text may not be greater than 1000 characters.',
            'site_status.in' => 'The site status must be active or inactive.',
        ];
    }
}
