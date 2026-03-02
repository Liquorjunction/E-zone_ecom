<?php

namespace App\Http\Requests\Admin\SiteSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddOpeningHoursSiteSettingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('site_status.update');
    }

    public function rules()
    {
        return [
            'opening_hours' => 'required|string',
        ];
    }
}
