{{--
    Partial: checkout/partials/_summary-item-row.blade.php
    Renders a compact item row in the order-summary sidebar (both step 1 and step 2).
    Variable: $item, $showQtyControls (bool – false on step 3)
--}}

@php
    $sym          = config('app.CURRENCY_SYMBOL', '$');
    $showQtyCtrl  = $showQtyControls ?? true;
    $unitPrice    = $item->unit_price ?? 0;
    $unitFinal    = $item->final_price ?? $unitPrice;
    $lineTotal    = $unitPrice * $item->quantity;
    $lineFinal    = $unitFinal * $item->quantity;
    $hasDiscount  = ($item->discount_amount ?? 0) > 0;

    $availableStock = 0;
    if ($showQtyCtrl && $item->variant && method_exists($item->variant, 'getSellableStock')) {
        $availableStock = (int) $item->variant->getSellableStock();
    } elseif ($showQtyCtrl && $item->product && method_exists($item->product, 'getSellableStock')) {
        $availableStock = (int) $item->product->getSellableStock();
    }

    $isOutOfStock = $availableStock <= 0;
@endphp

<div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200 summary-item-row items-center justify-between"
    data-summary-item-id="{{ $item->id }}"
    data-stock-status="{{ $isOutOfStock ? 'out' : 'available' }}"
    @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @else data-max-stock="0" @endif>

    <div class="flex gap-4 flex-1 min-w-0">
        <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/80x80' }}"
            alt="{{ optional($item->product)->name ?? statictranslations('Product') }}"
            class="w-20 h-20 rounded-lg object-cover shadow-sm shrink-0">

        <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-900 text-xs mb-1 truncate">
                {{ optional($item->product)->name ?? statictranslations('Product') }}
            </h4>

            @if (optional($item->variant)->variant_name)
                <div class="text-gray-600 text-xs font-normal mb-1">{{ $item->variant->variant_name }}</div>
            @endif

            @if ($showQtyCtrl && ! $isOutOfStock)
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-xs text-gray-600">{{ statictranslations('Qty:') }}</p>
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                        <button type="button"
                            class="summary-qty-decrease px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                            data-item-id="{{ $item->id }}">−</button>
                        <span class="summary-quantity px-3 py-1 text-xs font-semibold text-gray-900 bg-white min-w-7.5 text-center">
                            {{ $item->quantity }}
                        </span>
                        <button type="button"
                            class="summary-qty-increase px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                            data-item-id="{{ $item->id }}">+</button>
                    </div>
                </div>
            @elseif ($showQtyCtrl)
                <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700">
                    {{ statictranslations('Out of stock') }}
                </div>
                <p class="text-xs text-red-600 mb-2">{{ statictranslations('Remove this item to complete checkout.') }}</p>
            @else
                <p class="text-xs text-gray-500 mb-1">{{ statictranslations('Qty:') }} {{ $item->quantity }}</p>
            @endif

            @if ($hasDiscount)
                <span class="text-xs text-gray-400 line-through">{{ $sym }} {{ number_format($lineTotal, 2) }}<br/></span>
                <span class="summary-item-price text-sm font-bold text-red-600">
                    {{ $sym }} {{ number_format($lineFinal, 2) }}
                </span>
            @else
                <p class="summary-item-price text-sm font-bold text-primary mt-1">
                    {{ $sym }} {{ number_format($lineFinal, 2) }}
                </p>
            @endif
        </div>
    </div>

    @if ($showQtyCtrl)
        <button type="button"
            class="summary-remove-item-btn flex items-center justify-center text-red-500 hover:text-red-700 transition-colors duration-200 shrink-0"
            data-item-id="{{ $item->id }}" title="{{ statictranslations('Remove item') }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    @endif
</div>
