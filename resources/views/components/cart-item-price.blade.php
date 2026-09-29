@props(['cartItem'])

@php
    // Use stored discount data from cart_items table
    $hasDiscount = ($cartItem->discount_amount ?? 0) > 0;
    $unitPrice = $cartItem->unit_price ?? 0;
    $unitFinal = $cartItem->final_price ?? $unitPrice;
    $lineTotal = $unitPrice * $cartItem->quantity;
    $lineDiscount = ($cartItem->discount_amount ?? 0) * $cartItem->quantity;
    $lineFinal = $unitFinal * $cartItem->quantity;
    $quantity = $cartItem->quantity;
    $discountName = $hasDiscount && $cartItem->discount ? $cartItem->discount->getTranslation('name', app()->getLocale()) : null;
@endphp

<div class="cart-item-price-wrapper" {{ $attributes }}>
    @if($hasDiscount)
        <div class="price-breakdown">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    {{ $quantity }} × {{ money_format_number($unitPrice) }}
                </span>
                <span class="text-muted text-decoration-line-through">
                    {{ money_format_number($lineTotal) }}
                </span>
            </div>
            @if($discountName)
            <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-success">
                    <i class="bi bi-tag"></i> {{ $discountName }}
                </small>
                <span class="text-success">
                    -{{ money_format_number($lineDiscount) }}
                </span>
            </div>
            @endif
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <span class="fw-bold">{{ statictranslations('Total') }}:</span>
                <span class="fw-bold text-danger fs-5">
                    {{ money_format_number($lineFinal) }}
                </span>
            </div>
        </div>
    @else
        <div class="price-no-discount">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">
                    {{ $quantity }} × {{ money_format_number($unitPrice) }}
                </span>
                <span class="fw-bold fs-5">
                    {{ money_format_number($lineTotal) }}
                </span>
            </div>
        </div>
    @endif
</div>
