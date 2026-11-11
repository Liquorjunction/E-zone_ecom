<?php

namespace App\Http\Requests\Client\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrderCheckoutRequest extends FormRequest
{
    public function authorize()
    {
        // Allow both guests and authenticated users to reach checkout
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cardNumber' => $this->cardNumber ? str_replace(' ', '', $this->cardNumber) : null,
            'cvv' => $this->cvv ? preg_replace('/\D/', '', $this->cvv) : null,
        ]);

        if ($this->filled('billing_address_id') && !$this->filled('saved_billing_address_id')) {
            $this->merge([
                'saved_billing_address_id' => $this->input('billing_address_id'),
            ]);
        }
    }

    public function rules()
    {
        $paymentMethods = ['card', 'paypal', 'cod', 'cash', 'bank_transfer', 'other'];

        return [
            'shipping_method' => ['nullable', 'string', Rule::in(['standard', 'express'])],

            'billing_same_as_shipping' => ['nullable', 'boolean'],

            'saved_address_id' => [
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where(fn($q) => $q->where('user_id', Auth::id() ?? 0)),
                Rule::prohibitedIf(!Auth::check()),
            ],
            'saved_billing_address_id' => [
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where(fn($q) => $q->where('user_id', Auth::id() ?? 0)),
                Rule::prohibitedIf(!Auth::check()),
            ],
            'save_address' => ['sometimes', 'boolean'],

            'firstName' => ['required_without_all:shipping_address,saved_address_id', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'address' => ['required_without_all:shipping_address,saved_address_id', 'string', 'max:255'],
            'city' => ['required_without_all:shipping_address,saved_address_id', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'regex:/^\d{1,10}$/'],
            'postal_code' => ['nullable', 'regex:/^\d{1,10}$/'],
            'country' => ['required_without_all:shipping_address,saved_address_id', 'string', 'max:255'],
            'phone' => Auth::check() ? ['nullable', 'string', 'max:20'] : ['required', 'string', 'max:20'],

            'shipping_address' => ['nullable', 'array'],
            'shipping_address.name' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.street' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.city' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.state' => ['nullable', 'string', 'max:255'],
            'shipping_address.postal_code' => ['nullable', 'regex:/^\d{1,10}$/'],
            'shipping_address.country' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.phone' => ['nullable', 'string', 'max:20'],

            'billing_address' => ['nullable', 'array'],
            'billing_address.name' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.street' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.state' => ['nullable', 'string', 'max:255'],
            'billing_address.postal_code' => ['nullable', 'regex:/^\d{1,10}$/'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.phone' => ['nullable', 'string', 'max:20'],

            'billing_firstName' => ['required_without_all:billing_address,billing_same_as_shipping', 'string', 'max:255'],
            'billing_lastName' => ['nullable', 'string', 'max:255'],
            'billing_address_line' => ['required_without_all:billing_address,billing_same_as_shipping', 'string', 'max:255'],
            'billing_city' => ['required_without_all:billing_address,billing_same_as_shipping', 'string', 'max:255'],
            'billing_state' => ['nullable', 'string', 'max:255'],
            'billing_zip' => ['nullable', 'regex:/^\d{1,10}$/'],
            'billing_postal_code' => ['nullable', 'regex:/^\d{1,10}$/'],
            'billing_country' => ['required_without_all:billing_address,billing_same_as_shipping', 'string', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:20'],

            'email' => Auth::check() ? ['nullable', 'email'] : ['required', 'email'],

            'payment_method' => ['required_without:payment', 'string', Rule::in($paymentMethods)],
            'payment' => ['nullable', 'string', Rule::in($paymentMethods)],

            'cardNumber' => ['nullable', 'string', 'digits_between:12,19', 'required_if:payment_method,card', 'required_if:payment,card'],
            'expiry' => ['nullable', 'string', 'required_if:payment_method,card', 'required_if:payment,card'],
            'cvv' => ['nullable', 'string', 'digits_between:3,4', 'required_if:payment_method,card', 'required_if:payment,card'],

            'g-recaptcha-response' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!Auth::check()) {
                $submittedEmail = strtolower(trim((string) $this->input('email', '')));

                // 1. Block registered users from creating guest orders
                if (!empty($submittedEmail) && \App\Models\User::where('email', $submittedEmail)->exists()) {
                    $validator->errors()->add('email', statictranslations('You are already registered. Please log in to your account to place an order.'));
                }

                // 2. Mandatory Email OTP Verification Check
                $verifiedEmail  = (string) session('guest_email_verified', '');

                if (empty($submittedEmail)) {
                    $validator->errors()->add('email', statictranslations('Email address is required.'));
                } elseif (empty($verifiedEmail) || $verifiedEmail !== $submittedEmail) {
                    $validator->errors()->add('email', statictranslations('Please verify your email address with the verification code sent to your inbox before placing the order.'));
                }

                // 2. Mandatory CAPTCHA Verification Check (Google reCAPTCHA)
                $secret = config('services.recaptcha.secret_key');
                $token  = (string) $this->input('g-recaptcha-response', '');

                if (!empty($secret)) {
                    if (empty($token)) {
                        $validator->errors()->add('recaptcha', statictranslations('Please complete the security CAPTCHA verification before proceeding.'));
                    } elseif (!$this->verifyRecaptcha($token, $secret)) {
                        $validator->errors()->add('recaptcha', statictranslations('Security CAPTCHA verification failed. Please try again.'));
                    }
                }
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $submittedEmail = strtolower(trim((string) $this->input('email', '')));
            $isRegistered = !Auth::check() && !empty($submittedEmail) && \App\Models\User::where('email', $submittedEmail)->exists();

            $response = response()->json([
                'success'       => false,
                'is_registered' => $isRegistered,
                'message'       => $validator->errors()->first(),
                'errors'        => $validator->errors(),
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    protected function verifyRecaptcha(string $token, string $secret): bool
    {
        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $json = $response->json();
            $success = (bool) ($json['success'] ?? false);
            if (!$success) {
                Log::warning('reCAPTCHA siteverify rejected token from Google', [
                    'json' => $json,
                    'token_preview' => substr($token, 0, 15) . '...',
                    'ip' => $this->ip(),
                ]);
            }

            return $success;
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification request error: ' . $e->getMessage());
            return app()->environment('local', 'testing');
        }
    }

    // protected function prepareForValidation(): void
    // {
    //     if ($this->filled('billing_address_id') && !$this->filled('saved_billing_address_id')) {
    //         $this->merge([
    //             'saved_billing_address_id' => $this->input('billing_address_id'),
    //         ]);
    //     }
    // }

    public function messages(): array
    {
        return [
            'firstName.required_without_all' => 'First name is required.',
            // 'lastName.required_without_all' => 'Last name is required.',
            'address.required_without_all' => 'Address is required.',
            'city.required_without_all' => 'City is required.',
            'country.required_without_all' => 'Country is required.',

            'billing_firstName.required_without_all' => 'Billing first name is required.',
            // 'billing_lastName.required_without_all' => 'Billing last name is required.',
            'billing_address_line.required_without_all' => 'Billing address is required.',
            'billing_city.required_without_all' => 'Billing city is required.',
            'billing_country.required_without_all' => 'Billing country is required.',
            '*.regex' => 'The postal code must be up to 10 digits.',

            'phone.required' => 'Phone number is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',

            'payment_method.required_without' => 'Please select a payment method.',
            'cardNumber.required_if' => 'Card number is required when paying by card.',
            'cardNumber.digits_between' => 'Card number must be between 12 and 19 digits.',
            'expiry.required_if' => 'Expiry date is required when paying by card.',
            'cvv.required_if' => 'CVV is required when paying by card.',
            'cvv.digits_between' => 'CVV must be 3 or 4 digits.',
        ];
    }
}
