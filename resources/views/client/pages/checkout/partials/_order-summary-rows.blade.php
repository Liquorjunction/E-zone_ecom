{{--
    Partial: checkout/partials/_order-summary-rows.blade.php
    Renders the price breakdown rows used inside the order summary sidebar.

    Required variables (passed via @include):
      - $totals  : array from PriceCalculatorService::calculate()
      - $step    : int (1, 2, or 3)
      - $idPrefix: string '' for step 1, '-step2' for step 2
                   (prevents duplicate HTML IDs when both sidebars exist on the same page)
--}}

@php
    $sym        = config('app.CURRENCY_SYMBOL', '$');
    $subtotalId = 'order-subtotal' . $idPrefix;
    $discId     = 'order-discount' . $idPrefix;
    $subAfterDiscId = 'order-subtotal-after-discount' . $idPrefix;
    $couponDiscId   = 'order-coupon-discount' . $idPrefix;
    $shippingId     = 'order-shipping' . $idPrefix;
    $totalId        = 'order-total' . $idPrefix;
@endphp

<div class="space-y-4 mb-6">

    {{-- MRP / Subtotal --}}
    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
        <span class="text-gray-600 font-medium">{{ statictranslations('MRP') }}</span>
        <span id="{{ $subtotalId }}" class="font-bold text-gray-900">
            {{ money_format_number($totals['subtotal'] ?? 0) }}
        </span>
    </div>

    {{-- Item discount --}}
    @if (($totals['total_item_discount'] ?? 0) > 0)
        <div id="item-discount-row{{ $idPrefix }}"
            class="flex justify-between text-green-600 text-base bg-green-50 px-4 py-3 rounded-xl">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ statictranslations('Discount on MRP') }}
            </span>
            <span id="{{ $discId }}" class="font-bold">
                -{{ money_format_number($totals['total_item_discount'] ?? 0) }}
            </span>
        </div>
    @else
        {{-- Hidden placeholder so JS can toggle it via display --}}
        <div id="item-discount-row{{ $idPrefix }}"
            class="flex justify-between text-green-600 text-base bg-green-50 px-4 py-3 rounded-xl"
            style="display:none">
            <span class="flex items-center">{{ statictranslations('Discount on MRP') }}</span>
            <span id="{{ $discId }}" class="font-bold"></span>
        </div>
    @endif

    {{-- Coupon discount --}}
    @php $couponApplied = ($totals['coupon_discount'] ?? 0) > 0; @endphp
    <div id="coupon-discount-row{{ $idPrefix }}"
        class="flex justify-between items-center text-purple-600 text-base bg-purple-50 px-4 py-3 rounded-xl"
        @unless($couponApplied) style="display:none" @endunless>
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span class="font-semibold">{{ statictranslations('Coupon Discount') }}</span>
        </div>
        <span id="{{ $couponDiscId }}" class="font-bold">
            @if($couponApplied) -{{ money_format_number($totals['coupon_discount']) }} @endif
        </span>
    </div>

    {{-- Shipping --}}
    @if ($step !== 1)
        <div class="flex justify-between text-gray-600 text-base">
            <span>{{ statictranslations('Shipping') }}</span>
            <span id="{{ $shippingId }}" class="font-bold text-green-600">
                {{ ($totals['shipping_cost'] ?? 0) > 0 ? money_format_number($totals['shipping_cost']) : statictranslations('FREE') }}
            </span>
        </div>
    @endif

    {{-- Grand total --}}
    <div class="border-t-2 border-gray-100 pt-4">
        <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-gray-900">{{ statictranslations('Total') }}</span>
            <span id="{{ $totalId }}"
                class="text-xl font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">
                {{ money_format_number($totals['final_total'] ?? 0) }}
            </span>
        </div>
    </div>

</div>
