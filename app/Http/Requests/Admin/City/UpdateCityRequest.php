<?php

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCityRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('cities.update');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer',
            'city_name' => 'sometimes|array',
            'city_name.en' => 'sometimes|required|string|max:255|unique:cities,city_name->en,' . $this->route('city'),
            'city_name.ar' => 'nullable|string|max:255|unique:cities,city_name->ar,' . $this->route('city'),
            'city_name.fr' => 'nullable|string|max:255|unique:cities,city_name->fr,' . $this->route('city'),
            'country_name' => 'sometimes|array',
            'country_name.en' => 'sometimes|required|string|max:255',
            'country_name.ar' => 'nullable|string|max:255',
            'country_name.fr' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'id.integer' => 'The city ID must be an integer.',
            'city_name.en.string' => 'The city name in English must be a string.',
            'city_name.en.max' => 'The city name in English may not be greater than 255 characters.',
            'city_name.ar.string' => 'The city name in Arabic must be a string.',
            'city_name.ar.max' => 'The city name in Arabic may not be greater than 255 characters.',
            'city_name.fr.string' => 'The city name in French must be a string.',
            'city_name.fr.max' => 'The city name in French may not be greater than 255 characters.',
            'country_name.en.string' => 'The country name in English must be a string.',
            'country_name.en.max' => 'The country name in English may not be greater than 255 characters.',
            'country_name.ar.string' => 'The country name in Arabic must be a string.',
            'country_name.ar.max' => 'The country name in Arabic may not be greater than 255 characters.',
            'country_name.fr.string' => 'The country name in French must be a string.',
            'country_name.fr.max' => 'The country name in French may not be greater than 255 characters.',
            'status.in' => 'The selected status is invalid. Allowed values are active or inactive.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'city ID',
            'city_name.en' => 'city name in English',
            'city_name.ar' => 'city name in Arabic',
            'city_name.fr' => 'city name in French',
            'country_name.en' => 'country name in English',
            'country_name.ar' => 'country name in Arabic',
            'country_name.fr' => 'country name in French',
            'status' => 'status',
        ];
    }
}
