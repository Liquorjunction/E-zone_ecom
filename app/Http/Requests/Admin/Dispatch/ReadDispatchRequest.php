<?php

namespace App\Http\Requests\Admin\Dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadDispatchRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('dispatch.read');
    }

    public function rules()
    {
        return [
            'status' => 'nullable|string|in:pending,dispatched,delivered,returned,cancelled',
            'order_number' => 'nullable|string|max:255',
        ];
    }
}
