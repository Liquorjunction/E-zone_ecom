<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
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
                        @php
                            $imgpath = config('app.url') . '/icon/Ezone-Logo-New-2025-Website.png';
                        @endphp
                        <td style="background: linear-gradient(135deg, #00a4d6 0%, #0090c5 100%); background-color: #00a4d6; padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <img src="{{ $imgpath }}" alt="{{ config('app.name') }}" width="160" style="max-width: 160px; height: auto; display: block; margin: 0 auto 16px auto; border: 0;">
                            <h1 style="margin: 12px 0 6px 0; padding: 0; color: #ffffff; font-size: 26px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">@yield('header_title')</h1>
                            <p style="margin: 0; padding: 0; color: #ffffff; font-size: 14px; font-weight: 500; line-height: 1.5; opacity: 0.95;">@yield('header_subtitle')</p>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px 35px;">
                            @yield('content')
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
                                        <p style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Secure Encryption</p>
                                    </td>
                                    <td style="padding: 0 12px;">
                                        <p style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Privacy Protected</p>
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
