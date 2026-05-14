<?php

namespace App\Http\Requests\Admin\Showrooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateShowroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('showrooms.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:showrooms,id',

            // -- Name --
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',

            // -- Description -- (Optional)
            // FIX: Changed to nullable
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            // -- Address --
            'address' => 'required|array',
            'address.en' => 'required|string',
            'address.fr' => 'nullable|string',
            'address.ar' => 'nullable|string',

            // -- City --
            'city' => 'required|array',
            'city.en' => 'required|string',
            'city.fr' => 'nullable|string',
            'city.ar' => 'nullable|string',

            // -- State --
            'state' => 'required|array',
            'state.en' => 'required|string',
            'state.fr' => 'nullable|string',
            'state.ar' => 'nullable|string',

            // -- Country --
            'country' => 'required|array',
            'country.en' => 'required|string',
            'country.fr' => 'nullable|string',
            'country.ar' => 'nullable|string',

            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:255',
            'map_iframe' => 'nullable|string',

            // FIX: Changed 'json' to 'array'
            'opening_hours' => 'nullable|array',

            // FIX: Cover image should be nullable on Update (user might not change it)
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',

            'has_gallery_images' => 'nullable|boolean',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',

            // FIX: Removed Slug rule
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The showroom ID is required.',
            'id.exists' => 'The specified showroom does not exist.',
            'name.required' => 'The name field is required.',
            'name.en.required' => 'The English name is required.',
            'address.required' => 'The address field is required.',
            'address.en.required' => 'The English address is required.',
            'city.required' => 'The city field is required.',
            'city.en.required' => 'The English city is required.',
            'state.required' => 'The state field is required.',
            'state.en.required' => 'The English state is required.',
            'country.required' => 'The country field is required.',
            'country.en.required' => 'The English country is required.',
            'email.email' => 'The email must be a valid email address.',
            'cover_image.image' => 'The cover image must be an image file.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'cover_image.max' => 'The cover image may not be greater than 2MB.',
            'gallery_images.*.image' => 'Each gallery image must be an image file.',
            'gallery_images.*.mimes' => 'Each gallery image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'gallery_images.*.max' => 'Each gallery image may not be greater than 2MB.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
