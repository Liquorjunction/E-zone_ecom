<?php

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCityRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('cities.create');
    }

    public function rules()
    {
        return [
            'city_name' => 'required|array',
            'city_name.en' => 'required|string|max:255|unique:cities,city_name->en',
            'city_name.ar' => 'nullable|string|max:255|unique:cities,city_name->ar',
            'city_name.fr' => 'nullable|string|max:255|unique:cities,city_name->fr',
            'country_name' => 'required|array',
            'country_name.en' => 'required|string|max:255',
            'country_name.ar' => 'nullable|string|max:255',
            'country_name.fr' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'city_name.required' => 'The city name field is required.',
            'city_name.en.required' => 'The city name in English is required.',
            'city_name.en.unique' => 'The city name in English must be unique.',
            'city_name.en.max' => 'The city name in English may not be greater than 255 characters.',
            'city_name.ar.max' => 'The city name in Arabic may not be greater than 255 characters.',
            'city_name.fr.max' => 'The city name in French may not be greater than 255 characters.',
            'country_name.required' => 'The country name field is required.',
            'country_name.en.required' => 'The country name in English is required.',
            'country_name.en.max' => 'The country name in English may not be greater than 255 characters.',
            'country_name.ar.max' => 'The country name in Arabic may not be greater than 255 characters.',
            'country_name.fr.max' => 'The country name in French may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid. Allowed values are active or inactive.',
        ];
    }

    public function attributes()
    {
        return [
            'city_name' => 'city name',
            'city_name.en' => 'city name in English',
            'city_name.ar' => 'city name in Arabic',
            'city_name.fr' => 'city name in French',
            'country_name' => 'country name',
            'country_name.en' => 'country name in English',
            'country_name.ar' => 'country name in Arabic',
            'country_name.fr' => 'country name in French',
            'status' => 'status',
        ];
    }
}
