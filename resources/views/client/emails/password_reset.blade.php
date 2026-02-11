@extends('client.emails.layout')

@section('title', config('app.name') . ' - Password Reset')
@section('header_title', 'Password Reset Request')
@section('header_subtitle', 'Secure Your Account')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        We received a request to reset the password for your {{ config('app.name') }} account. To proceed with resetting your password, please click the button below.
    </p>

    <p style="margin: 20px 0 0 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        <strong style="color: #1a1a1a;">Didn't request this?</strong> No worries! You can safely ignore this email and your account will remain secure.
    </p>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="border-radius: 8px; background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; box-shadow: 0 4px 12px rgba(0, 191, 255, 0.3);">
                            <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 16px 48px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; letter-spacing: 0.5px;">Reset Password</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Steps Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">

                <!-- Steps Title -->
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Next Steps</p>

                <!-- Step 1 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">1</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Click the "Reset Password" button above</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 2 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">2</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Create a strong password (minimum 8 characters)</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 3 -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">3</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Sign in with your new credentials</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Time Sensitive:</strong> This password reset link will expire in 60 minutes for security reasons. If it expires, you can request a new reset link from the login page.
                </p>
            </td>
        </tr>
    </table>

    <!-- Warning Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0 0 0; background-color: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #8b5a00; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Security Reminder:</strong> Never share this reset link with anyone. {{ config('app.name') }} will never ask for your password via email or phone.
                </p>
            </td>
        </tr>
    </table>
@endsection
