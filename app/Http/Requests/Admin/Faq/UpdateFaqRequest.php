<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateFaqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('faqs.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:faqs,id',
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|array',
            'question.en' => ['required', 'string', 'max:255', Rule::unique('faqs', 'question->en')->ignore($this->id)],
            'question.ar' => 'nullable|string|max:255',
            'question.fr' => 'nullable|string|max:255',
            'answer' => 'required|array',
            'answer.en' => 'required|string',
            'answer.ar' => 'nullable|string',
            'answer.fr' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'The question field is required.',
            'question.en.required' => 'The question in English is required.',
            'question.en.unique' => 'The question in must be unique.',
            'question.en.max' => 'The question in English may not be greater than 255 characters.',
            'question.ar.max' => 'The question in Arabic may not be greater than 255 characters.',
            'question.fr.max' => 'The question in French may not be greater than 255 characters.',
            'answer.required' => 'The answer field is required.',
            'answer.en.required' => 'The answer in English is required.',
        ];
    }

    public function attributes(): array
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
