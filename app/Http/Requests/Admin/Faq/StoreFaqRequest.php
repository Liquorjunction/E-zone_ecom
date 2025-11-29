<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFaqRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('faqs.create');
    }

    public function rules()
    {
        return [
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|array',
            'question.en' => 'required|string|max:255',
            'question.ar' => 'nullable|string|max:255',
            'question.fr' => 'nullable|string|max:255',
            'answer' => 'required|array',
            'answer.en' => 'required|string',
            'answer.ar' => 'nullable|string',
            'answer.fr' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'question.required' => 'The question field is required.',
            'question.en.required' => 'The question in English is required.',
            'question.en.max' => 'The question in English may not be greater than 255 characters.',
            'question.ar.max' => 'The question in Arabic may not be greater than 255 characters.',
            'question.fr.max' => 'The question in French may not be greater than 255 characters.',
            'answer.required' => 'The answer field is required.',
            'answer.en.required' => 'The answer in English is required.',
        ];
    }

    public function attributes()
    {
        return [
            'question' => 'question',
            'question.en' => 'question in English',
            'question.ar' => 'question in Arabic',
            'question.fr' => 'question in French',
            'answer' => 'answer',
            'answer.en' => 'answer in English',
            'answer.ar' => 'answer in Arabic',
            'answer.fr' => 'answer in French',
        ];
    }
}
