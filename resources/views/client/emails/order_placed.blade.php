@extends('client.emails.layout')

@section('title', config('app.name') . ' - Order Placed')
@section('header_title', 'Order Placed Successfully')
@section('header_subtitle', 'We\'ve received your order')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 18px 0; padding: 0; color: #1a1a1a; font-size: 22px; font-weight: 700; line-height: 1.3; font-family: Arial, Helvetica, sans-serif;">Bonjour {{ $user->name }},</p>

    <!-- Main Message -->
    <p style="margin: 0 0 24px 0; padding: 0; color: #555555; font-size: 15px; line-height: 1.7; font-family: Arial, Helvetica, sans-serif;">
        Merci d'avoir fait vos achats chez <strong>{{ config('app.name') }}</strong> ! Votre commande a été passée avec succès et nous sommes déjà en train de tout préparer pour vous.
    </p>

    <!-- Meta Row: Order Info + Address -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 24px 0;">
        <tr>
            <!-- Order Info -->
            <td width="48%" valign="top" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 18px;">
                <p style="margin: 0 0 6px 0; padding: 0; color: #888888; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; font-family: Arial, Helvetica, sans-serif;">Informations sur la commande</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #1a1a1a; font-size: 14px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">#{{ $order->order_number }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                <p style="margin: 0 0 4px 0; padding: 0; color: #555555; font-size: 13px; font-family: Arial, Helvetica, sans-serif;">Mode de paiement : {{ $order->payment_method }}</p>
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
                // Product name translation with robust fallback
                if ($item->product && method_exists($item->product, 'getTranslation')) {
                    $itemName = $item->product->getTranslation('name', 'fr') ?? $item->product->name ?? $item->product_name ?? $item->name ?? '—';
                } else {
                    $itemName = $item->product_name ?? $item->name ?? '—';
                }
                // Variant name translation with robust fallback
                if ($item->variant && method_exists($item->variant, 'getTranslation')) {
                    $variantName = $item->variant->getTranslation('variant_name', 'fr') ?? $item->variant->variant_name ?? $item->variant_name ?? null;
                } else {
                    $variantName = $item->variant_name ?? null;
                }
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

                    <tr>
                        <td style="padding: 10px 16px 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Sous-total: <span style="color: #1a1a1a; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->subtotal ?? $order->total, 2) }}</span>
                            </p>
                        </td>
                    </tr>

                    @if (!empty($order->discount_amount) && $order->discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Remise: <span style="color: #22c55e; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->coupon_discount_amount) && $order->coupon_discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Réduction avec coupon: <span style="color: #22c55e; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </p>
                        </td>
                    </tr>
                    @endif

                    @if (!empty($order->shipping_cost) && $order->shipping_cost > 0)
                    <tr>
                        <td style="padding: 4px 16px;">
                            <p style="margin: 0; padding: 0; color: #888888; font-size: 13px; font-family: Arial, Helvetica, sans-serif; text-align: right;">
                                Livraison: <span style="color: #1a1a1a; font-weight: 500; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->shipping_cost, 2) }}</span>
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
                                Total général: <span style="font-weight: 700; font-size: 17px; margin-left: 16px;">{{config('app.CURRENCY_SYMBOL')}}{{ number_format($order->total, 2) }}</span>
                            </p>
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
                    <strong>Qu'est-ce qui se passe ensuite :</strong> Nous vous enverrons un autre e-mail une fois votre commande confirmée. Gardez un œil sur votre boîte de réception !
                </p>
            </td>
        </tr>
    </table>
@endsection
