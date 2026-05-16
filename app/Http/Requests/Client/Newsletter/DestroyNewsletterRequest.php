<?php

namespace App\Http\Requests\Client\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class DestroyNewsletterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:newsletter,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.exists' => 'This email is not subscribed to the newsletter.',
        ];
    }
}
