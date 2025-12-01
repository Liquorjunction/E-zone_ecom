<?php

namespace App\Http\Requests\Admin\AboutUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAboutUsRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('about_us.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'description.en.required' => 'The English description is required.',
        ];
    }

    public function attributes()
    {
        return [
            'description.en' => 'description (English)',
            'description.fr' => 'description (French)',
            'description.ar' => 'description (Arabic)',
        ];
    }


}
