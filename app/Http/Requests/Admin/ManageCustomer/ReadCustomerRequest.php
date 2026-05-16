<?php

namespace App\Http\Requests\Admin\ManageCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadCustomerRequest extends FormRequest
{

    public function authorize()
    {
        return Auth::guard('admin')->user()->can('manageCustomers.read');
    }

    public function rules(): array
    {
        return [
            'id' => 'sometimes|exists:users,id',
        ];
    }
}
