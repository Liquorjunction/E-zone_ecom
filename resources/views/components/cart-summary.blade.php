@props(['cart'])

@php
    $totals = $cart->calculateTotals();
    $subtotal = $totals['subtotal'];
    $totalDiscount = $totals['total_discount'];
    $total = $totals['total'];
    $hasDiscounts = $totalDiscount > 0;
@endphp

<div class="cart-summary" {{ $attributes }}>
    <div class="summary-line d-flex justify-content-between py-2">
        <span>{{ __('Subtotal') }}:</span>
        <span class="fw-semibold">{{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($subtotal, 2) }}</span>
    </div>

    @if($hasDiscounts)
        <div class="summary-line d-flex justify-content-between py-2 text-success">
            <span>
                <i class="bi bi-tag"></i> {{ __('Discount') }}:
            </span>
            <span class="fw-semibold">-{{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($totalDiscount, 2) }}</span>
        </div>
    @endif

    <div class="summary-line d-flex justify-content-between py-2 border-top">
        <span class="fs-5 fw-bold">{{ __('Total') }}:</span>
        <span class="fs-4 fw-bold text-primary">{{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($total, 2) }}</span>
    </div>

    @if($hasDiscounts)
        <div class="savings-badge alert alert-success mt-3 py-2">
            <i class="bi bi-check-circle"></i>
            {{ __('You save') }} {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($totalDiscount, 2) }} {{ __('with current discounts!') }}
        </div>
    @endif
</div>
