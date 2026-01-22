<?php

namespace App\Http\Requests\Admin\SubCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('subcategory.create');
    }

    public function rules(): array
    {
        return [
            // Translatable Field
            'sub-category-name' => 'required|array',
            'sub-category-name.en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub-categories', 'sub-category-name'),
            ],
            'sub-category-name.fr' => 'nullable|string|max:255',
            'sub-category-name.ar' => 'nullable|string|max:255',

            // Standard Fields
            'category_id' => 'required|exists:categories,id',
            'sub-category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'sub-category-name.en.required' => 'The English sub-category name is required.',
            'sub-category-name.en.unique' => 'The English sub-category name has already been taken.',
            'category_id.required' => 'Please select a parent category.',
        ];
    }
}
