<?php

namespace App\Http\Requests\Client\PrivacyPolicy;

use Illuminate\Foundation\Http\FormRequest;

class ReadPrivacyPolicyRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Client-side, no specific authorization needed for reading
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Add any necessary validation rules for client-side reading
        ];
    }
}
