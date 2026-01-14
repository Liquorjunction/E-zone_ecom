<?php

namespace App\Http\Requests\Admin\FaqCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFaqCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('faqs.create');
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
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
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
        ];
    }

    public function attributes(): array
    {
        return [
            'name.en' => 'name (English)',
            'name.ar' => 'name (Arabic)',
            'name.fr' => 'name (French)',
        ];
    }
}
