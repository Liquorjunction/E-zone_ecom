<?php

namespace App\Http\Requests\Client\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAddressRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'type' => ['nullable','in:billing,shipping'],
            'is_default' => ['nullable','boolean'],
            'address_label' => ['nullable','string','max:255'],
            'name' => ['nullable','string','max:255'],
            'street' => ['nullable','string','max:255'],
            'city_id' => ['nullable','exists:cities,id'],
            'state' => ['nullable','string','max:255'],
            'postal_code' => ['nullable','regex:/^\d{1,10}$/'],
            'country' => ['nullable','string','max:255'],
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

    public function messages()
    {
        return [
            'type.in' => 'The type must be billing or shipping.',
            'is_default.boolean' => 'The is default must be a boolean.',
            'address_label.max' => 'The address label must not be greater than 255 characters.',
            'name.max' => 'The name must not be greater than 255 characters.',
            'street.max' => 'The street must not be greater than 255 characters.',
            'city_id.exists' => 'The selected city is invalid.',
            'state.max' => 'The state must not be greater than 255 characters.',
            'postal_code.regex' => 'The postal code must be up to 10 digits.',
            'country.max' => 'The country must not be greater than 255 characters.',
        ];
    }

    public function attributes()
    {
        return [
            'type' => 'type',
            'is_default' => 'is default',
            'address_label' => 'address label',
            'name' => 'name',
            'street' => 'street',
            'city_id' => 'city',
            'state' => 'state',
            'postal_code' => 'postal code',
            'country' => 'country',
        ];
    }
}
