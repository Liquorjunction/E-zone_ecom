<?php

namespace App\Http\Requests\Admin\PrivacyPolicy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePrivacyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('privacy_policies.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Translatable Fields
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
