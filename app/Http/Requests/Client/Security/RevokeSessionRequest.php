<?php

namespace App\Http\Requests\Client\Security;

use Illuminate\Foundation\Http\FormRequest;

class RevokeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required'],
        ];
    }
}
