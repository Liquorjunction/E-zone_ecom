<?php

namespace App\Http\Requests\Admin\Tnc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('tnc.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:terms_and_conditions,id',
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.ar' => 'nullable|string',
            'title.fr' => 'nullable|string',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.ar' => 'nullable|string',
            'content.fr' => 'nullable|string',
            'status' => 'required|in:0,1',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'title',
            'title.en' => 'title in English',
            'title.ar' => 'title in Arabic',
            'title.fr' => 'title in French',
            'content' => 'content',
            'content.en' => 'content in English',
            'content.ar' => 'content in Arabic',
            'content.fr' => 'content in French',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.en.required' => 'The title in English is required.',
            'content.required' => 'The content field is required.',
            'content.en.required' => 'The content in English is required.',
        ];
    }
}
