<?php

namespace App\Http\Requests\Client\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadAddressRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'id' => ['required','exists:addresses,id'],
            'type' => ['nullable','in:billing,shipping'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('type')) {
            $t = strtolower($this->input('type'));
            if (in_array($t, ['home','work','other'])) {
                $t = 'shipping';
            }
            $this->merge(['type' => $t]);
        }
    }
}
