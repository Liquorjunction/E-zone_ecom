<?php

namespace App\Http\Requests\Admin\ManageCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('manageCustomers.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
