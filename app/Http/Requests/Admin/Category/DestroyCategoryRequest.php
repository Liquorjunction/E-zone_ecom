<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category.delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The id is required.',
            'id.exists' => 'The id does not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'id',
        ];
    }
}
