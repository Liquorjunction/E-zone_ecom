<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('products.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Validation rules

            'id' => 'required|exists:products,id',

            // -- Translatable fields --
            'name' => 'required|array',
            'name.en' => 'required|string',
            'name.fr' => 'nullable|string',
            'name.ar' => 'nullable|string',

            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            'dimensions' => 'required|array',
            'dimensions.en' => 'required|string',
            'dimensions.fr' => 'nullable|string',
            'dimensions.ar' => 'nullable|string',

            'specifications' => 'required|array',
            'specifications.en' => 'required|string',
            'specifications.fr' => 'nullable|string',
            'specifications.ar' => 'nullable|string',

            'warrenty' => 'required|array',
            'warrenty.en' => 'required|string',
            'warrenty.fr' => 'nullable|string',
            'warrenty.ar' => 'nullable|string',
            //  -- End translatable fields --

            // -- Non-translatable fields --
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|numeric',
            // -- End non-translatable fields --

            // -- Product Images --
            'image_path' => 'nullable',
            'image_path.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            // -- End Product Images --

            // -- Product Variants --
            'variants' => 'required|array',
            'variants.*.name' => 'required|array',
            'variants.*.name.en' => 'required|string',
            'variants.*.name.fr' => 'nullable|string',
            'variants.*.name.ar' => 'nullable|string',
            'variants.*.price' => 'required|numeric',
            // -- End Product Variants --
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'The id is required.',
            // Validation messages
            'name.en.required' => 'The English name is required.',
            'description.en.required' => 'The English description is required.',
            'dimensions.en.required' => 'The English dimensions are required.',
            'specifications.en.required' => 'The English specifications are required.',
            'warrenty.en.required' => 'The English warrenty is required.',

            'variants.*.name.en.required' => 'The English variant name is required.',

            'cover_image.required' => 'The cover image is required.',
            'cover_image.image' => 'The cover image must be an image.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif, svg.',
            'cover_image.max' => 'The cover image may not be greater than 2MB.',

            'image_path.*.image' => 'The image must be an image.',
            'image_path.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'image_path.*.max' => 'The image may not be greater than 2MB.',

            'variants.*.price.required' => 'The variant price is required.',
            'variants.*.price.numeric' => 'The variant price must be a number.',

            'brand_id.required' => 'The brand is required.',
            'category_id.required' => 'The category is required.',
            'sub_category_id.required' => 'The sub category is required.',

            'variants.*.name.*.required' => 'The variant name is required.',
            'variants.*.name.*.string' => 'The variant name must be a string.',
            'variants.*.price.required' => 'The variant price is required.',
            'variants.*.price.numeric' => 'The variant price must be a number.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'dimensions' => 'dimensions',
            'specifications' => 'specifications',
            'warrenty' => 'warrenty',
            'variants' => 'variants',
            'variants.*.name' => 'variant name',
            'variants.*.price' => 'variant price',
        ];
    }
}
