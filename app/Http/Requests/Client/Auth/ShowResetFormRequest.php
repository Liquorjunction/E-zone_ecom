<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ShowResetFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payload' => 'required_without:token,email|string',
            'token' => 'required_without:payload|string|size:64',
            'email' => 'required_without:payload|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'payload.required_without' => 'Reset link is invalid or expired.',
            'token.required_without' => 'Reset token is required.',
            'token.size' => 'Invalid reset token format.',
            'email.required_without' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.exists' => 'If your email is registered, a reset link has been sent.',
        ];
    }
}
