{{-- ═══════════════════════════════════════════════
     HORIZONTAL LIST CARD
     ═══════════════════════════════════════════════ --}}
<div class="group bg-white rounded-2xl overflow-hidden border-2 border-gray-100
            hover:border-primary/20 transition-all duration-300 hover:shadow-xl
            flex flex-row">

    {{-- ── Image (fixed square, left side) ── --}}
    <div class="relative shrink-0 w-44 sm:w-52 bg-gray-50 overflow-hidden">
        <a href="{{ $productUrl }}"
           class="block w-full h-full aspect-square">
            <img src="{{ $imagePath }}"
                 alt="{{ $name }}"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 loading="lazy">
        </a>

        {{-- Hover overlay --}}
        <div class="absolute inset-0 bg-linear-to-t from-black/30 to-transparent
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

        {{-- Badges ── top left --}}
        @if ($productDiscountInfo['has_discount'] || $master->created_at > now()->subDays(30) || $isBestseller)
            <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 z-10">
                @if ($productDiscountInfo['has_discount'])
                    <span class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg">
                        <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                        </svg>
                        @if ($productDiscountInfo['discount_percentage'] > 0)
                            {{ number_format($productDiscountInfo['discount_percentage'], 0) }}% {{ statictranslations('OFF') }}
                        @else
                            {{ money_format_number($productDiscountInfo['discount_amount']) }} {{ statictranslations('OFF') }}
                        @endif
                    </span>
                @endif
                @if ($master->created_at > now()->subDays(30))
                    <span class="inline-flex items-center gap-1 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg">
                        <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ statictranslations('NEW') }}
                    </span>
                @endif
                @if ($isBestseller)
                    <span class="inline-flex items-center gap-1 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg">
                        <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ statictranslations('BESTSELLER') }}
                    </span>
                @endif
            </div>
        @endif

        {{-- Wishlist button ── top right of image --}}
        <button type="button"
                aria-label="{{ statictranslations('Add to wishlist') }}"
                data-product-variant-id="{{ $variantId }}"
                data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                data-toast-ignore
                class="wishlist-btn no-toast absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-white shadow-md
                       flex items-center justify-center transition-all duration-200 hover:scale-110 z-10
                       focus:outline-none {{ $isWishlisted ? 'opacity-100 is-wishlisted' : 'opacity-0 group-hover:opacity-100' }}"
                @if (!$variantId) disabled @endif>
            @if ($isWishlisted)
                <svg class="w-4 h-4 text-red-500 transition-all duration-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z" />
                </svg>
            @else
                <svg class="w-4 h-4 text-gray-400 group-hover:text-red-400 transition-all duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            @endif
        </button>
    </div>

    {{-- ── Content (right side, fills remaining space) ── --}}
    <div class="flex flex-col flex-1 min-w-0 p-4 sm:p-5">

        {{-- Top row: category + stock --}}
        <div class="flex items-center justify-between gap-2 mb-2.5">
            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                         bg-primary/10 text-primary border border-primary/20 truncate max-w-[55%]">
                {{ $categoryName }}
            </span>
            @if ($stock > 0)
                <div class="flex items-center gap-1.5 text-xs font-medium text-green-600 shrink-0">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    {{ statictranslations('In Stock') }}
                </div>
            @else
                <div class="flex items-center gap-1.5 text-xs font-medium text-red-600 shrink-0">
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    {{ statictranslations('Out of Stock') }}
                </div>
            @endif
        </div>

        {{-- Product name --}}
        <a href="{{ $productUrl }}">
            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 mb-2.5
                       group-hover:text-primary transition-colors duration-200 leading-snug">
                {{ $name }}
            </h3>
        </a>

        {{-- Rating --}}
        <div class="flex items-center gap-2 mb-3">
            @if($reviewCount > 0)
                <div class="flex items-center text-yellow-400 text-sm">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))★
                        @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)◐
                        @else☆
                        @endif
                    @endfor
                </div>
                <span class="text-xs text-gray-600 font-medium">
                    {{ $averageRating }} <span class="text-gray-400">({{ $reviewCount }})</span>
                </span>
            @else
                <div class="flex items-center text-yellow-400 text-sm">☆☆☆☆☆</div>
                <span class="text-xs text-gray-400">{{ statictranslations('No reviews yet') }}</span>
            @endif
        </div>

        {{-- Warranty --}}
        @if($warranty)
            <div class="flex items-center gap-1.5 pb-3 mb-3 border-b border-gray-100">
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs text-gray-600 font-medium">{{ $warranty }}</span>
            </div>
        @endif

        {{-- Spacer pushes price+actions to bottom --}}
        <div class="flex-1"></div>

        {{-- Bottom row: price + actions --}}
        <div class="flex items-center justify-between gap-3 mt-auto">

            {{-- Price --}}
            <div class="flex-1 min-w-0">
                @if ($itemDiscountInfo['has_discount'])
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs text-gray-400 line-through">
                            {{ money_format_number($itemDiscountInfo['original_price']) }}
                        </span>
                        <span class="text-2xl font-bold text-red-600">
                            {{ money_format_number($itemDiscountInfo['final_price']) }}
                        </span>
                    </div>
                @else
                    <span class="text-2xl font-bold text-primary">
                        {{ money_format_number($itemDiscountInfo['final_price']) }}
                    </span>
                @endif
            </div>

            {{-- Actions: Compare + Cart --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Compare --}}
                <button type="button"
                        aria-label="{{ statictranslations('Compare product') }}"
                        data-product-variant-id="{{ $variantId }}"
                        data-init-compared="{{ $isCompared ? '1' : '0' }}"
                        class="compare-btn no-toast w-10 h-10 rounded-xl {{ $isCompared ? 'bg-blue-50 text-blue-700 is-compared' : 'bg-gray-100 text-blue-500' }}
                               flex items-center justify-center transition-all duration-200
                               hover:bg-blue-50 hover:scale-105 active:scale-95 focus:outline-none"
                        @if (!$variantId) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="7" width="7" height="13" rx="2" fill="currentColor" class="text-blue-100"/>
                        <rect x="14" y="3" width="7" height="17" rx="2" fill="currentColor" class="{{ $isCompared ? 'text-blue-500' : 'text-blue-300' }}"/>
                        <rect x="3" y="7" width="7" height="13" rx="2"/>
                        <rect x="14" y="3" width="7" height="17" rx="2"/>
                    </svg>
                </button>

                {{-- Add to cart / Out of stock (same as product-card) --}}
                @if ($isInCart)
                    <button class="add-to-cart-btn w-10 h-10 rounded-xl bg-primary/90 flex items-center justify-center cursor-not-allowed opacity-80"
                        disabled aria-disabled="true" title="{{ statictranslations('In Cart') }}"
                        data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                @elseif (!$isInCart && $stock > 0)
                    <button class="add-to-cart-btn w-10 h-10 rounded-xl bg-primary shadow-lg shadow-primary/30 flex items-center justify-center transition-all duration-300 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/40 hover:scale-105 active:scale-95"
                        onclick="addToCart('{{ $variantId }}', '{{ $master->id }}')"
                        data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}"
                        @if (!$variantId) disabled @endif>
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                @elseif ($stock <= 0)
                    <button class="w-10 h-10 rounded-xl bg-gray-200 flex items-center justify-center cursor-not-allowed" disabled title="{{ statictranslations('Out of Stock') }}">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                @endif

            </div>
        </div>
    </div>
</div>
