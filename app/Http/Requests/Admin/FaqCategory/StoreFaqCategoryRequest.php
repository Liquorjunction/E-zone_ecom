<?php

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFaqCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('faq.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255|unique:faq_categories,name',
            'name.ar' => 'nullable|string|max:255|unique:faq_categories,name',
            'name.fr' => 'nullable|string|max:255|unique:faq_categories,name',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'description.fr' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.en.required' => 'The English name is required.',
            'name.ar.required' => 'The Arabic name is required.',
            'name.fr.required' => 'The French name is required.',
            'name.en.unique' => 'The English name must be unique.',
            'name.ar.unique' => 'The Arabic name must be unique.',
            'name.fr.unique' => 'The French name must be unique.',
            'description.en.required' => 'The English description is required.',
            'description.ar.required' => 'The Arabic description is required.',
            'description.fr.required' => 'The French description is required.',
            'description.en.string' => 'The English description must be a string.',
            'description.ar.string' => 'The Arabic description must be a string.',
            'description.fr.string' => 'The French description must be a string.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name.en' => 'name (English)',
            'name.ar' => 'name (Arabic)',
            'name.fr' => 'name (French)',
            'description.en' => 'description (English)',
            'description.ar' => 'description (Arabic)',
            'description.fr' => 'description (French)',
        ];
    }
}
