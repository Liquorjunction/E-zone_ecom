<?php

namespace App\Http\Requests\Admin\Brands;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadBrandRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('brands.read');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|exists:brands,id',
        ];
    }
}
