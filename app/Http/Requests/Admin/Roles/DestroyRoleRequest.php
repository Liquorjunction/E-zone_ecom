<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('role.delete');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:roles,id',
        ];
    }
}
