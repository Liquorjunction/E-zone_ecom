@props(['cartItem'])

@php
    $discountInfo = $cartItem->getDiscountInfo();
    $hasDiscount = $discountInfo['has_discount'];
    $lineTotal = $cartItem->getLineTotal();
    $lineDiscount = $cartItem->getLineDiscount();
    $lineFinal = $cartItem->getLineFinal();
    $quantity = $cartItem->quantity;
@endphp

<div class="cart-item-price-wrapper" {{ $attributes }}>
    @if($hasDiscount)
        <div class="price-breakdown">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    {{ $quantity }} × {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($discountInfo['original_price'], 2) }}
                </span>
                <span class="text-muted text-decoration-line-through">
                    {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($lineTotal, 2) }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-success">
                    <i class="fas fa-tag"></i> {{ $discountInfo['discount_name'] }}
                </small>
                <span class="text-success">
                    -{{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($lineDiscount, 2) }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <span class="fw-bold">Total:</span>
                <span class="fw-bold text-danger fs-5">
                    {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($lineFinal, 2) }}
                </span>
            </div>
        </div>
    @else
        <div class="price-no-discount">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    {{ $quantity }} × {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($cartItem->getApplicablePrice(), 2) }}
                </span>
                <span class="fw-bold fs-5">
                    {{ config('app.CURRENCY_SYMBOL', '$') }}{{ number_format($lineTotal, 2) }}
                </span>
            </div>
        </div>
    @endif
</div>
