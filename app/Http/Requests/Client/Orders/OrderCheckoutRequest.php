<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderCheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'shipping_address' => ['nullable','array'],
            // Payment
            'payment_method' => ['required','string','in:card,paypal,cod,cash,bank_transfer,other'],

            // Card fields: required only when `payment_method` is `card`
            'cardNumber' => ['nullable','string','required_if:payment_method,card','digits_between:12,19'],
            'expiry' => ['nullable','string','required_if:payment_method,card'],
            'cvv' => ['nullable','string','required_if:payment_method,card','digits_between:3,4'],

            'phone' => ['nullable','string'],
        ];
    }
}
