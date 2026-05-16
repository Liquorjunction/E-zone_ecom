<?php

namespace App\Http\Requests\Admin\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyNewsletterRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('newsletter.delete');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:newsletter,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The id field is required.',
            'id.exists' => 'The selected id is invalid.',
        ];
    }
}
