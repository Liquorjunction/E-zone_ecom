<?php

namespace App\Http\Requests\Admin\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateContactUsStatus extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('contact_us.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contact_us,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The Enquiry ID is required.',
            'id.integer' => 'The Enquiry ID must be an integer.',
            'id.exists' => 'The specified Enquiry does not exist.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: open, in_progress, resolved, closed.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Enquiry ID',
            'status' => 'Status',
        ];
    }
}
