<?php

namespace App\Http\Requests\Admin\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditContactUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('contact_us.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contact_us,id',
        ];
    }
}
