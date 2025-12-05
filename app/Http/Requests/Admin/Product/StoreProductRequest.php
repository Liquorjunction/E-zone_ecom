<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('products.create');
    }

    public function rules(): array
    {
        return [
            // Basic Info
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',

            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            // Relations
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub-categories,id',

            // Pricing & Images
            'price' => 'required|numeric|min:0',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Required on create
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Complex JSON Structures
            'dimensions.en' => 'nullable|string',

            // Specifications
            'specifications.en' => 'nullable|array',
            'specifications.en.*.title' => 'required_with:specifications.en|string',
            'specifications.en.*.value' => 'required_with:specifications.en|string',

            // Variants Validation (FIXED)
            'variants' => 'nullable|array',
            // Validate that 'name' is an array containing 'en' key
            'variants.*.name.en' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',

            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => 'The English Product Name is required.',
            'variants.*.name.en.required' => 'All variants must have an English name.',
        ];
    }
}
