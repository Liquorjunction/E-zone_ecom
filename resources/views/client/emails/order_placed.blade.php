@extends('client.emails.layout')

@section('title', config('app.name') . ' - Order Placed')
@section('header_title', 'Order Placed Successfully')
@section('header_subtitle', 'We\'ve received your order')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Thank you for shopping with <strong>{{ config('app.name') }}</strong>! Your order has been placed successfully and we're already getting things ready for you.
    </p>

    <!-- Order Summary Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Order Summary</p>

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Order ID</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">#{{ $order->order_number }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Total Amount</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">₹{{ number_format($order->total_amount, 2) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Payment Method</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $order->payment_method }}</p>
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
                            <a href="{{ $orderUrl }}" target="_blank" style="display: inline-block; padding: 16px 48px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; letter-spacing: 0.5px;">View Order</a>
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
                    <strong>What's Next:</strong> We'll send you another email once your order is confirmed and again when it's dispatched. Keep an eye on your inbox!
                </p>
            </td>
        </tr>
    </table>
@endsection
