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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp,ico,tiff,avif|max:2048',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp,ico,tiff,avif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp,ico,tiff,avif|max:2048',
            'gallery_images_existing' => 'nullable|array',
            'gallery_images_variant_id' => 'nullable|integer',

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
            'variants.*.name.fr' => 'nullable|string',
            'variants.*.name.ar' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.attributes.en' => 'nullable|string',
            'variants.*.attributes.fr' => 'nullable|string',
            'variants.*.attributes.ar' => 'nullable|string',
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function passedValidation()
    {
        $data = $this->validated();
        if (!empty($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $i => $variant) {
                if (isset($variant['attributes'])) {
                    $attrs = $variant['attributes'];
                    if (is_string($attrs)) {
                        $decoded = json_decode($attrs, true);
                        if (is_array($decoded)) {
                            $attrs = $decoded;
                        } else {
                            $attrs = [$attrs];
                        }
                    }
                    if (is_array($attrs)) {
                        $normalized = [];
                        foreach ($attrs as $k => $v) {
                            if (is_int($k) && is_array($v) && (isset($v['key']) || isset($v['value']))) {
                                $normalized[] = $v;
                            } elseif (is_string($k)) {
                                $normalized[] = ['key' => (string)$k, 'value' => $v];
                            } else {
                                $normalized[] = ['key' => (string)$k, 'value' => (string)$v];
                            }
                        }
                        $data['variants'][$i]['attributes'] = $normalized;
                    }
                }
            }
        }
        $this->replace(array_merge($this->all(), $data));
    }
}
