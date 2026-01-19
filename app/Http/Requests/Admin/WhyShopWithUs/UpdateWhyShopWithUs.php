<?php

namespace App\Http\Requests\Admin\WhyShopWithUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateWhyShopWithUs extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('why_choose_us.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:why_shop_with_us,id',
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.ar' => 'nullable|string',
            'title.fr' => 'nullable|string',
            'description' => 'required|array',
            'description.en' => 'required|string|max:150',
            'description.ar' => 'nullable|string',
            'description.fr' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The ID is required.',
            'id.exists' => 'The selected ID is invalid.',
            'title.required' => 'The title is required.',
            'title.en.required' => 'The title in English is required.',
            'description.required' => 'The description is required.',
            'description.en.required' => 'The description in English is required.',
            'description.en.max' => 'The description in English may not be greater than 150 characters.',
            'icon.image' => 'The icon must be an image.',
            'icon.mimes' => 'The icon must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'icon.max' => 'The icon may not be greater than 2MB.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID',
            'title' => 'title',
            'description' => 'description',
            'icon' => 'icon',
        ];
    }
}
