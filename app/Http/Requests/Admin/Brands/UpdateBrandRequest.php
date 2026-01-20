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
            'name' => 'required|unique:brands,name,' . $this->id,
            'name.en' => ['required', Rule::unique('brands', 'name->en')->ignore($this->id)],
            'name.ar' => 'required',
            'name.fr' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
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
