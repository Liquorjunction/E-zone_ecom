<?php

namespace App\Http\Requests\Client\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'id' => ['nullable','exists:addresses,id'],
            'type' => ['required','in:billing,shipping'],
            'is_default' => ['nullable','boolean'],
            'address_label' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'name' => ['required','string','max:255'],
            'street' => ['required','string','max:255'],
            'city' => ['required','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'postal_code' => ['nullable','string','max:20'],
            'country' => ['required','string','max:255'],
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
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be billing or shipping.',
            'is_default.boolean' => 'The is default must be a boolean.',
            'address_label.max' => 'The address label must not be greater than 255 characters.',
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not be greater than 255 characters.',
            'street.required' => 'The street field is required.',
            'street.max' => 'The street must not be greater than 255 characters.',
            'city.required' => 'The city field is required.',
            'city.max' => 'The city must not be greater than 255 characters.',
            'state.max' => 'The state must not be greater than 255 characters.',
            'postal_code.max' => 'The postal code must not be greater than 20 characters.',
            'country.required' => 'The country field is required.',
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
            'city' => 'city',
            'state' => 'state',
            'postal_code' => 'postal code',
            'country' => 'country',
        ];
    }
}
