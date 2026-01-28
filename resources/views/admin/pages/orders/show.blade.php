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
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left"> </i>Back
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            // Always use $order->items (relation) for order items
                            $items = $order->items ?? collect();
                            $shipping = [];
                            if (is_array($order->shipping_address)) {
                                $shipping = $order->shipping_address;
                            } elseif (!empty($order->shipping_address)) {
                                $decoded = json_decode($order->shipping_address, true);
                                $shipping = is_array($decoded) ? $decoded : [];
                            }
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th>Order #</th>
                                        <td>{{ $order->order_number ?? $order->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <td>{{ optional($order->created_at)->format('d M Y, h:i A') ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $order->status ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment</th>
                                        <td>{{ $order->payment_method ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td>{{ isset($order->total) ? number_format($order->total, 2) : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6>Customer</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $order->user->name ?? ($order->customer_name ?? ($order->user->email ?? 'N/A')) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $order->user->email ?? ($order->customer_email ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $order->phone_no ?? ($order->user->phone ?? ($order->customer_phone ?? ($shipping['phone'] ?? 'N/A'))) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <h6>Shipping Address</h6>
                                <div class="border rounded p-3">
                                    @if (!empty($shipping))
                                        <div>{{ $shipping['name'] ?? 'N/A' }}</div>
                                        <div>
                                            {{ $shipping['street'] ?? ($shipping['address'] ?? ($shipping['line1'] ?? '')) }}
                                        </div>
                                        <div>
                                            {{ trim(($shipping['city'] ?? '') . ' ' . ($shipping['state'] ?? '')) }}
                                            {{ $shipping['postal_code'] ?? ($shipping['postcode'] ?? ($shipping['zip'] ?? '')) ? ' ' . ($shipping['postal_code'] ?? ($shipping['postcode'] ?? ($shipping['zip'] ?? ''))) : '' }}
                                        </div>
                                        <div>{{ $shipping['country'] ?? '' }}</div>
                                        @if (!empty($order->phone_no) || !empty($shipping['phone']))
                                            <div>Phone: {{ $order->phone_no ?? $shipping['phone'] }}</div>
                                        @endif
                                    @else
                                        <div>{{ $order->shipping_address_text ?? 'N/A' }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <h6>Billing Address</h6>
                                <div class="border rounded p-3">
                                    @php
                                        $billing = [];
                                        if (is_array($order->billing_address)) {
                                            $billing = $order->billing_address;
                                        } elseif (!empty($order->billing_address)) {
                                            $decodedBilling = json_decode($order->billing_address, true);
                                            $billing = is_array($decodedBilling) ? $decodedBilling : [];
                                        }
                                    @endphp
                                    @if (!empty($billing))
                                        <div>{{ $billing['name'] ?? 'N/A' }}</div>
                                        <div>{{ $billing['street'] ?? ($billing['address'] ?? ($billing['line1'] ?? '')) }}
                                        </div>
                                        <div>
                                            {{ trim(($billing['city'] ?? '') . ' ' . ($billing['state'] ?? '')) }}
                                            {{ $billing['postal_code'] ?? ($billing['postcode'] ?? ($billing['zip'] ?? '')) ? ' ' . ($billing['postal_code'] ?? ($billing['postcode'] ?? ($billing['zip'] ?? ''))) : '' }}
                                        </div>
                                        <div>{{ $billing['country'] ?? '' }}</div>
                                        @if (!empty($order->phone_no) || !empty($billing['phone']))
                                            <div>Phone: {{ $order->phone_no ?? $billing['phone'] }}</div>
                                        @endif
                                    @else
                                        <div>{{ $order->billing_address_text ?? 'N/A' }}</div>
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
                                                <th>Unit Price</th>
                                                <th>Line Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($items as $item)
                                                @php
                                                    $productName =
                                                        $item->product->name ??
                                                        ($item->product_name ?? ($item->name ?? 'N/A'));
                                                    $qty = $item->quantity ?? 1;
                                                    $unitPrice = $item->unit_price ?? ($item->price ?? 0);
                                                    $discount = $item->discount_amount ?? ($item->line_discount ?? 0);
                                                    $lineSubtotal = $item->line_subtotal ?? $qty * $unitPrice;
                                                    $lineTotal = $item->line_total ?? $lineSubtotal - $discount;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $productName }}
                                                        @if (!empty($item->variant) && !empty($item->variant->variant_name))
                                                            . {{ $item->variant->variant_name }}
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($item->variant)->sku ?? (optional($item->product)->sku ?? ($item->sku ?? '—')) }}
                                                    </td>
                                                    <td>{{ $qty }}</td>
                                                    <td>
                                                        @if (isset($discount) && $discount > 0)
                                                            <del>{{ number_format($unitPrice + $discount / $qty, 2) }}</del>
                                                            <br>
                                                            {{ number_format($unitPrice, 2) }}
                                                        @else
                                                            {{ number_format($unitPrice, 2) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($lineTotal, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7">No items found for this order.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 align-items-start">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h5 class="mb-0">Order Summary</h5>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <th class="w-50">Subtotal</th>
                                                @if (isset($order->coupon_id))
                                                    <td>
                                                        {{ isset($order->subtotal) ? number_format($order->subtotal, 2) : '-' }}
                                                    </td>
                                                @else
                                                    <td>
                                                        {{ isset($order->total) ? number_format($order->total, 2) : '-' }}
                                                    </td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <th>Coupon Discount</th>
                                                <td>-{{ isset($order->coupon_discount_amount) ? number_format($order->coupon_discount_amount, 2) : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td>{{ isset($order->shipping_amount) ? number_format($order->shipping_amount, 2) : (isset($order->shipping_cost) ? number_format($order->shipping_cost, 2) : '-') }}
                                                </td>
                                            </tr>
                                            <tr class="table-active font-weight-bold">
                                                <th>Total</th>
                                                <td class="font-weight-bold">
                                                    {{ isset($order->total) ? number_format($order->total, 2) : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h5 class="mb-0">Tax Summary</h5>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <th>Taxable Amount</th>
                                                <td>{{ isset($order->taxable_amount) ? number_format($order->taxable_amount, 2) : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tax Rate</th>
                                                <td>{{ isset($order->tax_percentage) ? number_format($order->tax_percentage, 2) . '%' : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tax Amount</th>
                                                <td>{{ isset($order->tax_amount) ? number_format($order->tax_amount, 2) : (isset($order->tax) ? number_format($order->tax, 2) : '-') }}
                                                </td>
                                            </tr>
                                            <tr class="table-active font-weight-bold">
                                                <th>Total Tax Inclusive Amount</th>
                                                <td class="font-weight-bold">
                                                    {{ isset($order->total) ? number_format($order->total, 2) : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h5 class="mb-0">Payment Information</h5>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <th>Payment Method</th>
                                                <td>{{ $order->payment_method ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Payment Status</th>
                                                <td>{{ $order->payment_status ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Transaction ID</th>
                                                <td>{{ $order->transaction_id ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card border-0 shadow-sm h-100 mt-3">
                                    <div class="card-header bg-light py-2 d-flex align-items-center">
                                        <i class="bi bi-pencil-square me-2 text-primary"></i>
                                        <h5 class="mb-0">Edit Status</h5>
                                    </div>
                                    <div class="card-body py-3">
                                        <form action="{{ route('admin.orders.update-order-status') }}" method="POST"
                                            class="needs-validation" novalidate>
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $order->id }}">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Order Status</label>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @php
                                                        $statuses = [
                                                            'pending' => 'Pending',
                                                            'processing' => 'Processing',
                                                            'completed' => 'Completed',
                                                            'cancelled' => 'Cancelled',
                                                        ];
                                                    @endphp
                                                    @foreach ($statuses as $value => $label)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="status"
                                                                id="status_{{ $value }}"
                                                                value="{{ $value }}"
                                                                {{ $order->status == $value ? 'checked' : '' }} required>
                                                            <label class="form-check-label"
                                                                for="status_{{ $value }}">{{ $label }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="invalid-feedback">
                                                    Please select a status.
                                                </div>
                                            </div>
                                            <button type="submit"
                                                class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                                <i class="bi bi-check-circle"></i>
                                                <span>Update Status</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 w-100 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
