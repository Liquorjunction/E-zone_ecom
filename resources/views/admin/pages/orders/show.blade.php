@extends('admin.layouts.app')
@section('title', 'Order Details')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Order Details - #{{ $order->order_number ?? $order->id }}</h4>
                        <small class="text-muted">View detailed information about the order</small>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $items = $order->orderItems ?? $order->items ?? $order->order_items ?? collect();
                        $shipping = [];
                        if (is_array($order->shipping_address)) {
                            $shipping = $order->shipping_address;
                        } elseif (! empty($order->shipping_address)) {
                            $decoded = json_decode($order->shipping_address, true);
                            $shipping = is_array($decoded) ? $decoded : [];
                        }
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><th>Order #</th><td>{{ $order->order_number ?? $order->id }}</td></tr>
                                <tr><th>Date</th><td>{{ optional($order->created_at)->format('d M Y, h:i A') ?? 'N/A' }}</td></tr>
                                <tr><th>Status</th><td>{{ $order->status ?? 'N/A' }}</td></tr>
                                <tr><th>Payment</th><td>{{ $order->payment_method ?? 'N/A' }}</td></tr>
                                <tr><th>Total</th><td>{{ isset($order->total) ? number_format($order->total, 2) : 'N/A' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6>Customer</h6>
                            <table class="table table-borderless table-sm">
                                <tr><th>Name</th><td>{{ $order->user->name ?? $order->customer_name ?? ($order->user->email ?? 'N/A') }}</td></tr>
                                <tr><th>Email</th><td>{{ $order->user->email ?? $order->customer_email ?? 'N/A' }}</td></tr>
                                <tr><th>Phone</th><td>{{ $order->user->phone ?? $order->customer_phone ?? ($shipping['phone'] ?? 'N/A') }}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Shipping Address</h6>
                            <div class="border rounded p-3">
                                @if(!empty($shipping))
                                    <div>{{ $shipping['name'] ?? 'N/A' }}</div>
                                    <div>{{ $shipping['address'] ?? ($shipping['line1'] ?? '') }}</div>
                                    <div>{{ ($shipping['city'] ?? '') }} {{ $shipping['state'] ?? '' }} {{ $shipping['postcode'] ?? '' }}</div>
                                    <div>{{ $shipping['country'] ?? '' }}</div>
                                @else
                                    <div>{{ $order->shipping_address_text ?? 'N/A' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Items</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                            @php
                                                $productName = $item->product->name ?? $item->product_name ?? $item->name ?? 'N/A';
                                                $qty = $item->quantity ?? $item->qty ?? 1;
                                                $price = $item->price ?? $item->unit_price ?? 0;
                                                $subtotal = $qty * $price;
                                            @endphp
                                            <tr>
                                                <td>{{ $productName }}</td>
                                                <td>{{ $item->sku ?? '—' }}</td>
                                                <td>{{ $qty }}</td>
                                                <td>{{ number_format($price, 2) }}</td>
                                                <td>{{ number_format($subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5">No items found for this order.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            @can('order.update')
                                <a href="{{ route('admin.orders.edit', ['id' => $order->id]) }}" class="btn btn-primary">Edit Order</a>
                            @endcan
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h5>Summary</h5>
                            <table class="table table-borderless table-sm w-auto float-md-right">
                                <tr><th>Subtotal:</th><td>{{ isset($order->subtotal) ? number_format($order->subtotal,2) : '-' }}</td></tr>
                                <tr><th>Shipping:</th><td>{{ isset($order->shipping_amount) ? number_format($order->shipping_amount,2) : (isset($order->shipping_cost) ? number_format($order->shipping_cost,2) : '-') }}</td></tr>
                                <tr><th>Tax:</th><td>{{ isset($order->tax) ? number_format($order->tax,2) : '-' }}</td></tr>
                                <tr><th class="font-weight-bold">Total:</th><td class="font-weight-bold">{{ isset($order->total) ? number_format($order->total,2) : '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
