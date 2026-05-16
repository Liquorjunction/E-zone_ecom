<?php

namespace App\Http\Requests\Admin\Dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditDispatchRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('dispatch.update');
    }

    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
        ];
    }
}
