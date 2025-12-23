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
            'category-icon' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'nullable', // Processed in controller/repo
        ];
    }

    public function messages(): array
    {
        return [
            'category-name.en.required' => 'The English category name is required.',
            'category-icon.required' => 'The category icon is required.',
            'category-icon.image' => 'The category icon must be an image file.',
            'category-icon.mimes' => 'The category icon must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'category-icon.max' => 'The category icon must not be greater than 2MB.',
        ];
    }
}
