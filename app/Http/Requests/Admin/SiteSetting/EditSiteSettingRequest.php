<?php

namespace App\Http\Requests\Admin\SiteSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditSiteSettingRequest extends FormRequest
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
            'site_setting_id' => 'required|exists:site_settings,id',
        ];
    }



    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {        return [
            'site_setting_id.required' => 'The site setting ID is required.',
            'site_setting_id.exists' => 'The selected site setting does not exist.',
        ];
    }
}
