<div
    class="group bg-white rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl border-2 border-gray-100 hover:border-primary/20">
    <div class="relative aspect-square bg-gray-100 overflow-hidden">
        <a href="{{ $productUrl }}"
            class="block w-full h-full">
            <img src="{{ $imagePath ?? 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $name }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
        </a>
        <div
            class="absolute inset-0 bg-linear-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
        </div>
        @if ($productDiscountInfo['has_discount'] || $master->created_at > now()->subDays(30) || $isBestseller)
            <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
                @if ($productDiscountInfo['has_discount'])
                    <span
                        class="inline-flex items-center gap-1 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                                clip-rule="evenodd" />
                        </svg>
                        @if ($productDiscountInfo['discount_percentage'] > 0)
                            {{ number_format($productDiscountInfo['discount_percentage'], 0) }}% {{ statictranslations('OFF') }}
                        @else
                            {{ config('app.CURRENCY_SYMBOL') }} {{ number_format($productDiscountInfo['discount_amount'], 2) }} {{ statictranslations('OFF') }}
                        @endif
                    </span>
                @endif

                @if ($master->created_at > now()->subDays(30))
                    <span
                        class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ statictranslations('NEW') }}
                    </span>
                @endif

                @if ($isBestseller)
                    <span
                        class="inline-flex items-center gap-1 bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{  statictranslations('BESTSELLER') }}
                    </span>
                @endif
            </div>
        @endif

        <button type="button" aria-label="{{ statictranslations('Add to wishlist') }}" data-product-variant-id="{{ $variantId }}"
            data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}" data-toast-ignore
            class="wishlist-btn no-toast absolute top-3 right-3 w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center transition-all duration-200 hover:scale-110 z-10 focus:outline-none {{ $isWishlisted ? 'opacity-100 is-wishlisted' : 'opacity-0 group-hover:opacity-100' }}"
            @if (!$variantId) disabled @endif>
            @if ($isWishlisted)
                <svg class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z" />
                </svg>
            @else
                <svg class="w-5 h-5 text-gray-400 transition-all duration-200 group-hover:text-red-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z" />
                </svg>
            @endif
        </button>

        <!-- Compare Product Icon Button -->
        <button type="button" aria-label="{{ statictranslations('Compare product') }}" data-product-variant-id="{{ $variantId }}"
            data-init-compared="{{ $isCompared ? '1' : '0' }}"
            class="compare-btn no-toast absolute top-16 right-3 w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center transition-all duration-200 hover:scale-110 z-10 focus:outline-none {{ $isCompared ? 'opacity-100 is-compared' : 'opacity-0 group-hover:opacity-100' }}"
            @if (!$variantId) disabled @endif>
            <svg class="w-5 h-5 {{ $isCompared ? 'text-blue-700' : 'text-blue-500 group-hover:text-blue-700' }} transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="7" width="7" height="13" rx="2" fill="currentColor" class="{{ $isCompared ? 'text-blue-200' : 'text-blue-200' }}"/>
                <rect x="14" y="3" width="7" height="17" rx="2" fill="currentColor" class="{{ $isCompared ? 'text-blue-500' : 'text-blue-400' }}"/>
                <rect x="3" y="7" width="7" height="13" rx="2" stroke="currentColor"/>
                <rect x="14" y="3" width="7" height="17" rx="2" stroke="currentColor"/>
            </svg>
        </button>
    </div>

    <div class="p-5">
        <div class="flex items-center justify-between mb-3">
            <span
                class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20">
                {{ $categoryName }}
            </span>
            @if ($stock > 0)
                <div class="flex items-center gap-1.5 text-xs font-medium text-green-600">
                    <span class="flex h-2 w-2 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    {{ statictranslations('In Stock') }}
                </div>
            @else
                <div class="flex items-center gap-1.5 text-xs font-medium text-red-600">
                    <span class="flex h-2 w-2 relative">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                    {{ statictranslations('Out of Stock') }}
                </div>
            @endif
        </div>

        <a href="{{ $productUrl }}">
            <h3
                class="text-sm font-bold text-gray-900 line-clamp-2 mb-3 group-hover:text-primary transition-colors leading-snug">
                {{ $name }}
            </h3>
        </a>

        <div class="flex items-center gap-2 mb-4">
            @if($reviewCount > 0)
                <div class="flex items-center text-yellow-400 text-base">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            ★
                        @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                            ◐
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <span class="text-sm text-gray-600 font-medium">{{ $averageRating }} <span class="text-gray-400">({{ $reviewCount }})</span></span>
            @else
                <div class="flex items-center text-yellow-400 text-base">
                    ☆☆☆☆☆
                </div>
                <span class="text-sm text-gray-400 font-medium">{{ statictranslations('No reviews yet') }}</span>
            @endif
        </div>

        <div class="flex gap-2 mb-4 pb-4 border-b border-gray-100">
            @if($warranty)
                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ $warranty }}</span>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3">
            <div class="flex-1">
                @if ($itemDiscountInfo['has_discount'])
                    <div class="flex flex-col">
                        <div>
                            <span class="inline-block text-xs text-gray-400 line-through opacity-75">
                                {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['original_price'], 2) }}
                            </span>
                        </div>
                        <span class="text-2xl font-bold text-red-600">
                            {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                        </span>
                    </div>
                @else
                    <span class="text-2xl font-bold text-primary">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </span>
                @endif
            </div>
            @php
                $user = Auth::user();
                $sessionId = session()->getId();
                if (! $user) {
                    $isInCart = false;
                } elseif ($isVariant) {
                    $isInCart = $product
                        ->cartItems()
                        ->whereHas('cart', function ($q) use ($user, $sessionId) {
                            $q->where('user_id', $user->id)
                                ->where(function ($inner) use ($sessionId) {
                                    $inner->whereNull('session_id')
                                        ->orWhere('session_id', $sessionId);
                                })
                                ->where(function ($inner) {
                                    $inner->whereNull('session_id')
                                        ->orWhere('session_id', 'not like', '%:buy-now');
                                });
                        })
                        ->exists();
                } else {
                    $isInCart = $master
                        ->cartItems()
                        ->whereHas('cart', function ($q) use ($user, $sessionId) {
                            $q->where('user_id', $user->id)
                                ->where(function ($inner) use ($sessionId) {
                                    $inner->whereNull('session_id')
                                        ->orWhere('session_id', $sessionId);
                                })
                                ->where(function ($inner) {
                                    $inner->whereNull('session_id')
                                        ->orWhere('session_id', 'not like', '%:buy-now');
                                });
                        })
                        ->exists();
                }
            @endphp
            @if ($isInCart)
                <button
                    class="add-to-cart-btn shrink-0 w-12 h-12 rounded-xl bg-primary/90 flex items-center justify-center cursor-not-allowed opacity-80"
                    disabled aria-disabled="true" title="{{ statictranslations('In Cart') }}"
                    data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            @elseif (!$isInCart && $stock > 0)
                <button
                    class="add-to-cart-btn shrink-0 w-12 h-12 rounded-xl bg-primary shadow-lg shadow-primary/30 flex items-center justify-center transition-all duration-300 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/40 hover:scale-105 active:scale-95"
                    onclick="addToCart('{{ $variantId }}', '{{ $master->id }}')"
                    data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}"
                    data-ga-item="{{ json_encode($ga4Item ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}"
                    @if (!$variantId) disabled @endif>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </button>
            @elseif ($stock <= 0)
                <button
                    class="shrink-0 w-12 h-12 rounded-xl bg-gray-300 flex items-center justify-center cursor-not-allowed"
                    disabled title="{{ statictranslations('Out of Stock') }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
