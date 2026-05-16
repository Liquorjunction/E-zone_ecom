<?php

namespace App\Http\Requests\Admin\ManageCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BatchUpdateStatusCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('manageCustomers.update');
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'The customer IDs are required.',
            'ids.array' => 'The customer IDs must be an array.',
            'ids.*.exists' => 'One or more customers do not exist.',
            'status.required' => 'The status is required.',
            'status.boolean' => 'The status must be true or false.',
        ];
    }
}
