@extends('client.emails.layout')

@section('title', config('app.name') . ' - Commande Annulée')
@section('header_title', 'Commande Annulée')
@section('header_subtitle', 'Votre commande a été annulée')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
       Nous avons le regret de vous informer que votre commande <strong>#{{ $order->order_number }}</strong> a été annulée. Nous sommes désolés pour tout inconvénient que cela pourrait causer.
    </p>

    <!-- Warning Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 16px 0; background-color: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #8b5a00; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Vous n'avez pas demandé cette annulation ?</strong> Si vous n'avez pas initié cette annulation, veuillez contacter notre équipe d'assistance immédiatement pour que nous puissions sécuriser votre compte et enquêter sur l'activité suspecte.
                </p>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>We're Here to Help:</strong> Our support team is always happy to assist you. Feel free to reach out with any questions about your cancellation or refund.
                </p>
            </td>
        </tr>
    </table>
@endsection
