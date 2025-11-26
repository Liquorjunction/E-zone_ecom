<?php

namespace App\Http\Requests\Admin\PrivacyPolicy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePrivacyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('privacy_policies.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Id
            'id' => 'required|integer|exists:privacy_policies,id',

            // Translatable Fields
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
