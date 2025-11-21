<?php

namespace App\Http\Requests\Admin\SubCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('subcategory.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sub-category-name' => 'required|string|max:255',
            'sub-category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sub-category-name.string' => 'The sub-category name must be a string.',
            'sub-category-name.max' => 'The sub-category name must not exceed 255 characters.',
            'sub-category-name.required' => 'The sub-category name is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sub-category-name' => 'sub-category name',
            'category_id' => 'category',
        ];
    }
}
