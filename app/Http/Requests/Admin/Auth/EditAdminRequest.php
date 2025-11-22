<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditAdminRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('user management.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:admin,id',
        ];
    }
}
