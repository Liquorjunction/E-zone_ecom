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

            // Relations
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub-categories,id',

            // Images
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Specs
            'dimensions.en' => 'nullable|string',
            'specifications.en' => 'nullable|array',
            'warranty.en' => 'nullable|string',

            'status' => 'nullable|in:active,inactive',
            'is_bestseller' => 'nullable|boolean',

            // --- Variants ---
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id', // Null = New Variant

            'variants.*.name.en' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.attributes' => 'nullable|array',
        ];
    }
}
