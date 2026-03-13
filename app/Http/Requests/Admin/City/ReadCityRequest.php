<?php

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadCityRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('cities.read');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer',
        ];
    }
}