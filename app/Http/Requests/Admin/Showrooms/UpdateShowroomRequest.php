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
            'mapIframe' => 'nullable|string',

            // FIX: Changed 'json' to 'array'
            'opening_hours' => 'nullable|array',

            // FIX: Cover image should be nullable on Update (user might not change it)
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',

            // FIX: Removed Slug rule
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
