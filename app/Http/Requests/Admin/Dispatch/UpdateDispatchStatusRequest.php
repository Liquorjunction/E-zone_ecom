<?php

namespace App\Http\Requests\Admin\Dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDispatchStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('dispatch.update');
    }

    public function rules()
    {
        return [
            'status' => 'required|string|in:pending,dispatched,delivered,returned,cancelled',
        ];
    }
}
