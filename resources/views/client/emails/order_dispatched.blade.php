@extends('client.emails.layout')

@section('title', config('app.name') . ' - Commande prête à quitter l’entrepôt')
@section('header_title', 'Commande prête à quitter l’entrepôt')
@section('header_subtitle', 'Votre colis est presque expédié')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Bonjour {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Votre commande <strong>#{{ $order->order_number }}</strong> est prête à quitter notre entrepôt et sera bientôt remise à notre partenaire de livraison. Utilisez les informations de suivi ci-dessous pour suivre son parcours.
    </p>

    <!-- Meta Row: Dispatch Info + Address -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Dispatch Info -->
            <td width="48%" valign="top" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #888888; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Informations d’expédition</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">#{{ $order->order_number }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $order->courier_name }}</p>
                <p style="margin: 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Suivi : <strong>{{ $order->tracking_number }}</strong></p>
            </td>
            <td width="4%"></td>
            <!-- Shipping Address -->
            <td width="48%" valign="top" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #888888; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Adresse de livraison</p>
                <p style="margin: 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 600; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                    {{ $order->shipping_address['name'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}@if(!empty($order->shipping_address['state'])), {{ $order->shipping_address['state'] }}@endif {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                    {{ $order->shipping_address['country'] ?? '' }}<br>
                    <span style="color: #555555; font-weight: 400;">{{ $order->phone_no ?? '' }}</span>
                </p>
            </td>
        </tr>
    </table>

    <!-- Order Journey -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Parcours de la commande</p>

                <!-- Step 1 - Done -->
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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Commande passée et confirmée</strong> — Reçue et traitée</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 2 - Active -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #00BFFF 0%, #0099CC 100%); background-color: #00BFFF; color: #ffffff; font-size: 18px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">🏭</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; font-weight: 700; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Prête à quitter l’entrepôt</strong> — Avec {{ $order->courier_name }}</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 3 - Pending -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 14px;">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background-color: #e5e7eb; color: #9ca3af; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">3</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #9ca3af; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>En cours de livraison</strong> — Bientôt chez vous</p>
                        </td>
                    </tr>
                </table>

                <!-- Step 4 - Pending -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="32" valign="top" style="padding-right: 14px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="32" height="32">
                                <tr>
                                    <td align="center" valign="middle" style="background-color: #e5e7eb; color: #9ca3af; font-size: 15px; font-weight: 700; border-radius: 8px; font-family: Arial, Helvetica, sans-serif;">4</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="padding-top: 5px;">
                            <p style="margin: 0; padding: 0; color: #9ca3af; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Livrée</strong> — Presque arrivé !</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; background-color: #f0f9ff; border-left: 4px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 18px;">
                <p style="margin: 0; padding: 0; color: #1a3a52; font-size: 14px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Livraison estimée :</strong> Les colis arrivent généralement sous 2 à 5 jours ouvrés. Vous recevrez un autre email lorsque votre commande sera en cours de livraison.
                </p>
            </td>
        </tr>
    </table>
@endsection
