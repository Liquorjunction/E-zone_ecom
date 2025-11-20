<?php

namespace App\Http\Requests\Admin\SubCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category-subcategory.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:sub_categories,id',
            'sub-category-name' => 'required|string|max:255',
            'sub-category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $this->route('subcategory')->id,
            'status' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Sub-category ID is required.',
            'id.exists' => 'Sub-category not found.',
            'sub-category-name.string' => 'The sub-category name must be a string.',
            'sub-category-name.max' => 'The sub-category name must not exceed 255 characters.',
            'sub-category-name.required' => 'The sub-category name is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'slug.unique' => 'The slug must be unique.',
            'slug.max' => 'The slug must not exceed 255 characters.',
            'slug.required' => 'The slug is required.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Sub-category ID',
            'sub-category-name' => 'Sub-category Name',
            'sub-category-icon' => 'Sub-category Icon',
            'category_id' => 'Category',
            'slug' => 'Slug',
            'status' => 'Status',
        ];
    }
}
