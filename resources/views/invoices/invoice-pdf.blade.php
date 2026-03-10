<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice #{{ $order->order_number ?? $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.35;
            color: #1e293b;
            background: #fff;
        }

        .invoice-card {
            width: 96%;
            margin: 14px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-header-top {
            background: #1a3a52;
            border-bottom: 4px solid #22d3ee;
            padding: 24px 24px 20px 24px;
        }

        .hdr-flex {
            width: 100%;
            border-collapse: collapse;
        }

        .hdr-flex td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .hdr-left {
            width: 58%;
        }

        .hdr-right {
            width: 42%;
            text-align: right;
        }

        .logo-pill {
            display: inline-block;
            background: #ffffff;
            border-radius: 12px;
            padding: 7px 14px;
        }

        .logo-pill img {
            height: 40px;
            width: auto;
            display: block;
        }

        .logo-text-fallback {
            font-size: 18px;
            font-weight: 700;
            color: #1a3a52;
            font-family: Helvetica, Arial, sans-serif;
        }

        .hdr-title {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-top: 14px;
            letter-spacing: -0.5px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .hdr-ordernum {
            font-size: 10px;
            font-weight: 600;
            color: #67e8f9;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 4px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .hdr-company {
            font-size: 11px;
            font-weight: 400;
            color: #e2e8f0;
            line-height: 1.8;
            text-align: right;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-header-meta {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 9px 24px;
        }

        .meta-flex {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-flex td {
            border: none;
            padding: 0 4px 0 0;
            vertical-align: middle;
            white-space: nowrap;
        }

        .invoice-chip {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 400;
            color: #64748b;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-chip span {
            font-weight: 600;
            color: #334155;
            font-family: Helvetica, Arial, sans-serif;
        }

        .body-wrap {
            padding: 24px 24px 10px 24px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .info-grid td.col-l {
            padding-right: 12px;
        }

        .info-grid td.col-r {
            padding-left: 12px;
        }

        .invoice-surface {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .invoice-surface h4 {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .addr-name {
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
            font-family: Helvetica, Arial, sans-serif;
        }

        .addr-line {
            font-size: 11.5px;
            font-weight: 400;
            color: #4b5563;
            line-height: 1.6;
            font-family: Helvetica, Arial, sans-serif;
        }

        .addr-phone {
            font-size: 11px;
            font-weight: 400;
            color: #9ca3af;
            margin-top: 5px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .addr-muted {
            font-size: 11.5px;
            font-weight: 400;
            color: #9ca3af;
            font-style: italic;
            font-family: Helvetica, Arial, sans-serif;
        }

        .section-heading {
            border-left: 4px solid #3b82f6;
            padding-left: 9px;
            margin-bottom: 10px;
        }

        .section-heading h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-items-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }

        .invoice-items thead tr {
            background: #f8fafc;
        }

        .invoice-items th {
            padding: 9px 14px;
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-items td {
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 400;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-items tr.row-even td {
            background: #f9fafb;
        }

        .invoice-items tr.row-last td {
            border-bottom: none;
        }

        .td-right {
            text-align: right;
        }

        .td-center {
            text-align: center;
        }

        .td-product {
            font-weight: 500;
            color: #1f2937;
            font-family: Helvetica, Arial, sans-serif;
        }

        .td-sku {
            font-size: 10.5px;
            font-weight: 400;
            color: #9ca3af;
        }

        .td-qty {
            font-weight: 400;
            color: #4b5563;
        }

        .td-price {
            font-weight: 400;
            color: #374151;
        }

        .td-total {
            font-weight: 600;
            color: #111827;
            font-family: Helvetica, Arial, sans-serif;
        }

        .price-original {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 10.5px;
            font-weight: 400;
            margin-right: 3px;
        }

        .sum-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sum-table td {
            padding: 3px 0;
            border: none;
            font-size: 12px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .sum-label {
            font-weight: 400;
            color: #4b5563;
        }

        .sum-val {
            text-align: right;
            font-weight: 500;
            color: #1f2937;
        }

        .sum-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 8px 0;
        }

        .total-row-wrap {
            background: #eff6ff;
            border-radius: 8px;
            margin-top: 10px;
        }

        .total-row-wrap td {
            padding: 8px 10px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .total-label {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .total-value {
            text-align: right;
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
            font-family: Helvetica, Arial, sans-serif;
        }

        .coupon-badge {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-size: 9.5px;
            font-weight: 600;
            padding: 1px 8px;
            border-radius: 999px;
            margin-left: 4px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .text-green {
            color: #16a34a;
            font-weight: 500;
        }

        .invoice-footer {
            text-align: center;
            font-size: 10.5px;
            font-weight: 400;
            color: #9ca3af;
            padding: 16px 24px 22px;
            border-top: 1px solid #f1f5f9;
            margin-top: 8px;
            font-family: Helvetica, Arial, sans-serif;
        }

        .invoice-footer a {
            color: #3b82f6;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    @php
        $sym = config('app.CURRENCY_SYMBOL', '$');
        $orderNum = $order->order_number ?? $order->id;

        $ship = is_array($order->shipping_address)
            ? $order->shipping_address
            : json_decode($order->shipping_address, true) ?? [];
        $bill = is_array($order->billing_address)
            ? $order->billing_address
            : json_decode($order->billing_address, true) ?? [];

        $subtotal = (float) ($totals['subtotal'] ?? 0);
        $totalItemDiscount = (float) ($totals['total_item_discount'] ?? 0);
        $subtotalAfterDiscount = (float) ($totals['subtotal_after_item_discount'] ?? 0);
        $couponDiscount = (float) ($totals['coupon_discount'] ?? 0);
        $totalAfterCoupon = (float) ($totals['total_after_coupon'] ?? 0);
        $taxRate = (float) ($totals['tax_rate'] ?? 0);
        $tax = (float) ($totals['tax_amount'] ?? 0);
        $taxableAmount = (float) ($totals['taxable_amount'] ?? 0);
        $shippingMethod = ucfirst(str_replace('_', ' ', $totals['shipping_method'] ?? 'Standard'));
        $shippingCost = (float) ($totals['shipping_cost'] ?? 0);
        $total = (float) ($totals['final_total'] ?? 0);
        $totalWithoutShipping = $total - $shippingCost;

        $couponCode = $order->coupon_code ?? null;
        $paymentMethod = ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A'));
        $paymentStatus = ucfirst($order->payment_status ?? 'Pending');
        $orderStatus = ucfirst($order->status ?? 'Processing');

        $shipName = $ship['name'] ?? trim(($ship['first_name'] ?? '') . ' ' . ($ship['last_name'] ?? ''));
        $shipStreet = $ship['street'] ?? ($ship['address'] ?? ($ship['address_line'] ?? ''));
        $shipCity = $ship['city'] ?? '';
        $shipState = $ship['state'] ?? '';
        $shipZip = $ship['postal_code'] ?? ($ship['zip'] ?? '');
        $shipCountry = $ship['country'] ?? '';
        $shipPhone = $ship['phone'] ?? '';

        $billName = $bill['name'] ?? trim(($bill['first_name'] ?? '') . ' ' . ($bill['last_name'] ?? ''));
        $billStreet = $bill['street'] ?? ($bill['address'] ?? ($bill['address_line'] ?? ''));
        $billCity = $bill['city'] ?? '';
        $billState = $bill['state'] ?? '';
        $billZip = $bill['postal_code'] ?? ($bill['zip'] ?? '');
        $billCountry = $bill['country'] ?? '';
        $billPhone = $bill['phone'] ?? '';

        $logoPath = public_path('icon/Ezone-Logo-New-2025-Website.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
    @endphp

    <div class="invoice-card">
        <div class="invoice-header-top">
            <table class="hdr-flex">
                <tr>
                    <td class="hdr-left">
                        <div class="logo-pill">
                            @if ($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ config('app.name') }}">
                            @else
                                <span class="logo-text-fallback">{{ config('app.name') }}</span>
                            @endif
                        </div>
                        <div class="hdr-title">Invoice</div>
                        <div class="hdr-ordernum">Order #{{ $orderNum }}</div>
                    </td>
                    <td class="hdr-right">
                        <div class="hdr-company">
                            <div>{{ $siteContent->address ?? 'Not available' }}</div>
                            @if ($siteContent->phone ?? null)
                                <div>{{ $siteContent->phone }}</div>
                            @endif
                            <div>{{ $siteContent->email ?? 'support@ezone.com' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-header-meta">
            <table class="meta-flex">
                <tr>
                    <td>
                        <span class="invoice-chip">
                            Placed: <span>{{ optional($order->created_at)->format('F j, Y, g:i A') }}</span>
                        </span>
                    </td>
                    <td>
                        <span class="invoice-chip">
                            Order Status: <span>{{ $orderStatus }}</span>
                        </span>
                    </td>
                    <td>
                        <span class="invoice-chip">
                            Payment: <span>{{ $paymentMethod }} ({{ $paymentStatus }})</span>
                        </span>
                    </td>
                    <td>
                        <span class="invoice-chip">
                            Shipping: <span>{{ $shippingMethod }}</span>
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="body-wrap">
            <table class="info-grid">
                <tr>
                    <td class="col-l">
                        <div class="invoice-surface">
                            <h4>Shipping Address</h4>
                            @if (!empty($ship))
                                <div class="addr-name">{{ $shipName }}</div>
                                @if ($shipStreet)
                                    <div class="addr-line">{{ $shipStreet }}</div>
                                @endif
                                @if ($shipCity || $shipZip)
                                    <div class="addr-line">
                                        {{ $shipCity }}{{ $shipCity && $shipState ? ', ' . $shipState : '' }}
                                        {{ $shipZip }}
                                    </div>
                                @endif
                                @if ($shipCountry)
                                    <div class="addr-line">{{ $shipCountry }}</div>
                                @endif
                                @if ($shipPhone)
                                    <div class="addr-phone">Phone: {{ $shipPhone }}</div>
                                @endif
                            @else
                                <div class="addr-muted">No shipping address on record.</div>
                            @endif
                        </div>
                    </td>
                    <td class="col-r">
                        <div class="invoice-surface">
                            <h4>Billing Address</h4>
                            @if (!empty($bill))
                                <div class="addr-name">{{ $billName }}</div>
                                @if ($billStreet)
                                    <div class="addr-line">{{ $billStreet }}</div>
                                @endif
                                @if ($billCity || $billZip)
                                    <div class="addr-line">
                                        {{ $billCity }}{{ $billCity && $billState ? ', ' . $billState : '' }}
                                        {{ $billZip }}
                                    </div>
                                @endif
                                @if ($billCountry)
                                    <div class="addr-line">{{ $billCountry }}</div>
                                @endif
                                @if ($billPhone)
                                    <div class="addr-phone">Phone: {{ $billPhone }}</div>
                                @endif
                            @elseif(!empty($ship))
                                <div class="addr-name">{{ $shipName }}</div>
                                <div class="addr-muted">Same as shipping address</div>
                            @else
                                <div class="addr-muted">No billing address on record.</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>

            <div class="section-heading">
                <h3>Order Items</h3>
            </div>

            <div class="invoice-items-wrap">
                <table class="invoice-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="td-center">Qty</th>
                            <th class="td-right">Unit Price</th>
                            <th class="td-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $i => $item)
                            @php
                                $productName = $item->product->name ?? 'Product';
                                $variantName = $item->variant->variant_name ?? null;
                                $sku = $item->variant->sku ?? ($item->product->sku ?? '—');
                                $qty = (float) ($item->quantity ?? 1);
                                $safeQty = $qty > 0 ? $qty : 1;
                                $unitPrice = (float) ($item->unit_price ?? ($item->price ?? 0));
                                $lineDiscount = (float) ($item->discount_amount ?? ($item->line_discount ?? 0));
                                $lineSubtotal = (float) ($item->line_subtotal ?? $safeQty * $unitPrice);
                                $lineTotal = (float) ($item->line_total ?? $lineSubtotal - $lineDiscount);
                                $originalUnitPrice =
                                    $lineDiscount > 0 ? $unitPrice + $lineDiscount / $safeQty : $unitPrice;
                            @endphp
                            <tr
                                class="{{ ($i + 1) % 2 === 0 ? 'row-even' : '' }} {{ $loop->last ? 'row-last' : '' }}">
                                <td class="td-product">{{ $variantName ?? $productName }}</td>
                                <td class="td-sku">{{ $sku }}</td>
                                <td class="td-center td-qty">{{ $qty }}</td>
                                <td class="td-right td-price">
                                    @if ($lineDiscount > 0)
                                        <span class="price-original">
                                            {{ $sym }} {{ number_format($originalUnitPrice, 2) }}
                                        </span>
                                    @endif
                                    <span>{{ $sym }} {{ number_format($unitPrice, 2) }}</span>
                                </td>
                                <td class="td-right td-total">{{ $sym }} {{ number_format($lineTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <table class="info-grid">
                <tr>
                    <td class="col-l">
                        <div class="invoice-surface">
                            <h4>Tax Summary</h4>
                            <table class="sum-table">
                                <tr>
                                    <td class="sum-label">Taxable Amount :</td>
                                    <td class="sum-val">
                                        @if ($taxableAmount !== null)
                                            {{ $sym }} {{ number_format((float) $taxableAmount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="sum-label">VAT {{ number_format((float) $taxRate, 0) }}% :</td>
                                    <td class="sum-val">
                                        {{ $tax > 0 ? $sym . ' ' . number_format($tax, 2) : '-' }}
                                    </td>
                                </tr>
                            </table>
                            <hr class="sum-divider">
                            <table class="sum-table">
                                <tr>
                                    <td class="sum-label">Total Tax Inclusive Amount :</td>
                                    <td class="sum-val">{{ $sym }}
                                        {{ number_format($totalWithoutShipping, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td class="col-r">
                        <div class="invoice-surface">
                            <h4>Order Summary</h4>
                            <table class="sum-table">
                                <tr>
                                    <td class="sum-label">
                                        Subtotal ({{ $order->items->sum('quantity') }} items) :
                                    </td>
                                    <td class="sum-val">{{ $sym }} {{ number_format($subtotal, 2) }}</td>
                                </tr>
                                @if ($couponCode)
                                    <tr>
                                        <td class="sum-label">
                                            Coupon Discount:
                                            <span class="coupon-badge">{{ strtoupper($couponCode) }}</span>
                                        </td>
                                        <td class="sum-val text-green">
                                            {{ $couponDiscount > 0 ? '− ' . $sym . ' ' . number_format($couponDiscount, 2) : '-' }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="sum-label">Shipping ({{ $shippingMethod }}) :</td>
                                    <td class="sum-val">
                                        @if ($shippingCost > 0)
                                            {{ $sym }} {{ number_format($shippingCost, 2) }}
                                        @else
                                            <span class="text-green">Free</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <table class="sum-table total-row-wrap">
                                <tr>
                                    <td class="total-label">Total :</td>
                                    <td class="total-value">{{ $sym }} {{ number_format($total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        <div class="invoice-footer">
            Thank you for shopping with {{ config('app.name') }}.
            If you have any questions about your order, contact us at
            <a href="mailto:{{ $siteContent->email ?? 'support@ezone.com' }}">
                {{ $siteContent->email ?? 'support@ezone.com' }}
            </a>.
        </div>

    </div>

</body>

</html>
