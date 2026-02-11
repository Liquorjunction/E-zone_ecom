<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f5f7fa; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <!-- Outer Table for Background -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin: 0; padding: 0; background-color: #f5f7fa;">
        <tr>
            <td style="padding: 40px 20px;">

                <!-- Main Container Table -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center"
                    style="margin: 0 auto; max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 164, 214, 0.08); border: 1px solid #e5e7eb;">

                    <!-- Header Section -->
                    <tr>
                        @php
                            $imgpath = config('app.url') . '/icon/Ezone-Logo-New-2025-Website.png';
                        @endphp
                        <td
                            style="background:linear-gradient(135deg,#1a3a52 0%,#0d1f2d 100%);background-color:#1a3a52;padding:40px 30px;text-align:center;border-radius:12px 12px 0 0;border-bottom:4px solid #00bfff">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                                style="margin:0 auto 24px auto">
                                <tbody>
                                    <tr>
                                        <td style="background-color:#ffffff;padding:16px 32px;border-radius:12px">
                                            <img src="{{ $imgpath }}" alt="{{ config('app.name') }}" width="200"
                                                style="max-width: 200px; height: auto; display: block; margin: 0 auto; border: 0;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h1
                                style="margin: 24px 0 8px 0; padding: 0; color: #ffffff; font-size: 28px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif; letter-spacing: -0.5px;">
                                @yield('header_title')</h1>
                            <p
                                style="margin: 0; padding: 0; color: #00BFFF; font-size: 14px; font-weight: 600; line-height: 1.5; letter-spacing: 0.5px; text-transform: uppercase;">
                                @yield('header_subtitle')</p>
                        </td>

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
                        <td
                            style="padding: 32px 35px; background-color: #f8fafb; text-align: center; border-radius: 0 0 12px 12px;">

                            <!-- Company Name -->
                            <p
                                style="margin: 0 0 16px 0; padding: 0; color: #1a1a1a; font-size: 16px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">
                                {{ config('app.name') }}
                            </p>

                            <!-- Tagline -->
                            <p
                                style="margin: 0 0 20px 0; padding: 0; color: #999999; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                Your Trusted Electronics Retailer
                            </p>

                            <!-- Trust Badges -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                                style="margin: 0 auto 24px auto;">
                                <tr>
                                    <td style="padding: 0 12px;">
                                        <p
                                            style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            Secure Encryption</p>
                                    </td>
                                    <td style="padding: 0 12px;">
                                        <p
                                            style="margin: 0; padding: 0; color: #666666; font-size: 13px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            Privacy Protected</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                style="margin: 0 0 20px 0;">
                                <tr>
                                    <td style="border-top: 1px solid #e5e7eb;"></td>
                                </tr>
                            </table>

                            <!-- Social Media Links -->
                            @php
                                $socialMedia = [];
                                $fbicon = config('app.url') . '/icon/fb.png';
                                $instaicon = config('app.url') . '/icon/insta.png';

                                if (!empty($siteContent->facebook)) {
                                    $socialMedia['facebook'] = [
                                        'url' => $siteContent->facebook,
                                        'icon' => '<img src="' . $fbicon . '" alt="Facebook" width="20" height="20">'
                                    ];
                                }

                                if (!empty($siteContent->instagram)) {
                                    $socialMedia['instagram'] = [
                                        'url' => $siteContent->instagram,
                                        'icon' => '<img src="' . $instaicon . '" alt="Instagram" width="20" height="20">'
                                    ];
                                }
                            @endphp
                            @if (count($socialMedia) > 0)
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                                    style="margin: 0 auto 24px auto;">
                                    <tr>
                                        @foreach ($socialMedia as $platform => $data)
                                            <td style="padding: 0 12px;">
                                                <a href="{{ $data['url'] }}" target="_blank"
                                                    title="{{ ucfirst($platform) }}"
                                                    style="color: #00BFFF; text-decoration: none; display: inline-block; line-height: 0;">
                                                    {!! $data['icon'] !!}
                                                </a>
                                            </td>
                                            @if (!$loop->last)
                                                <td style="padding: 0 8px; color: #e5e7eb;"></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                </table>
                            @endif

                            <!-- Footer Links -->
                            <p
                                style="margin: 14px 0; padding: 0; color: #777777; font-size: 12px; line-height: 1.8; font-family: Arial, Helvetica, sans-serif;">
                                <a href="{{ route('privacy-policy') }}"
                                    style="color: #00BFFF; text-decoration: none; font-weight: 600;">Privacy
                                    Policy</a>
                                &nbsp;•&nbsp;
                                <a href="{{ route('terms') }}"
                                    style="color: #00BFFF; text-decoration: none; font-weight: 600;">Terms
                                    of Service</a>
                                &nbsp;•&nbsp;
                                <a href="{{ route('contact') }}"
                                    style="color: #00BFFF; text-decoration: none; font-weight: 600;">Contact
                                    Support</a>
                            </p>

                            <!-- Copyright -->
                            <p
                                style="margin: 14px 0 0 0; padding: 0; color: #999999; font-size: 11px; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
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
