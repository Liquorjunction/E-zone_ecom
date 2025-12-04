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
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.fr' => 'nullable|string',
            'title.ar' => 'nullable|string',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.en.required' => 'The English title is required.',
            'description.en.required' => 'The English description is required.',
        ];
    }

    public function attributes()
    {
        return [
            'title.en' => 'title (English)',
            'title.fr' => 'title (French)',
            'title.ar' => 'title (Arabic)',
            'description.en' => 'description (English)',
            'description.fr' => 'description (French)',
            'description.ar' => 'description (Arabic)',
        ];
    }


}
