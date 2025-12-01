<?php

namespace App\Http\Requests\Admin\AboutUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAboutUsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('about_us.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:about_us,id',
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

    /**
     * Prepare the data for validation.
     * This fixes the issue where 'id' is in the URL but not in the request body.
     */
    protected function prepareForValidation()
    {
        // If 'id' is missing from input but exists in route (e.g. /about-us/{id}/edit)
        if ($this->route('about_us') && ! $this->has('id')) {
            $this->merge(['id' => $this->route('about_us')]);
        }
        // Sometimes route parameter is named 'id'
        elseif ($this->route('id') && ! $this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
