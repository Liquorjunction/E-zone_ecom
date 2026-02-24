@extends('client.emails.layout')

@section('title', config('app.name') . ' - Thank You for Your Order')
@section('header_title', 'Thank You!')
@section('header_subtitle', 'We appreciate your purchase')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Thank you for your order with <strong>{{ config('app.name') }}</strong>! We truly appreciate your trust in us. Your order <strong>#{{ $order->order_number }}</strong> is now in our hands and we'll take good care of it.
    </p>

    <!-- What Happens Next Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">What Happens Next</p>

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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Our team will review and confirm your order</p>
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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Your items will be carefully packed and dispatched</p>
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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">You'll receive tracking information once it's on the way</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="border-radius: 8px; background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; box-shadow: 0 4px 12px rgba(0, 191, 255, 0.3);">
                            <a href="{{ $orderUrl ?? url('/orders') }}" target="_blank" style="display: inline-block; padding: 16px 48px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; letter-spacing: 0.5px;">View My Order</a>
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
                    <strong>Thank you for choosing {{ config('app.name') }}!</strong> We strive to deliver the best products and experience to every customer. We hope to see you again soon.
                </p>
            </td>
        </tr>
    </table>
@endsection
