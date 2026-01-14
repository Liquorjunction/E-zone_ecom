<?php

namespace App\Http\Requests\Admin\FaqCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateFaqCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('faqs.update');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:faq_categories,id',

            'name' => 'required|array',
            'name.en' => 'required|string',
            'name.fr' => 'nullable|string',
            'name.ar' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => 'The English name is required.',
            'name.fr.required' => 'The French name is required.',
            'name.ar.required' => 'The Arabic name is required.',
            'name.en.string' => 'The English name must be a string.',
            'name.fr.string' => 'The French name must be a string.',
            'name.ar.string' => 'The Arabic name must be a string.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }

    public function attributes()
    {
        return [
            'name.en' => 'name (English)',
            'name.fr' => 'name (French)',
            'name.ar' => 'name (Arabic)',
            'status' => 'status',
        ];
    }
}
