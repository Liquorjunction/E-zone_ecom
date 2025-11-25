<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:categories,id',

            // Translatable Field
            'category-name' => 'required|array',
            'category-name.en' => 'required|string|max:255',
            'category-name.fr' => 'nullable|string|max:255',
            'category-name.ar' => 'nullable|string|max:255',

            'category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'category-name.en.required' => 'The English category name is required.',
        ];
    }
}
