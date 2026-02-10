<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} - Password Reset</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>

<body style="margin: 0; padding: 0; background-color: #f5f7fa; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <!-- Outer Table for Background -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; padding: 0; background-color: #f5f7fa;">
        <tr>
            <td style="padding: 40px 20px;">

                <!-- Main Container Table -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center" style="margin: 0 auto; max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 164, 214, 0.08); border: 1px solid #e5e7eb;">

                    <!-- Header Section -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6; padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="{{ config('app.name') }}" width="160" style="max-width: 160px; height: auto; display: block; margin: 0 auto 16px auto; border: 0;">
                            <h1 style="margin: 12px 0 6px 0; padding: 0; color: #ffffff; font-size: 26px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Password Reset Request</h1>
                            <p style="margin: 0; padding: 0; color: #ffffff; font-size: 14px; font-weight: 500; line-height: 1.5; opacity: 0.95;">Secure Your Account</p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px 35px;">

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
                                                <td style="border-radius: 8px; background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6;">
                                                    <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 16px 48px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; font-family: Arial, Helvetica, sans-serif; border-radius: 8px;">Reset Password</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Steps Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9fc; border: 1px solid #d6f1fa; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 24px;">

                                        <!-- Steps Title -->
                                        <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">✓ Next Steps</p>

                                        <!-- Step 1 -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                                            <tr>
                                                <td width="32" valign="top" style="padding-right: 14px;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                                        <tr>
                                                            <td align="center" valign="middle" style="background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">1</td>
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
                                                            <td align="center" valign="middle" style="background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">2</td>
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
                                                            <td align="center" valign="middle" style="background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">3</td>
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
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0; background-color: #f0f9fc; border-left: 4px solid #00a4d6; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <p style="margin: 0; padding: 0; color: #005f7f; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                                            <strong>⏱ Time Sensitive:</strong> This password reset link will expire in 60 minutes for security reasons. If it expires, you can request a new reset link from the login page.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback Link Label -->
                            <p style="margin: 24px 0 14px 0; padding: 0; color: #999999; font-size: 13px; text-align: center; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                Button not working? Copy and paste this link into your browser:
                            </p>

                            <!-- Fallback Link Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f0f9fc; border: 2px solid #d6f1fa; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 16px; word-break: break-all;">
                                        <a href="{{ $resetUrl }}" style="color: #00a4d6; text-decoration: none; font-size: 12px; font-family: 'Courier New', monospace;">{{ $resetUrl }}</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0 0 0; background-color: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 16px 18px;">
                                        <p style="margin: 0; padding: 0; color: #8b5a00; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                                            <strong>🔒 Security Reminder:</strong> Never share this reset link with anyone. {{ config('app.name') }} will never ask for your password via email or phone.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="border-top: 1px solid #e5e7eb;"></td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td style="padding: 32px 35px; background-color: #f8fafb; text-align: center; border-radius: 0 0 12px 12px;">

                            <!-- Company Name -->
                            <p style="margin: 0 0 16px 0; padding: 0; color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">
                                {{ config('app.name') }}
                            </p>

                            <!-- Tagline -->
                            <p style="margin: 0 0 20px 0; padding: 0; color: #999999; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                Your Trusted Electronics Retailer
                            </p>

                            <!-- Trust Badges -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 24px auto;">
                                <tr>
                                    <td style="padding: 0 12px;">
                                        <p style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">🛡️ Secure Encryption</p>
                                    </td>
                                    <td style="padding: 0 12px;">
                                        <p style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">🔒 Privacy Protected</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 20px 0;">
                                <tr>
                                    <td style="border-top: 1px solid #e5e7eb;"></td>
                                </tr>
                            </table>

                            <!-- Footer Links -->
                            <p style="margin: 14px 0; padding: 0; color: #777777; font-size: 12px; line-height: 1.8; font-family: Arial, Helvetica, sans-serif;">
                                <a href="{{ route('privacy-policy') }}" style="color: #00a4d6; text-decoration: none; font-weight: 500;">Privacy Policy</a>
                                &nbsp;•&nbsp;
                                <a href="{{ route('terms') }}" style="color: #00a4d6; text-decoration: none; font-weight: 500;">Terms of Service</a>
                                &nbsp;•&nbsp;
                                <a href="{{ route('contact') }}" style="color: #00a4d6; text-decoration: none; font-weight: 500;">Contact Support</a>
                            </p>

                            <!-- Copyright -->
                            <p style="margin: 14px 0 0 0; padding: 0; color: #999999; font-size: 11px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                © {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                                <br>
                                This is an automated message. Please do not reply to this email.
                            </p>

                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>
    <!-- End Outer Table -->

</body>

</html>
