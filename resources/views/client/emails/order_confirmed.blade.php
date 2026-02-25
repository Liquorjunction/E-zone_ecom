@extends('client.emails.layout')

@section('title', config('app.name') . ' - Order Confirmed')
@section('header_title', 'Order Confirmed')
@section('header_subtitle', 'Your order is being prepared')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Hello {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Excellente nouvelle ! Votre commande <strong>#{{ $order->order_number }}</strong> Votre commande a bien été confirmée et notre équipe la prépare actuellement pour l’expédition. Voici le récapitulatif complet de votre commande.
    </p>

    <!-- Meta Row: OrderInfo + Address -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Order Info -->
            <td width="48%" valign="top" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #888888; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Informations sur la commande</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">#{{ $order->order_number }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Mode de paiement: {{ $order->payment_method }}</p>
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

    <!-- Order Items Table -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">

        <!-- Table Header -->
        <tr style="background-color: #1a3a52;">
            <td style="padding: 12px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Article</p>
            </td>
            <td align="center" style="padding: 12px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Qté</p>
            </td>
            <td align="right" style="padding: 12px 16px;">
                <p style="margin: 0; padding: 0; color: #ffffff; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Prix</p>
            </td>
        </tr>

        <!-- Items -->
        @foreach ($order->orderItems as $i => $item)
            @php
                $itemName = $item->product && method_exists($item->product, 'getTranslation')
                    ? $item->product->getTranslation('name', 'fr')
                    : ($item->product_name ?? $item->name ?? '—');
                $variantName = $item->variant && method_exists($item->variant, 'getTranslation')
                    ? $item->variant->getTranslation('variant_name', 'fr')
                    : ($item->variant_name ?? null);
                $rowBg = $i % 2 === 0 ? '#ffffff' : '#f9fafb';
            @endphp
            <tr style="background-color: {{ $rowBg }}; border-top: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px;">
                    <p style="margin: 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 500; font-family: Arial, Helvetica, sans-serif;">{{ $itemName }}</p>
                    @if ($variantName)
                        <p style="margin: 3px 0 0 0; padding: 0; color: #888888; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">{{ $variantName }}</p>
                    @endif
                </td>
                <td align="center" style="padding: 12px 16px;">
                    <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">{{ $item->quantity }}</p>
                </td>
                <td align="right" style="padding: 12px 16px;">
                    <p style="margin: 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 500; font-family: Arial, Helvetica, sans-serif;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($item->unit_price, 2) }}</p>
                </td>
            </tr>
        @endforeach

        <!-- Totals Section -->
        <tr style="border-top: 2px solid #e5e7eb; background-color: #f9fafb;">
            <td colspan="3" style="padding: 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                    <!-- Subtotal -->
                    <tr>
                        <td style="padding: 10px 16px 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Subtotal: <span style="color: #1a1a1a; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}} {{ number_format($order->subtotal ?? $order->total, 2) }}</span>
                            </p>
                        </td>
                    </tr>

                    @if (!empty($order->discount_amount) && $order->discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Discount: <span style="color: #22c55e; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->coupon_discount_amount) && $order->coupon_discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Coupon Discount: <span style="color: #22c55e; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->shipping_cost) && $order->shipping_cost > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Expédition: <span style="color: #1a1a1a; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->shipping_cost, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->tax_amount) && $order->tax_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Impôt: <span style="color: #1a1a1a; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->tax_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    <!-- Grand Total -->
                    <tr>
                        <td style="padding: 10px 16px 14px 16px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; padding: 0; font-size: 15px; font-family: Arial, Helvetica, sans-serif; text-align: right; color: #1a3a52;">
                                Total: <span style="font-weight: 700; font-size: 17px; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->total, 2) }}</span>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>

    </table>

    <!-- Order Journey -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0; background-color: #f0f9ff; border: 2px solid #00BFFF; border-radius: 8px;">
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 18px 0; padding: 0; color: #1a3a52; font-size: 15px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Voyage de commande</p>

                <!-- Step 1 -->
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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Commande passée</strong></p>
                        </td>
                    </tr>
                </table>

                <!-- Step 2 -->
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
                            <p style="margin: 0; padding: 0; color: #555555; font-size: 14px; font-weight: 500; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;"><strong>Commande confirmée</strong></p>
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
                    <strong>Restez à l'écoute:</strong>Nous vous enverrons un e-mail dès que votre commande sera expédiée. Cela se produit généralement dans les 1 à 2 jours ouvrables.
                </p>
            </td>
        </tr>
    </table>

@endsection
