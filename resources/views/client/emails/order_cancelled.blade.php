@extends('client.emails.layout')

@section('title', config('app.name') . ' - Order Cancelled')
@section('header_title', 'Order Cancelled')
@section('header_subtitle', 'Your order has been cancelled')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 16px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        We regret to inform you that your order <strong>#{{ $order->order_number }}</strong> has been cancelled. We understand this may be inconvenient, and we're sorry for any disappointment.
    </p>

    @if(!empty($order->refund_amount))
    <!-- Refund Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 28px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Refund Details</p>

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Refund Amount</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">₹{{ number_format($order->refund_amount, 2) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Expected Timeline</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $order->refund_eta ?? '5–7 business days' }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @endif

    <!-- Warning Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0; background-color: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #8b5a00; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Didn't request this cancellation?</strong> If you believe this is an error, please contact our support team immediately and we'll look into it right away.
                </p>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0 0 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>We're Here to Help:</strong> Our support team is always happy to assist you. Feel free to reach out with any questions about your cancellation or refund.
                </p>
            </td>
        </tr>
    </table>
@endsection
