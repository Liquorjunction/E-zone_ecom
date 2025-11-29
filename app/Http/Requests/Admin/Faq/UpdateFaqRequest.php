<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'question.en' => 'required|string|max:255',
            'question.ar' => 'nullable|string|max:255',
            'question.fr' => 'nullable|string|max:255',
            'answer' => 'required|array',
            'answer.en' => 'required|string',
            'answer.ar' => 'nullable|string',
            'answer.fr' => 'nullable|string',
        ];
    }
}
