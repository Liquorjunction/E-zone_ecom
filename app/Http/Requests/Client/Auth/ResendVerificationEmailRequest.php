<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can resend verification emails
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'payload' => 'required_without_all:token,email|string',
            'token' => 'required_without:payload|string|size:64',
            'email' => 'required_without:payload|email',
        ];
    }

    public function messages(): array
    {
        return [
            'payload.required_without_all' => 'Verification link is invalid or expired.',
            'token.required_without' => 'Verification token is required.',
            'token.size' => 'Invalid verification token format.',
            'email.required_without' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }
}
