@extends('client.emails.layout')

@section('title', config('app.name') . ' - ' . statictranslations('Verification Code'))
@section('header_title', statictranslations('Email Verification'))
@section('header_subtitle', statictranslations('Verify your email address to complete your checkout'))

@section('content')
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 20px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">
        {{ statictranslations('Bonjour') }},
    </p>

    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        {{ statictranslations('You are placing a guest order at') }} <strong>{{ config('app.name') }}</strong>.
        {{ statictranslations('Please use the verification code below to verify your email address and complete your checkout.') }}
    </p>

    <!-- Verification Code Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; background-color: #f0f9ff; border: 2px dashed #00BFFF; border-radius: 12px; text-align: center;">
        <tr>
            <td style="padding: 28px 16px;">
                <p style="margin: 0 0 8px 0; padding: 0; color: #1a3a52; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-family: Arial, Helvetica, sans-serif;">
                    {{ statictranslations('Verification Code') }}
                </p>
                <p style="margin: 0; padding: 0; color: #0088cc; font-size: 36px; font-weight: 800; letter-spacing: 0.25em; font-family: 'Courier New', Courier, monospace;">
                    {{ $code }}
                </p>
                <p style="margin: 12px 0 0 0; padding: 0; color: #666666; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">
                    {{ statictranslations('This code will expire in 10 minutes.') }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin: 0 0 16px 0; padding: 0; color: #777777; font-size: 13px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
        {{ statictranslations('If you did not request this verification code, please ignore this email.') }}
    </p>

    <p style="margin: 0; padding: 0; color: #999999; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
        {{ statictranslations('Security Tip: Never share this verification code with anyone.') }}
    </p>
@endsection
