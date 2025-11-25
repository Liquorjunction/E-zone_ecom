<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('category.create');
    }

    public function rules(): array
    {
        return [
            // Translatable Field
            'category-name' => 'required|array',
            'category-name.en' => 'required|string|max:255',
            'category-name.fr' => 'nullable|string|max:255',
            'category-name.ar' => 'nullable|string|max:255',

            // Standard Fields
            'category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable', // Processed in controller/repo
        ];
    }

    public function messages(): array
    {
        return [
            'category-name.en.required' => 'The English category name is required.',
        ];
    }
}
