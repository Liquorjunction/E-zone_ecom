<?php

namespace App\Http\Requests\Admin\AboutUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadAboutUsRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('about_us.read');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'sometimes|exists:about_us,id',
        ];
    }
}
