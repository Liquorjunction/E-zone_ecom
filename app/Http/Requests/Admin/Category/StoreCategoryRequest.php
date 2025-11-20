<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category-subcategory.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category-name' => 'required|string|max:255',
            'category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category-name.string' => 'The category name must be a string.',
            'category-name.max' => 'The category name must not exceed 255 characters.',
            'category-name.required' => 'The category name is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category-name' => 'category name',
        ];
    }
}
