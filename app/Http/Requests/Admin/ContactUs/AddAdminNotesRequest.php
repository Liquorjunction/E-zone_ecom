<?php

namespace App\Http\Requests\Admin\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddAdminNotesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('contact_us.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contact_us,id',
            'status' => 'required|in:open,in_progress,closed',
            'admin_reply' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_reply.max' => 'The admin reply must not exceed 2000 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Enquiry ID',
            'status' => 'Status',
            'admin_reply' => 'Admin Reply',
        ];
    }
}
