<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends FormRequest
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
            'id' => 'required|exists:categories,id',
            'category-name' => 'required|string|max:255',
            'category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Category ID is required.',
            'id.exists' => 'Category not found.',
            'category-name.string' => 'The category name must be a string.',
            'category-name.max' => 'The category name must not exceed 255 characters.',
            'category-icon.image' => 'The category icon must be an image.',
            'category-icon.mimes' => 'The category icon must be a JPEG, PNG, JPG, or GIF image.',
            'category-icon.max' => 'The category icon size must not exceed 2MB.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Category ID',
            'category-name' => 'Category Name',
            'category-icon' => 'Category Icon',
            'status' => 'Status',
        ];
    }

}
