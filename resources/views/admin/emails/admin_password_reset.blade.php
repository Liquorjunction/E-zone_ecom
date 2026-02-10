<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin Password Reset</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu',
                'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #c62828 0%, #ad1457 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .message {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .cta-button {
            display: inline-block;
            background-color: #c62828;
            color: white;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 30px 0;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #ad1457;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .code-box {
            background-color: #f9f9f9;
            border-left: 4px solid #c62828;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            color: #333;
            font-size: 13px;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 13px;
        }

        .info {
            background-color: #ffebee;
            border: 1px solid #ef5350;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 13px;
        }

        .critical {
            background-color: #ffe0e0;
            border: 2px solid #c62828;
            color: #7f0000;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
            font-weight: 500;
        }

        .footer {
            background-color: #f9f9f9;
            padding: 25px 30px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        .footer-link {
            color: #c62828;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        .divider {
            border: 0;
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }

        .steps {
            margin: 25px 0;
        }

        .step {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: #c62828;
            color: white;
            border-radius: 50%;
            font-weight: 600;
            flex-shrink: 0;
            margin-right: 12px;
        }

        .step-text {
            font-size: 14px;
            color: #555;
            padding-top: 3px;
        }

        @media (max-width: 600px) {
            .content {
                padding: 25px 20px;
            }

            .greeting {
                font-size: 18px;
            }

            .message {
                font-size: 13px;
            }

            .cta-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body style="background-color: #f5f5f5;">
    <div style="background-color: #f5f5f5; padding: 20px;">
        <div class="email-container" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

            <!-- Header -->
            <div class="header" style="background: linear-gradient(135deg, #c62828 0%, #ad1457 100%); padding: 30px 20px; text-align: center;">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="{{ config('app.name') }}" class="logo"
                    style="max-width: 120px; height: auto; display: inline-block;">
                <h1 style="color: white; font-size: 24px; margin-top: 10px; font-weight: 700;">Admin Password Reset</h1>
            </div>

            <!-- Content -->
            <div class="content" style="padding: 40px 30px;">

                <p class="greeting" style="font-size: 20px; font-weight: 600; color: #1a1a1a; margin-bottom: 15px;">
                    Hello {{ $user->name }},
                </p>

                <p class="message" style="font-size: 14px; color: #555; margin-bottom: 20px; line-height: 1.8;">
                    A password reset request has been initiated for your admin account. If you did not request this, <strong>please
                        contact your system administrator immediately</strong>.
                </p>

                <p class="message" style="font-size: 14px; color: #555; margin-bottom: 20px; line-height: 1.8;">
                    To reset your admin password, click the button below:
                </p>

                <!-- CTA Button -->
                <div class="button-container" style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" class="cta-button"
                        style="display: inline-block; background-color: #c62828; color: white; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                        Reset Admin Password
                    </a>
                </div>

                <!-- Fallback Link -->
                <p style="font-size: 13px; color: #999; text-align: center; margin: 20px 0;">
                    or copy this link in your browser:
                </p>
                <div class="code-box" style="background-color: #f9f9f9; border-left: 4px solid #c62828; padding: 15px; border-radius: 4px; word-break: break-all; font-family: monospace; color: #333; font-size: 12px; overflow-x: auto;">
                    <a href="{{ $resetUrl }}" style="color: #c62828; text-decoration: none;">{{ $resetUrl }}</a>
                </div>

                <!-- Critical Security Notice -->
                <div class="critical" style="background-color: #ffe0e0; border: 2px solid #c62828; color: #7f0000; padding: 15px; border-radius: 6px; margin: 20px 0; font-size: 13px; font-weight: 500;">
                    <strong>🔐 CRITICAL SECURITY ALERT:</strong>
                    <br>
                    Admin accounts have elevated privileges. Ensure this reset link credentials are kept secure.
                    Do not share this email or link with unauthorized personnel.
                </div>

                <!-- Steps -->
                <p style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 25px 0 15px 0;">What's next:</p>
                <div class="steps">
                    <div class="step" style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                        <div class="step-number" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: #c62828; color: white; border-radius: 50%; font-weight: 600; flex-shrink: 0; margin-right: 12px; font-size: 13px;">1</div>
                        <div class="step-text" style="font-size: 14px; color: #555; padding-top: 2px;">Click the reset button above</div>
                    </div>
                    <div class="step" style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                        <div class="step-number" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: #c62828; color: white; border-radius: 50%; font-weight: 600; flex-shrink: 0; margin-right: 12px; font-size: 13px;">2</div>
                        <div class="step-text" style="font-size: 14px; color: #555; padding-top: 2px;">Enter a strong new password (minimum 8 characters)</div>
                    </div>
                    <div class="step" style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                        <div class="step-number" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: #c62828; color: white; border-radius: 50%; font-weight: 600; flex-shrink: 0; margin-right: 12px; font-size: 13px;">3</div>
                        <div class="step-text" style="font-size: 14px; color: #555; padding-top: 2px;">Log in to the admin panel with your new password</div>
                    </div>
                </div>

                <!-- Important Info -->
                <div class="info" style="background-color: #ffebee; border: 1px solid #ef5350; color: #c62828; padding: 12px 15px; border-radius: 4px; margin: 20px 0; font-size: 13px;">
                    <strong>⏰ Expiration:</strong> This reset link will expire in <strong>60 minutes</strong>. If it expires, request a new one from the admin login page.
                </div>

                <!-- Security Warning -->
                <div class="warning" style="background-color: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px 15px; border-radius: 4px; margin: 20px 0; font-size: 13px;">
                    <strong>🔒 Security Reminder:</strong>
                    <br>
                    • Never share this email or reset link
                    <br>
                    • Use a unique, strong password
                    <br>
                    • {{ config('app.name') }} staff will never ask for your password or this link
                </div>

                <!-- Help Text -->
                <p style="font-size: 12px; color: #999; margin-top: 25px; text-align: center; line-height: 1.6;">
                    If you did not request this password reset or suspect unauthorized access,
                    <br>
                    please contact your system administrator or
                    <a href="{{ route('support') }}" style="color: #c62828; text-decoration: none; font-weight: 600;">security team</a>
                    immediately.
                </p>

            </div>

            <!-- Divider -->
            <hr class="divider" style="border: 0; height: 1px; background-color: #eee; margin: 0;">

            <!-- Footer -->
            <div class="footer" style="background-color: #f9f9f9; padding: 25px 30px; font-size: 12px; color: #666; text-align: center;">

                <p style="margin-bottom: 15px;">
                    <strong>{{ config('app.name') }} - Admin Portal</strong>
                    <br>
                    Administrative Access
                </p>

                <hr style="border: 0; height: 1px; background-color: #ddd; margin: 15px 0;">

                <p style="margin: 12px 0; line-height: 1.6;">
                    <a href="{{ route('privacy-policy') }}" class="footer-link" style="color: #c62828; text-decoration: none;">Privacy Policy</a>
                    &nbsp;|&nbsp;
                    <a href="{{ route('terms') }}" class="footer-link" style="color: #c62828; text-decoration: none;">Terms of Service</a>
                    &nbsp;|&nbsp;
                    <a href="{{ route('support') }}" class="footer-link" style="color: #c62828; text-decoration: none;">Support</a>
                </p>

                <p style="margin-top: 15px; line-height: 1.6; color: #999;">
                    © {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                    <br>
                    <small>This is an automated email. Please don't reply directly.</small>
                </p>

            </div>

        </div>
    </div>

</body>

</html>
