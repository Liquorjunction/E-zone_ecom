<?php

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'id' => [
                Rule::requiredIf($this->routeIs('admin.cities.edit')),
                'nullable',
                'integer',
                'exists:cities,id',
            ],
        ];
    }
}
