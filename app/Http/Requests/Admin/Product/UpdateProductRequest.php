<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('products.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:products,id',

            // Translatable fields
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',

            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            'dimensions.en' => 'nullable|string',

            // Specifications
            'specifications.en' => 'nullable|array',
            'specifications.en.*.title' => 'required_with:specifications.en|string',
            'specifications.en.*.value' => 'required_with:specifications.en|string',

            'warranty.en' => 'nullable|string',

            // Non-translatable fields
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub-categories,id',

            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',

            // Images (Nullable on update)
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_path.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Variants Validation (FIXED)
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            // Validate that 'name' is an array containing 'en' key
            'variants.*.name.en' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Product ID is missing.',
            'name.en.required' => 'Product Name (English) is required.',
            'variants.*.name.en.required' => 'All variants must have an English name.',
        ];
    }
}
