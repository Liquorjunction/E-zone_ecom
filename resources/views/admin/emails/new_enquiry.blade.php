@extends('admin.emails.admin_layout')

@section('title', config('app.name') . ' Admin — Nouvelle Demande Reçue')
@section('header_title', 'Nouvelle Demande Reçue')
@section('header_subtitle', 'Un visiteur a soumis un formulaire de contact · Veuillez examiner et répondre')

@section('content')
    <!-- Intro -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #374151; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Une nouvelle demande a été soumise via le formulaire de contact. Les détails sont ci-dessous — veuillez examiner et répondre au client dès que possible.
    </p>

    <!-- Meta Row: Ticket Info + Sender Info -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Ticket Info -->
            <td width="48%" valign="top" style="background-color: #f8f9fe; border: 1px solid #e0e4f0; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Informations du Ticket</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $ticket_number ?? 'N/A' }}</p>
                <p style="margin: 0 0 8px 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ now()->format('d M Y, h:i A') }}</p>
                <!-- Subject Badge -->
                <span style="display: inline-block; background-color: #eff6ff; color: #4361ee; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid #c7d2fe; font-family: Arial, Helvetica, sans-serif;">{{ $subject_label ?? 'Général' }}</span>

                @if(!empty($order_number))
                <p style="margin: 8px 0 0 0; padding: 0; color: #3a0ca3; font-size: 13px; font-family: Arial, Helvetica, sans-serif;"><strong>Numéro de Commande :</strong> {{ $order_number }}</p>
                @endif
                @if(!empty($product_sku))
                <p style="margin: 4px 0 0 0; padding: 0; color: #3a0ca3; font-size: 13px; font-family: Arial, Helvetica, sans-serif;"><strong>SKU Produit :</strong> {{ $product_sku }}</p>
                @endif
            </td>
            <td width="4%"></td>
            <!-- Sender Info -->
            <td width="48%" valign="top" style="background-color: #f8f9fe; border: 1px solid #e0e4f0; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">De</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $name ?? 'N/A' }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $email ?? 'N/A' }}</p>
                @if(!empty($phone))
                <p style="margin: 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $phone }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Message Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; border: 1px solid #e0e4f0; border-radius: 8px; overflow: hidden;">

        <!-- Box Header -->
        <tr style="background-color: #4361ee;">
            <td style="padding: 11px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Message</p>
            </td>
        </tr>

        <!-- Message Content -->
        <tr>
            <td style="padding: 20px 20px; background-color: #ffffff;">
                <p style="margin: 0; padding: 0; color: #374151; font-size: 14px; line-height: 1.8; font-family: Arial, Helvetica, sans-serif; white-space: pre-line;">{{ $user_message ?? '' }}</p>
            </td>
        </tr>

    </table>

    <!-- CTA Buttons Row -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Primary: View in Panel -->
            <td align="center" style="padding-right: 8px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td align="center" style="border-radius: 8px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); background-color: #4361ee; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.35);">
                            <a href="{{ $ticketUrl ?? '#' }}" target="_blank" style="display: block; padding: 13px 20px; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; text-align: center; letter-spacing: 0.3px;">Voir dans le panneau d'administration</a>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- Secondary: Reply via Email -->
            <td align="center" style="padding-left: 8px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td align="center" style="border-radius: 8px; background-color: #ffffff; border: 2px solid #4361ee;">
                            <a href="mailto:{{ $email ?? '' }}?subject=Re: [{{ $ticket_number ?? '' }}] {{ $subject_label ?? '' }}" style="display: block; padding: 12px 20px; color: #4361ee; text-decoration: none; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 6px; text-align: center; letter-spacing: 0.3px;">Répondre au client</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Warning Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; background-color: #eff6ff; border-left: 4px solid #4361ee; border-radius: 8px;">
        <tr>
            <td style="padding: 14px 18px;">
                <p style="margin: 0; padding: 0; color: #3730a3; font-size: 13px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Rappel :</strong> Un e-mail de confirmation a déjà été envoyé à <strong>{{ $email ?? 'N/A' }}</strong> avec le numéro de ticket <strong>{{ $ticket_number ?? 'N/A' }}</strong>. Veuillez répondre sous 24 à 48 heures pour maintenir la satisfaction du client.
                </p>
            </td>
        </tr>
    </table>

@endsection
