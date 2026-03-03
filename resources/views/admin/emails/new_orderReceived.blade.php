@extends('admin.emails.admin_layout')

@section('title', config('app.name') . ' Admin — Nouvelle Commande Reçue')
@section('header_title', 'Nouvelle Commande Reçue')
@section('header_subtitle', 'Un client a placé une nouvelle commande · Une action peut être requise')

@section('content')

    <!-- Alert Badge -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <td>
                <span style="display: inline-block; background-color: #eff6ff; color: #4361ee; font-size: 12px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; padding: 5px 14px; border-radius: 20px; border: 1px solid #c7d2fe; font-family: Arial, Helvetica, sans-serif;">🛒 Alerte Nouvelle Commande</span>
            </td>
        </tr>
    </table>

    <!-- Intro -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #374151; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Une nouvelle commande a été placée sur la vitrine et attend votre examen. Veuillez la confirmer ou la traiter à partir du panneau d'administration.
    </p>

    <!-- Meta Row: Order Info + Customer Info -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Order Info -->
            <td width="48%" valign="top" style="background-color: #f8f9fe; border: 1px solid #e0e4f0; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Informations de Commande</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">#{{ $order->order_number }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $order->payment_method }}</p>
                <!-- Payment Status Badge -->
                @php
                    $isPaid = in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success']);
                @endphp
                <span style="display: inline-block; margin-top: 4px; background-color: {{ $isPaid ? '#dcfce7' : '#fef9c3' }}; color: {{ $isPaid ? '#16a34a' : '#92400e' }}; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; font-family: Arial, Helvetica, sans-serif;">
                    {{ $isPaid ? 'Payé' : 'Paiement en Attente' }}
                </span>
            </td>
            <td width="4%"></td>
            <!-- Customer Info -->
            <td width="48%" valign="top" style="background-color: #f8f9fe; border: 1px solid #e0e4f0; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Client</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">{{ $user->name }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #6b7280; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $user->email }}</p>
                <p style="margin: 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 600; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                    {{ $order->shipping_address['city'] ?? '' }}@if(!empty($order->shipping_address['state'])), {{ $order->shipping_address['state'] }}@endif<br>
                    <span style="color: #6b7280; font-weight: 400; font-size: 13px;">{{ $order->phone_no ?? '' }}</span>
                </p>
            </td>
        </tr>
    </table>

    <!-- Order Items Table -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; border: 1px solid #e0e4f0; border-radius: 8px; overflow: hidden;">

        <!-- Table Header -->
        <tr style="background-color: #4361ee;">
            <td style="padding: 11px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Article</p>
            </td>
            <td align="center" style="padding: 11px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Qté</p>
            </td>
            <td align="right" style="padding: 11px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Prix</p>
            </td>
        </tr>

        <!-- Items -->
        @foreach ($order->orderItems as $i => $item)
            @php
                $itemName = $item->product->name ?? $item->product_name ?? $item->name ?? '—';
                $variantName = $item->variant->variant_name ?? $item->variant_name ?? null;
                $rowBg = $i % 2 === 0 ? '#ffffff' : '#f8f9fe';
            @endphp
            <tr style="background-color: {{ $rowBg }}; border-top: 1px solid #e0e4f0;">
                <td style="padding: 11px 16px;">
                    <p style="margin: 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 500; font-family: Arial, Helvetica, sans-serif;">{{ $itemName }}</p>
                    @if ($variantName)
                        <p style="margin: 3px 0 0 0; padding: 0; color: #9ca3af; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">{{ $variantName }}</p>
                    @endif
                </td>
                <td align="center" style="padding: 11px 16px;">
                    <p style="margin: 0; padding: 0; color: #6b7280; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">{{ $item->quantity }}</p>
                </td>
                <td align="right" style="padding: 11px 16px;">
                    <p style="margin: 0; padding: 0; color: #1a1a2e; font-size: 14px; font-weight: 500; font-family: Arial, Helvetica, sans-serif;">₹{{ number_format($item->unit_price, 2) }}</p>
                </td>
            </tr>
        @endforeach

        <!-- Totals -->
        <tr style="border-top: 2px solid #e0e4f0; background-color: #f8f9fe;">
            <td colspan="3" style="padding: 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                    <tr>
                        <td style="padding: 10px 16px 4px 16px;">
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Sous-total: <span style="color: #374151; font-weight: 500; margin-left: 16px;">₹{{ number_format($order->subtotal ?? $order->total, 2) }}</span>
                            </p>
                        </td>
                    </tr>

                    @if (!empty($order->discount_amount) && $order->discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Remise: <span style="color: #16a34a; font-weight: 500; margin-left: 16px;">−₹{{ number_format($order->discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->coupon_discount_amount) && $order->coupon_discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Remise Coupon: <span style="color: #16a34a; font-weight: 500; margin-left: 16px;">−₹{{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->shipping_cost) && $order->shipping_cost > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Livraison: <span style="color: #374151; font-weight: 500; margin-left: 16px;">₹{{ number_format($order->shipping_cost, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    <!-- Grand Total -->
                    <tr>
                        <td style="padding: 10px 16px 14px 16px; border-top: 1px solid #e0e4f0;">
                            <p style="margin: 0; font-size: 15px; font-family: Arial, Helvetica, sans-serif; text-align: right; color: #4361ee;">
                                Total Général: <span style="font-weight: 700; font-size: 17px; margin-left: 16px;">₹{{ number_format($order->total, 2) }}</span>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>

    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="border-radius: 8px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); background-color: #4361ee; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.35);">
                            <a href="{{ $orderUrl }}" target="_blank" style="display: inline-block; padding: 14px 48px; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; font-family: Arial, Helvetica, sans-serif; border-radius: 8px; letter-spacing: 0.5px;">Voir la Commande dans le Panneau Admin</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; background-color: #eff6ff; border-left: 4px solid #4361ee; border-radius: 8px;">
        <tr>
            <td style="padding: 14px 18px;">
                <p style="margin: 0; padding: 0; color: #3730a3; font-size: 13px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
                    <strong>Action Requise:</strong> Veuillez examiner et confirmer cette commande dans les 24 heures pour éviter les retards. Vous pouvez également l'assigner à un entrepôt à partir du panneau de Gestion des Expéditions.
                </p>
            </td>
        </tr>
    </table>

@endsection
