@extends('client.layouts.app')

@section('content')

    @php
        $sym = config('app.CURRENCY_SYMBOL', '$');
        $orderNum = $order->order_number ?? $order->id;

        $ship = is_array($order->shipping_address)
            ? $order->shipping_address
            : json_decode($order->shipping_address, true) ?? [];
        $bill = is_array($order->billing_address)
            ? $order->billing_address
            : json_decode($order->billing_address, true) ?? [];

        // Use pre-calculated totals from PriceCalculatorService
        $subtotal = (float) ($totals['subtotal'] ?? 0);
        $totalItemDiscount = (float) ($totals['total_item_discount'] ?? 0);
        $subtotalAfterDiscount = (float) ($totals['subtotal_after_item_discount'] ?? 0);
        $couponDiscount = (float) ($totals['coupon_discount'] ?? 0);
        $totalAfterCoupon = (float) ($totals['total_after_coupon'] ?? 0);
        $taxRate = (float) ($totals['tax_rate'] ?? 0);
        $tax = (float) ($totals['tax_amount'] ?? 0);
        $taxableAmount = (float) ($totals['taxable_amount'] ?? 0);
        $shippingMethod = ucfirst(str_replace('_', ' ', $totals['shipping_method'] ?? gtrans('Standard')));
        $shippingCost = (float) ($totals['shipping_cost'] ?? 0);
        $total = (float) ($totals['final_total'] ?? 0);
        $totalWithoutShipping = $total - $shippingCost;

        // Order metadata
        $couponCode = $order->coupon_code ?? null;
        $paymentMethod = ucfirst(str_replace('_', ' ', $order->payment_method ?? gtrans('N/A')));
        $paymentStatus = ucfirst($order->payment_status ?? gtrans('Pending'));
        $orderStatus = ucfirst($order->status ?? gtrans('Processing'));

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
    @endphp

    {{-- Only the print styles change — everything else is the original design --}}
    <style>
        .invoice-card>div {
            margin-left: 6px;
            margin-right: 6px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-card>div {
                margin-left: 3px !important;
                margin-right: 3px !important;
                margin-bottom: 30px !important;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                font-size: 12px !important;
                line-height: 1.35 !important;
            }

            header,
            aside,
            footer,
            #global-toasts,
            .sidebar-mobile-open-btn,
            .floating-action-btn,
            .no-print {
                display: none !important;
            }

            main.main-content-wrapper {
                min-height: auto !important;
                padding-top: 0 !important;
            }

            #main-content-inner {
                margin-left: 0 !important;
            }

            .invoice-bg {
                background: #fff !important;
                min-height: auto !important;
                padding: 0 !important;
            }

            .invoice-card {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                border: none !important;
                overflow: visible !important;
                display: flex !important;
                flex-direction: column !important;
                min-height: calc(297mm - 20mm) !important;
            }

            .invoice-section,
            .print-avoid-break {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .print-mb-2 {
                margin-bottom: 6px !important;
            }

            .print-mb-4 {
                margin-bottom: 8px !important;
            }

            .print-p-3 {
                padding: 10px !important;
            }

            .print-text-xs {
                font-size: 11px !important;
            }

            .print-text-sm {
                font-size: 12px !important;
            }

            h2 {
                font-size: 24px !important;
            }

            h3 {
                font-size: 15px !important;
            }

            h4 {
                font-size: 13px !important;
            }

            .invoice-info-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
            }

            table td,
            table th {
                padding: 6px 9px !important;
            }

            .print-footer-bottom {
                margin-top: auto !important;
            }

            .invoice-header-top {
                background: linear-gradient(135deg, #1a3a52 0%, #0d1f2d 100%) !important;
                border-bottom: 4px solid #00bfff !important;
            }

            .invoice-header-meta {
                background: #f8fafc !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            .invoice-chip {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
            }

            .invoice-surface {
                background: #ffffff !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }

            .invoice-items-wrap {
                border: 1px solid #e5e7eb !important;
            }

            .invoice-items-head {
                background: #f8fafc !important;
            }

            .invoice-items tbody tr:nth-child(even) {
                background: #f9fafb !important;
            }

            .invoice-total-highlight {
                background: #eef6ff !important;
            }
        }
    </style>

    <div class="min-h-screen invoice-bg py-12 px-2">
        <div
            class="invoice-card max-w-2xl mx-auto bg-white shadow-xl rounded-2xl p-8 border border-gray-200/80 ring-1 ring-gray-100 relative overflow-hidden">

            <!-- Email-style Invoice Header -->
            <div class="mb-10 print-mb-4 rounded-xl overflow-hidden border border-primary/20 shadow-sm invoice-section">
                <div
                    class="bg-linear-to-br from-primary-dark to-slate-900 px-6 py-6 border-b-4 border-cyan-400 invoice-header-top">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-3 bg-white rounded-xl px-4 py-2 shadow-sm">
                                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="{{ config('app.name') }}"
                                    class="h-10 w-auto object-contain">
                            </div>
                            <h2 class="text-3xl font-extrabold tracking-tight text-white mt-5">{{ gtrans('Invoice') }}</h2>
                            <p class="text-xs font-semibold tracking-widest uppercase text-cyan-300 mt-1">{{ gtrans('Order') }}
                                #{{ $orderNum }}</p>
                        </div>
                        <div class="text-right text-xs text-slate-200 leading-relaxed">
                            <div>{{ $siteContent->address ?? gtrans('Not available') }}</div>
                            @if ($siteContent->phone ?? null)
                                <div>{{ $siteContent->phone }}</div>
                            @endif
                            <div>{{ $siteContent->email ?? 'support@ezone.com' }}</div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-slate-50 px-6 py-3 flex flex-wrap items-center justify-between gap-2 text-sm border-t border-white/30 invoice-header-meta">
                    <div
                        class="text-slate-500 inline-flex items-center gap-1.5 bg-white/90 rounded-full px-3 py-1 border border-slate-200 invoice-chip">
                        {{ gtrans('Placed') }}: <span
                            class="font-semibold text-slate-700">{{ optional($order->created_at)->format('F j, Y, g:i A') }}</span>
                    </div>
                    <div
                        class="text-slate-500 inline-flex items-center gap-1.5 bg-white/90 rounded-full px-3 py-1 border border-slate-200 invoice-chip">
                        {{ gtrans('Order Status') }}: <span class="font-semibold text-slate-700">{{ $orderStatus }}</span></div>
                    <div
                        class="text-slate-500 inline-flex items-center gap-1.5 bg-white/90 rounded-full px-3 py-1 border border-slate-200 invoice-chip">
                        {{ gtrans('Payment') }}: <span class="font-semibold text-slate-700">{{ $paymentMethod }}
                            ({{ $paymentStatus }})</span></div>
                    <div
                        class="text-slate-500 inline-flex items-center gap-1.5 bg-white/90 rounded-full px-3 py-1 border border-slate-200 invoice-chip">
                        {{ gtrans('Shipping') }}: <span class="font-semibold text-slate-700">{{ $shippingMethod }}</span></div>
                </div>
            </div>

            <!-- Billing/Shipping summary above item list -->
            <div class="invoice-info-grid grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 print-mb-4 invoice-section">

                <div
                    class="bg-white rounded-xl p-4 print-p-3 border border-gray-200 shadow-sm print-avoid-break invoice-surface">
                    <h4 class="font-semibold text-gray-700 mb-1 text-sm">{{ gtrans('Shipping Address') }}</h4>
                    @if (!empty($ship))
                        <p class="text-sm text-gray-800 font-medium print-text-xs">{{ $shipName }}</p>
                        @if ($shipStreet)
                            <p class="text-sm text-gray-600 print-text-xs">{{ $shipStreet }}</p>
                        @endif
                        @if ($shipCity || $shipZip)
                            <p class="text-sm text-gray-600 print-text-xs">
                                {{ $shipCity }}{{ $shipCity && $shipState ? ', ' . $shipState : '' }}
                                {{ $shipZip }}</p>
                        @endif
                        @if ($shipCountry)
                            <p class="text-sm text-gray-600 print-text-xs">{{ $shipCountry }}</p>
                        @endif
                        @if ($shipPhone)
                            <p class="text-xs text-gray-400 mt-1">📞 {{ $shipPhone }}</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 italic">{{ gtrans('No shipping address on record.') }}</p>
                    @endif
                </div>

                <div
                    class="bg-white rounded-xl p-4 print-p-3 border border-gray-200 shadow-sm print-avoid-break invoice-surface">
                    <h4 class="font-semibold text-gray-700 mb-1 text-sm">{{ gtrans('Billing Address') }}</h4>
                    @if (!empty($bill))
                        <p class="text-sm text-gray-800 font-medium print-text-xs">{{ $billName }}</p>
                        @if ($billStreet)
                            <p class="text-sm text-gray-600 print-text-xs">{{ $billStreet }}</p>
                        @endif
                        @if ($billCity || $billZip)
                            <p class="text-sm text-gray-600 print-text-xs">
                                {{ $billCity }}{{ $billCity && $billState ? ', ' . $billState : '' }}
                                {{ $billZip }}</p>
                        @endif
                        @if ($billCountry)
                            <p class="text-sm text-gray-600 print-text-xs">{{ $billCountry }}</p>
                        @endif
                        @if ($billPhone)
                            <p class="text-xs text-gray-400 mt-1">📞 {{ $billPhone }}</p>
                        @endif
                    @elseif (!empty($ship))
                        <p class="text-sm text-gray-800 font-medium print-text-xs">{{ $shipName }}</p>
                        <p class="text-xs text-gray-400 italic">{{ gtrans('Same as shipping address') }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">{{ gtrans('No billing address on record.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-10 print-mb-4 invoice-section">
                <h3 class="font-semibold text-gray-800 mb-3 text-lg border-l-4 border-primary pl-2">{{ gtrans('Order Items') }}</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm invoice-items-wrap">
                    <table class="min-w-full bg-white invoice-items">
                        <thead class="bg-gray-50/80 invoice-items-head">
                            <tr>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-semibold tracking-wide uppercase text-gray-600">
                                    {{ gtrans('Product') }}</th>
                                <th
                                    class="px-4 py-2.5 text-left text-[11px] font-semibold tracking-wide uppercase text-gray-600">
                                    {{ gtrans('SKU') }}</th>
                                <th
                                    class="px-4 py-2.5 text-center text-[11px] font-semibold tracking-wide uppercase text-gray-600">
                                    {{ gtrans('Qty') }}</th>
                                <th
                                    class="px-4 py-2.5 text-right text-[11px] font-semibold tracking-wide uppercase text-gray-600">
                                    {{ gtrans('Unit Price') }}</th>
                                <th
                                    class="px-4 py-2.5 text-right text-[11px] font-semibold tracking-wide uppercase text-gray-600">
                                    {{ gtrans('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
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
                                <tr class="border-b last:border-b-0 odd:bg-white even:bg-gray-50/50">
                                    <td class="px-4 py-2 font-medium text-gray-800 print-text-sm">
                                        {{ $variantName }}
                                        {{-- @if ($variantName)
                                            <span
                                                class="block text-xs text-gray-400 font-normal">{{ $variantName }}</span>
                                        @endif --}}
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-400">{{ $sku }}</td>
                                    <td class="px-4 py-2 text-center text-gray-600">{{ $qty }}</td>
                                    <td class="px-4 py-2 text-right text-gray-700">
                                        @if ($lineDiscount > 0)
                                            <span class="text-xs text-gray-400 line-through mr-1">{{ $sym }}
                                                {{ number_format($originalUnitPrice, 2) }}</span>
                                        @endif
                                        <span>{{ $sym }} {{ number_format($unitPrice, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-right text-gray-900 font-semibold">
                                        <span>{{ $sym }} {{ number_format($lineTotal, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tax Summary and Order Summary -->
            <div class="invoice-info-grid grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 print-mb-4 invoice-section">
                <div
                    class="bg-white rounded-xl p-4 print-p-3 border border-gray-200 shadow-sm print-avoid-break invoice-surface">
                    <h4 class="font-semibold text-gray-700 mb-2 text-sm">{{ gtrans('Tax Summary') }}</h4>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ gtrans('Taxable Amount') }} :</span>
                        <span class="font-medium">
                            @if ($taxableAmount !== null)
                                {{ $sym }} {{ number_format((float) $taxableAmount, 2) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ gtrans('VAT') }} {{ number_format((float) $taxRate, 0) }}% :</span>
                        <span class="font-medium">
                            <span class="font-semibold">{{ $tax > 0 ? $sym . ' ' . number_format($tax, 2) : '-' }}</span>
                        </span>
                    </div>
                    <div class="flex justify-between text-sm border-t pt-2 mt-2"></div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-gray-600">{{ gtrans('Total Tax Inclusive Amount') }} :</span>
                        <span class="font-medium">{{ $sym }} {{ number_format($totalWithoutShipping, 2) }}</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl p-4 print-p-3 border border-gray-200 shadow-sm self-start print-avoid-break invoice-surface">
                    <h4 class="font-semibold text-gray-700 mb-2 text-sm">{{ gtrans('Order Summary') }}</h4>

                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ gtrans('Subtotal') }} ({{ $order->items->sum('quantity') }} {{ gtrans('items') }}) :</span>
                        <span class="font-medium">{{ $sym }} {{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if ($couponCode)
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">
                                {{ gtrans('Coupon Discount') }}:
                                <span
                                    class="ml-1 text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">{{ strtoupper($couponCode) }}</span>
                            </span>
                            <span
                                class="text-green-600 font-medium">{{ $couponDiscount > 0 ? '− ' . $sym . ' ' . number_format($couponDiscount, 2) : '-' }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ gtrans('Shipping') }} ({{ $shippingMethod }}) :</span>
                        @if ($shippingCost > 0)
                            <span>{{ $sym }} {{ number_format($shippingCost, 2) }}</span>
                        @else
                            <span class="text-green-600 font-medium">{{ gtrans('Free') }}</span>
                        @endif
                    </div>

                    <div
                        class="flex justify-between text-base mt-3 border-t pt-2 font-bold bg-primary/5 rounded-lg px-2 py-1 invoice-total-highlight">
                        <span class="text-gray-800">{{ gtrans('Total') }} :</span>
                        <span class="text-primary text-lg">{{ $sym }} {{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-between mt-8 no-print">
                <button onclick="window.print()"
                    class="px-6 py-2 bg-primary hover:bg-primary-dark transition text-white rounded-lg font-semibold shadow">
                    {{ gtrans('Print Invoice') }}
                </button>
                <a href="{{ route('dashboard.overview') }}"
                    class="text-sm text-gray-500 hover:text-primary underline">&larr; {{ gtrans('Back to dashboard') }}</a>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400 print-footer-bottom">
                {{ gtrans('Thank you for shopping with') }} {{ config('app.name') }}. {{ gtrans('If you have any questions about your order, contact us at') }}
                <a href="mailto:{{ $siteContent->email ?? 'support@ezone.com' }}"
                    class="text-primary underline">{{ $siteContent->email ?? 'support@ezone.com' }}</a>.
            </div>

        </div>
    </div>

@endsection
