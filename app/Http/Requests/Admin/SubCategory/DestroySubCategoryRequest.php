<?php

namespace App\Http\Requests\Admin\SubCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroySubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category-subcategory.delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:sub_categories,id',
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
