{{--
    Partial: checkout/partials/_cart-item-row.blade.php
    Renders a single cart item row in the step-1 main product list.
    Variable: $item (CartItem model with product, variant, discount relations)
--}}

@php
    $sym            = config('app.CURRENCY_SYMBOL', '$');
    $unitPrice      = $item->unit_price ?? 0;
    $unitFinal      = $item->final_price ?? $unitPrice;
    $lineTotal      = $unitPrice * $item->quantity;
    $lineDiscount   = ($item->discount_amount ?? 0) * $item->quantity;
    $lineFinal      = $unitFinal * $item->quantity;
    $hasDiscount    = ($item->discount_amount ?? 0) > 0;

    $availableStock = 0;
    if ($item->variant && method_exists($item->variant, 'getSellableStock')) {
        $availableStock = (int) $item->variant->getSellableStock();
    } elseif ($item->product && method_exists($item->product, 'getSellableStock')) {
        $availableStock = (int) $item->product->getSellableStock();
    }

    $isOutOfStock = $availableStock <= 0;
@endphp

<div class="cart-item-row flex flex-col sm:flex-row gap-6 p-6 bg-linear-to-r from-gray-50 to-white rounded-2xl border border-gray-100 transition-all duration-300 hover:shadow-md"
    data-item-id="{{ $item->id }}"
    data-stock-status="{{ $isOutOfStock ? 'out' : 'available' }}"
    @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @else data-max-stock="0" @endif>

    {{-- Product image --}}
    <div class="shrink-0">
        <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/150x150?text=Product' }}"
            alt="{{ optional($item->product)->name ?? statictranslations('Product') }}"
            class="w-full sm:w-36 h-36 object-cover rounded-2xl shadow-md">
    </div>

    <div class="flex-1">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ optional($item->product)->name ?? statictranslations('Product') }}
                    @if (optional($item->variant)->variant_name)
                        <span class="text-gray-600 text-sm font-normal">- {{ $item->variant->variant_name }}</span>
                    @endif
                </h3>
                @if (optional($item->product)->brand?->name)
                    <span class="inline-block text-xs font-semibold px-3 py-1.5 rounded-full bg-gray-100 text-gray-700">
                        {{ $item->product->brand->name }}
                    </span>
                @endif
            </div>

            {{-- Remove button --}}
            <button type="button"
                class="remove-item-btn flex items-center text-red-500 hover:text-red-700 transition-colors duration-200"
                data-item-id="{{ $item->id }}">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ statictranslations('Remove') }}
            </button>
        </div>

        @if ($isOutOfStock)
            <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ statictranslations('Out of stock') }}
            </div>
            <p class="mb-5 text-sm text-red-600 font-medium">{{ statictranslations('This item is currently unavailable. Remove it to continue checkout.') }}</p>
        @else
            <p class="flex items-center text-sm text-gray-600 mb-5">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ statictranslations('In Stock') }}</span>
            </p>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Quantity controls --}}
            <div class="flex items-center">
                <span class="text-sm font-semibold text-gray-700 mr-4">{{ statictranslations('Quantity:') }}</span>
                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm {{ $isOutOfStock ? 'opacity-50 pointer-events-none' : '' }}">
                    <button type="button"
                        class="qty-decrease px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300"
                        data-item-id="{{ $item->id }}"
                        @disabled($isOutOfStock)
                        @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>−</button>
                    <input type="number" value="{{ $item->quantity }}" min="1"
                        class="cart-quantity-input w-16 text-center border-0 focus:outline-none font-bold text-gray-900 bg-white"
                        data-item-id="{{ $item->id }}"
                        @disabled($isOutOfStock)
                        @if ($availableStock > 0) max="{{ $availableStock }}" data-max-stock="{{ $availableStock }}" @endif>
                    <button type="button"
                        class="qty-increase px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300"
                        data-item-id="{{ $item->id }}"
                        @disabled($isOutOfStock)
                        @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>+</button>
                </div>
            </div>

            {{-- Price display --}}
            <div class="text-right">
                @if ($hasDiscount)
                    <div class="text-lg text-gray-400 line-through mb-1">{{ $sym }} {{ number_format($lineTotal, 2) }}</div>

                    @if ($lineDiscount > 0)
                        <div class="inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ statictranslations('Save') }} {{ $sym }} {{ number_format($lineDiscount, 2) }}
                        </div>
                    @endif

                    <p class="text-3xl font-bold text-red-600 item-line-total" id="item-line-total-{{ $item->id }}">
                        {{ $sym }} {{ number_format($lineFinal, 2) }}
                    </p>
                @else
                    <p class="text-3xl font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent item-line-total"
                        id="item-line-total-{{ $item->id }}">
                        {{ $sym }} {{ number_format($lineFinal, 2) }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
