<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('role.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:roles,id',
        ];
    }
}
