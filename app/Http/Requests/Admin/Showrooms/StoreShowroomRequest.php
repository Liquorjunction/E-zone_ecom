<?php

namespace App\Http\Requests\Admin\Showrooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShowroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('showrooms.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Translatable Field

            // -- Name -- Translatable
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            // -- Description -- Translatable
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',
            // -- Address -- Translatable
            'address' => 'required|array',
            'address.en' => 'required|string',
            'address.fr' => 'nullable|string',
            'address.ar' => 'nullable|string',
            // -- City -- Translatable
            'city' => 'required|array',
            'city.en' => 'required|string',
            'city.fr' => 'nullable|string',
            'city.ar' => 'nullable|string',
            // -- State -- Translatable
            'state' => 'required|array',
            'state.en' => 'required|string',
            'state.fr' => 'nullable|string',
            'state.ar' => 'nullable|string',
            // -- Country -- Translatable
            'country' => 'required|array',
            'country.en' => 'required|string',
            'country.fr' => 'nullable|string',
            'country.ar' => 'nullable|string',

            // Non-Translatable Fields

            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:255',
            'mapIframe' => 'nullable|string',
            'opening_hours' => 'nullable|json',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'slug' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The showroom name field is required.',
            'name.en.required' => 'The showroom name field is required.',
            'description.required' => 'The showroom description field is required.',
            'description.en.required' => 'The showroom description field is required.',
            'address.required' => 'The showroom address field is required.',
            'address.en.required' => 'The showroom address field is required.',
            'city.required' => 'The showroom city field is required.',
            'city.en.required' => 'The showroom city field is required.',
            'state.required' => 'The showroom state field is required.',
            'state.en.required' => 'The showroom state field is required.',
            'country.required' => 'The showroom country field is required.',
            'country.en.required' => 'The showroom country field is required.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'showroom name',
            'description' => 'showroom description',
            'address' => 'showroom address',
            'city' => 'showroom city',
            'state' => 'showroom state',
            'country' => 'showroom country',
            'slug' => 'showroom slug',
            'status' => 'showroom status',
            'cover_image' => 'showroom cover image',
            'opening_hours' => 'showroom opening hours',
            'mapIframe' => 'showroom map iframe',
            'pincode' => 'showroom pincode',
            'phone' => 'showroom phone',
            'email' => 'showroom email',
        ];
    }
}
