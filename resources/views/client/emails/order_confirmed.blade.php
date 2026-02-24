@extends('client.emails.layout')

@section('title', config('app.name') . ' - Order Confirmed')
@section('header_title', 'Order Confirmed')
@section('header_subtitle', 'Your order is being prepared')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Great news! Your order <strong>#{{ $order->order_number }}</strong> has been confirmed and our team is now preparing it for dispatch. We'll keep you updated every step of the way.
    </p>

    <!-- Status Steps Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Order Journey</p>

                <!-- Step 1 - Active -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">✓</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Order Placed</strong> — Received and logged in our system</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 2 - Active -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">✓</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Order Confirmed</strong> — Being prepared for dispatch</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 3 - Pending -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background-color: #e5e7eb; color: #9ca3af; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">3</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #9ca3af; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Dispatched</strong> — On its way to you</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0 0 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Stay Tuned:</strong> You'll receive another email with tracking details once your order has been dispatched. This usually happens within 1–2 business days.
                </p>
            </td>
        </tr>
    </table>
@endsection
