@extends('client.layouts.app')

@section('content')

<link rel="stylesheet" href="/css/invoice-print.css">
<div class="min-h-screen invoice-bg py-12 px-2">
    <div class="max-w-2xl mx-auto bg-white shadow-2xl rounded-2xl p-8 border border-gray-200 relative overflow-hidden">
        <!-- Logo and Company Info -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone" class="h-12 w-12 object-contain rounded-lg border border-gray-200 bg-white shadow-sm">
                <div>
                    <div class="text-xl font-bold text-primary tracking-wide">E-Zone</div>
                    <div class="text-xs text-gray-500">www.ezone.com</div>
                </div>
            </div>
            <div class="text-right text-xs text-gray-500">
                <div>123 Market Street</div>
                <div>City, Country 12345</div>
                <div>support@ezone.com</div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-8 border-b pb-4">
            @php
                $clientStatus = strtolower($order->status ?? 'pending');
                if ($clientStatus === 'completed') {
                    $clientStatus = 'delivered';
                }
                $statusTone = $clientStatus === 'delivered' ? 'green' : ($clientStatus === 'pending' ? 'yellow' : 'red');
            @endphp
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-primary">Invoice</h2>
                <p class="text-base text-gray-500 mt-1">Order #{{ $order->order_number ?? $order->id }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">Placed: <span class="font-medium text-gray-700">{{ optional($order->created_at)->format('F j, Y, g:i A') }}</span></p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-{{ $statusTone }}-100 text-{{ $statusTone }}-700">
                    Status: {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $clientStatus)) }}
                </span>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-semibold text-gray-800 mb-3 text-lg border-l-4 border-primary pl-2">Order Items</h3>
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Product</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Qty</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Unit Price</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            @php
                                $title = $item->variant && $item->variant->variant_name ? $item->variant->variant_name : ($item->product->name ?? 'Product');
                            @endphp
                            <tr class="border-b last:border-b-0 hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $title }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 font-semibold">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($item->total_price ?? ($item->unit_price * $item->quantity), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                <h4 class="font-semibold text-gray-700 mb-2">Shipping Address</h4>
                @php $addr = $order->shipping_address ?? []; @endphp
                <p class="text-sm text-gray-800">{{ $addr['name'] ?? '' }}</p>
                <p class="text-sm text-gray-800">{{ $addr['street'] ?? '' }}</p>
                <p class="text-sm text-gray-800">{{ ($addr['city'] ?? '') }} {{ $addr['postal_code'] ?? '' }}</p>
                <p class="text-sm text-gray-800">{{ $addr['country'] ?? '' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                <h4 class="font-semibold text-gray-700 mb-2">Order Summary</h4>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($order->subtotal ?? ($order->total ?? 0) - ($order->tax ?? 0) - ($order->shipping_cost ?? 0), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Shipping:</span>
                    <span>{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($order->shipping_cost ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Tax:</span>
                    <span>{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($order->tax ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-base mt-3 border-t pt-2 font-bold">
                    <span class="text-gray-800">Total:</span>
                    <span class="text-primary text-lg">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($order->total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mt-4">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-medium text-gray-700">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-primary hover:bg-primary-dark transition text-white rounded-lg font-semibold shadow">Print Invoice</button>
            <a href="{{ route('dashboard.overview') }}" class="text-sm text-gray-500 hover:text-primary underline">&larr; Back to dashboard</a>
        </div>

        <div class="mt-10 text-center text-xs text-gray-400">
            Thank you for shopping with E-Zone. If you have any questions about your order, contact us at <a href="mailto:support@ezone.com" class="text-primary underline">support@ezone.com</a>.
        </div>
    </div>
</div>
@endsection
