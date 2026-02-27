<?php

namespace App\Http\Requests\Admin\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadContactUsRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('contact_us.read');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer',
        ];
    }
}
