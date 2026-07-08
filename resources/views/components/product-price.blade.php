@props(['product', 'variant' => null])

@php
    // Get discount info from product or variant
    if ($variant) {
        $discountInfo = $variant->getDiscountInfo();
        $price = $variant->price;
    } else {
        $discountInfo = $product->getDiscountInfo();
        $price = $product->price;
    }

    $hasDiscount = $discountInfo['has_discount'];
    $originalPrice = $discountInfo['original_price'];
    $finalPrice = $discountInfo['final_price'];
    $discountAmount = $discountInfo['discount_amount'];
    $discountPercentage = $discountInfo['discount_percentage'];
@endphp

<div class="product-price-wrapper" {{ $attributes }}>
    @if($hasDiscount)
        <div class="price-with-discount">
            <span class="original-price text-muted text-decoration-line-through">
                {{ money_format_number($originalPrice) }}
            </span>
            <span class="final-price text-danger fw-bold fs-5">
                {{ money_format_number($finalPrice) }}
            </span>
            @if($discountPercentage > 0)
                <span class="discount-badge badge bg-danger ms-2">
                    -{{ number_format($discountPercentage, 0) }}%
                </span>
            @else
                <span class="discount-badge badge bg-danger ms-2">
                    {{ statictranslations('Save') }} {{ money_format_number($discountAmount) }}
                </span>
            @endif
        </div>
        <small class="text-success d-block mt-1">
            <i class="bi bi-tag"></i> {{ $discountInfo['discount_name'] }}
        </small>
    @else
        <div class="price-no-discount">
            <span class="current-price fw-bold fs-5">
                {{ money_format_number($price) }}
            </span>
        </div>
    @endif
</div>
