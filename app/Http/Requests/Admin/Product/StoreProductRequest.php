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
            // --- Product Level ---

            // Product Name Translatable
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            // Product Description Translatable
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            // Foreign Keys
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub-categories,id',

            // Images
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // New gallery field name used by repository — accept both for compatibility
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images_existing' => 'nullable|array',
            'gallery_images_variant_id' => 'nullable|integer',

            // Additional product details
            'dimensions.en' => 'nullable|string',
            'specifications.en' => 'nullable|array',
            'warranty.en' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'is_bestseller' => 'nullable|boolean',

            // --- Variant Level ---
            'variants' => 'required|array|min:1',
            'variants.*.name.en' => 'required|string',
            'variants.*.name.fr' => 'nullable|string',
            'variants.*.name.ar' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.attributes.en' => 'nullable|string',
            'variants.*.attributes.fr' => 'nullable|string',
            'variants.*.attributes.ar' => 'nullable|string',
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => 'The Product Name (English) is required.',
            'variants.required' => 'You must add at least one product variant.',
            'variants.*.name.en.required' => 'All variants must have a name.',
            'variants.*.price.required' => 'All variants must have a price.',
        ];
    }
}
