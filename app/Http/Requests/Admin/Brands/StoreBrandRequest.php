<?php

namespace App\Http\Requests\Admin\Brands;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('brands.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'name.en' => ['required', Rule::unique('brands', 'name->en')],
            'name.ar' => 'nullable',
            'name.fr' => 'nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.en.unique' => 'Name must be unique',
            'name.required' => 'Name is required',
            'image.required' => 'Image is required',
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
