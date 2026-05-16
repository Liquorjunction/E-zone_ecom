<?php

namespace App\Http\Requests\Admin\Showrooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShowroomRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('showrooms.create');
    }

    public function rules()
    {
        return [
            // -- Name -- Translatable (Mandatory Step 1)
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',

            // -- Description -- Translatable (Optional Step 2)
            // FIX: Changed from 'required' to 'nullable'
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            // -- Address -- Translatable (Mandatory Step 1)
            'address' => 'required|array',
            'address.en' => 'required|string',
            'address.fr' => 'nullable|string',
            'address.ar' => 'nullable|string',

            // -- City -- Translatable (Mandatory Step 1)
            'city' => 'required|array',
            'city.en' => 'required|string',
            'city.fr' => 'nullable|string',
            'city.ar' => 'nullable|string',

            // -- State -- Translatable (Mandatory Step 1)
            'state' => 'required|array',
            'state.en' => 'required|string',
            'state.fr' => 'nullable|string',
            'state.ar' => 'nullable|string',

            // -- Country -- Translatable (Mandatory Step 1)
            'country' => 'required|array',
            'country.en' => 'required|string',
            'country.fr' => 'nullable|string',
            'country.ar' => 'nullable|string',

            // Non-Translatable Fields
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:255',
            'map_iframe' => 'nullable|string',

            // FIX: Changed 'json' to 'array' because standard HTML forms submit arrays
            'opening_hours' => 'nullable|array',

            'gallery_images' => 'nullable|array|max:5',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',

            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048', // Kept required for Step 1

            // FIX: Removed 'slug' rule entirely. The Model generates this automatically.
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'gallery_images.max' => 'You may upload up to 5 gallery images only.',
            'gallery_images.array' => 'The gallery images field must be a valid list of images.',
            'gallery_images.*.uploaded' => 'Each gallery image may not be greater than 2MB.',
            'gallery_images.*.max' => 'Each gallery image may not be greater than 2MB.',
        ];
    }

    public function attributes()
    {
        return [
            'name.en' => 'English Name',
            'address.en' => 'English Address',
            'city.en' => 'English City',
            'state.en' => 'English State',
            'country.en' => 'English Country',
            'description.en' => 'English Description',
        ];
    }
}
