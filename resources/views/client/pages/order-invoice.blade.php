@extends('client.layouts.app')

@section('content')
<div class="min-h-screen bg-white p-8">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold">Invoice</h2>
                <p class="text-sm text-gray-600">Order #{{ $order->order_number ?? $order->id }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm">Placed: {{ optional($order->created_at)->format('F j, Y, g:i A') }}</p>
                <p class="text-sm">Status: {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Items</h3>
            <div class="border rounded-lg">
                @foreach($order->items as $item)
                    @php
                        $title = $item->variant && $item->variant->variant_name ? $item->variant->variant_name : ($item->product->name ?? 'Product');
                    @endphp
                    <div class="flex justify-between px-4 py-3 border-b last:border-b-0">
                        <div>
                            <div class="font-medium">{{ $title }}</div>
                            <div class="text-xs text-gray-500">Qty: {{ $item->quantity }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium">${{ number_format($item->unit_price, 2) }}</div>
                            <div class="text-xs text-gray-500">Total: ${{ number_format($item->total_price ?? ($item->unit_price * $item->quantity), 2) }}</div>
                        </div>
                    </div>
                @endforeach
+            </div>
         </div>

         <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
             <div>
                 <h4 class="font-semibold">Shipping Address</h4>
                 @php $addr = $order->shipping_address ?? []; @endphp
                 <p class="text-sm">{{ $addr['name'] ?? '' }}</p>
                 <p class="text-sm">{{ $addr['street'] ?? '' }}</p>
                 <p class="text-sm">{{ ($addr['city'] ?? '') }} {{ $addr['postal_code'] ?? '' }}</p>
                 <p class="text-sm">{{ $addr['country'] ?? '' }}</p>
             </div>
             <div class="text-right">
                 <h4 class="font-semibold">Order Summary</h4>
                 <div class="text-sm">Subtotal: ${{ number_format($order->subtotal ?? ($order->total ?? 0) - ($order->tax ?? 0) - ($order->shipping_cost ?? 0), 2) }}</div>
                 <div class="text-sm">Shipping: ${{ number_format($order->shipping_cost ?? 0, 2) }}</div>
                 <div class="text-sm">Tax: ${{ number_format($order->tax ?? 0, 2) }}</div>
                 <div class="font-bold text-lg">Total: ${{ number_format($order->total ?? 0, 2) }}</div>
             </div>
         </div>

         <div class="flex items-center justify-between">
             <button onclick="window.print()" class="px-4 py-2 bg-primary text-white rounded">Print</button>
             <a href="{{ route('dashboard.overview') }}" class="text-sm text-gray-600">Back to dashboard</a>
         </div>
    </div>
</div>
@endsection
