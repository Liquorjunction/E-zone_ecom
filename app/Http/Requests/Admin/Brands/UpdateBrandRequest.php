<?php

namespace App\Http\Requests\Admin\Brands;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('brands.update');
    }

    public function rules()
    {
        return [
            'id' => 'required',
            'name' => 'required',
            'name.en' => ['required', Rule::unique('brands', 'name->en')->ignore($this->id)],
            'name.ar' => 'nullable',
            'name.fr' => 'nullable',
            'tagline' => 'nullable|array',
            'tagline.en' => 'nullable|string',
            'tagline.fr' => 'nullable|string',
            'tagline.ar' => 'nullable|string',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'required|in:active,inactive',

            // Banners
            'banners' => 'nullable|array',
            'banners.*.id' => 'nullable|integer',
            'banners.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'banners.*.alt_text' => 'nullable|array',
            'banners.*.sort_order' => 'nullable|integer',
            'banners.*.status' => 'nullable|in:active,inactive',
            'banners.*._delete' => 'nullable',

            // Showcases
            'showcases' => 'nullable|array',
            'showcases.*.id' => 'nullable|integer',
            'showcases.*.category_id' => 'nullable',
            'showcases.*.sub_category_id' => 'nullable',
            'showcases.*.title' => 'nullable|array',
            'showcases.*.description' => 'nullable|array',
            'showcases.*.button_text' => 'nullable|array',
            'showcases.*.button_text.en' => 'nullable|string',
            'showcases.*.button_text.fr' => 'nullable|string',
            'showcases.*.button_text.ar' => 'nullable|string',
            'showcases.*.button_url' => 'nullable|string',
            'showcases.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'showcases.*.sort_order' => 'nullable|integer',
            'showcases.*.status' => 'nullable|in:active,inactive',
            'showcases.*._delete' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.en.unique' => 'Name must be unique',
            'name.required' => 'Name is required',
            'status.required' => 'Status is required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'name.en' => 'Name (en)',
            'image' => 'Image',
            'status' => 'Status',
        ];
    }
}
