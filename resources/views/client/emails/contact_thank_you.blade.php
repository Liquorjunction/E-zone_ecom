@extends('client.emails.layout')

@section('title', config('app.name') . ' - Demande de support reçue')
@section('header_title', "Nous avons reçu votre message !")
@section('header_subtitle', 'Notre équipe de support s’en occupe')

@section('content')
    <!-- Salutation et message principal -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Bonjour {{ $name ?? 'Client' }},</p>
    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Merci d’avoir contacté le support de <strong>{{ config('app.name') }}</strong> !<br>
        Nous avons bien reçu votre demande. Notre équipe de support va examiner votre message et vous répondra sous <strong>24 à 48 heures</strong>.<br>
        <span style="color: #00BFFF; font-weight: 600;">Veuillez conserver cet e-mail pour vos dossiers.</span>
    </p>

    <!-- Boîte de détails du ticket -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Détails du ticket</p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Numéro du ticket</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $ticket_number ?? 'N/A' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Sujet</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $subject_label ?? 'N/A' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-top: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Message</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">{{ $user_message ?? 'N/A' }}</p>
                        </td>
                    </tr>
                    @if (!empty($order_number))
                    <tr>
                        <td style="padding: 8px 0; border-top: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Numéro de commande</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $order_number }}</p>
                        </td>
                    </tr>
                    @endif
                    @if (!empty($product_sku))
                    <tr>
                        <td style="padding: 8px 0; border-top: 1px solid #dbeafe;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">SKU du produit</p>
                            <p style="margin: 4px 0 0 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $product_sku }}</p>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Boîte d'information -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Besoin de mettre à jour votre demande ?</strong> Il vous suffit de répondre à cet e-mail avec votre numéro de ticket <strong>{{ $ticket_number ?? '' }}</strong> et tout détail supplémentaire.<br>
                    <span style="color: #00BFFF;">Notre équipe est dédiée à vous offrir la meilleure expérience de support possible.</span>
                </p>
            </td>
        </tr>
    </table>

    <!-- Clôture -->
    <p style="margin: 32px 0 0 0; color: #555555; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
        Merci d’avoir choisi <strong>{{ config('app.name') }}</strong>.<br>
        <span style="color: #1a3a52; font-weight: 600;">Nous vous remercions de votre confiance !</span>
    </p>
@endsection
