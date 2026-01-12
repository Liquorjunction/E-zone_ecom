<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderCheckoutRequest extends FormRequest
{
    public function authorize()
    {
        // Allow both guests and authenticated users to reach checkout
        return true;
    }

    public function rules()
    {
        $paymentMethods = ['card','paypal','cod','cash','bank_transfer','other'];

        return [
            'shipping_method' => ['nullable','string', Rule::in(['standard','express'])],

            'billing_same_as_shipping' => ['nullable','boolean'],

            'saved_address_id' => [
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where(fn ($q) => $q->where('user_id', Auth::id() ?? 0)),
                Rule::prohibitedIf(! Auth::check()),
            ],
            'save_address' => ['sometimes','boolean'],

            'firstName' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'lastName' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'address' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'city' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'state' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'zip' => ['required_without_all:shipping_address,saved_address_id','string','max:20'],
            'postal_code' => ['nullable','string','max:20'],
            'country' => ['required_without_all:shipping_address,saved_address_id','string','max:255'],
            'phone' => ['nullable','string','max:20'],
            'phone_no' => ['nullable','string','max:20'],

            'shipping_address' => ['nullable','array'],
            'shipping_address.name' => ['required_with:shipping_address','string','max:255'],
            'shipping_address.street' => ['required_with:shipping_address','string','max:255'],
            'shipping_address.city' => ['required_with:shipping_address','string','max:255'],
            'shipping_address.state' => ['required_with:shipping_address','string','max:255'],
            'shipping_address.postal_code' => ['required_with:shipping_address','string','max:20'],
            'shipping_address.country' => ['required_with:shipping_address','string','max:255'],
            'shipping_address.phone' => ['nullable','string','max:20'],

            'billing_address' => ['nullable','array'],
            'billing_address.name' => ['required_with:billing_address','string','max:255'],
            'billing_address.street' => ['required_with:billing_address','string','max:255'],
            'billing_address.city' => ['required_with:billing_address','string','max:255'],
            'billing_address.state' => ['required_with:billing_address','string','max:255'],
            'billing_address.postal_code' => ['required_with:billing_address','string','max:20'],
            'billing_address.country' => ['required_with:billing_address','string','max:255'],
            'billing_address.phone' => ['nullable','string','max:20'],

            'billing_firstName' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_lastName' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_address_line' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_city' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_state' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_zip' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:20'],
            'billing_postal_code' => ['nullable','string','max:20'],
            'billing_country' => ['required_without_all:billing_address,billing_same_as_shipping','string','max:255'],
            'billing_phone' => ['nullable','string','max:20'],

            'email' => Auth::check() ? ['nullable','email'] : ['required','email'],

            'payment_method' => ['required_without:payment','string', Rule::in($paymentMethods)],
            'payment' => ['nullable','string', Rule::in($paymentMethods)],

            'cardNumber' => ['nullable','string','digits_between:12,19','required_if:payment_method,card','required_if:payment,card'],
            'expiry' => ['nullable','string','required_if:payment_method,card','required_if:payment,card'],
            'cvv' => ['nullable','string','digits_between:3,4','required_if:payment_method,card','required_if:payment,card'],
        ];
    }
}
