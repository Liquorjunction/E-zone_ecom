@php
    $locale = app()->getLocale() ?? 'en';
    $isVariant = isset($product) && class_basename($product) === 'ProductVariant';
    $master = $isVariant ? $product->product : $product;

    $nameRaw = $master?->getTranslation('name', $locale) ?? '';
    $variantName = $isVariant ? $product->getTranslation('variant_name', $locale) ?? '' : '';
    $name = $isVariant
        ? (is_array($variantName)
            ? implode(' ', array_filter($variantName))
            : (string) $variantName)
        : (is_array($nameRaw)
            ? implode(' ', array_filter($nameRaw))
            : (string) $nameRaw);

    $catRaw = $master->category?->{'category-name'} ?? ($master->category?->name ?? 'Uncategorized');
    $categoryName = is_array($catRaw) ? implode(' ', array_filter($catRaw)) : (string) $catRaw;

    $imagePath = $master->cover_image
        ? asset('storage/' . $master->cover_image)
        : asset('icon/product-placeholder-A.jpg');

    $itemDiscountInfo = $isVariant ? $product->getDiscountInfo() : $master->getDiscountInfo();
    $productDiscountInfo = $master->getDiscountInfo();

    $variantId = $isVariant
        ? $product->id ?? null
        : ($master->productVariants && $master->productVariants->count() > 0
            ? $master->productVariants->first()->id
            : $master->id);
    $isWishlisted = isset($wishlistVariantIds) && $variantId ? in_array($variantId, $wishlistVariantIds) : false;
    $compareVariantIds = collect(session('compare_variant_ids', []))->map(fn($id) => (int) $id)->all();
    $isCompared = $variantId ? in_array((int) $variantId, $compareVariantIds, true) : false;

    $isBestseller = false;
    if ($isVariant) {
        $isBestseller = (bool) $product->is_bestseller;
    } else {
        $firstVariant = $master->productVariants?->first();
        $isBestseller = $firstVariant && (bool) $firstVariant->is_bestseller;
    }

    if ($isVariant) {
        $stock = method_exists($product, 'getSellableStock') ? $product->getSellableStock() : 0;
    } else {
        if ($master->productVariants && $master->productVariants->count() > 0) {
            $stock = $master->productVariants->sum(function ($v) {
                return method_exists($v, 'getSellableStock') ? $v->getSellableStock() : 0;
            });
        } else {
            $stock = 0;
        }
    }

    if ($isVariant) {
        $reviews = $product->reviews ?? collect();
    } else {
        $reviews = collect();
        if ($master->productVariants && $master->productVariants->count() > 0) {
            foreach ($master->productVariants as $variant) {
                if ($variant->reviews) $reviews = $reviews->merge($variant->reviews);
            }
        } elseif ($master->reviews) {
            $reviews = $master->reviews;
        }
    }

    $approvedReviews = $reviews->where('is_approved', 'approved');
    $reviewCount = $approvedReviews->count();
    $averageRating = $reviewCount > 0 ? round($approvedReviews->avg('rating'), 1) : 0;

    $warrantyRaw = $master->getTranslation('warranty', $locale) ?? '';
    $warranty = is_array($warrantyRaw) ? implode(' ', array_filter($warrantyRaw)) : (string) $warrantyRaw;
    $warranty = $warranty ?: '1 Year Warranty';

    $user = Auth::user();
    if ($isVariant) {
        $isInCart = $product->cartItems()->whereHas('cart', function ($q) use ($user) {
            $q->where('user_id', $user?->id);
        })->exists();
    } else {
        $isInCart = $master->cartItems()->whereHas('cart', function ($q) use ($user) {
            $q->where('user_id', $user?->id);
        })->exists();
    }

    $productUrl = route('products.detail', $master->slug) . ($isVariant ? '?variant=' . ($product->slug ?? '') : '');
@endphp

{{-- ═══════════════════════════════════════════════════════
     MOBILE PRODUCT CARD
     Designed for 2-column grids on small screens.
     Touch targets ≥ 44px, critical info above the fold.
     ═══════════════════════════════════════════════════════ --}}
<div class="group relative bg-white rounded-2xl overflow-hidden
            border border-gray-100 active:border-primary/30
            shadow-sm active:shadow-md
            transition-all duration-200 flex flex-col">

    {{-- ── IMAGE AREA ── --}}
    <div class="relative bg-gray-50 overflow-hidden" style="aspect-ratio: 1;">

        <a href="{{ $productUrl }}" class="block w-full h-full">
            <img src="{{ $imagePath }}"
                 alt="{{ $name }}"
                 class="w-full h-full object-cover transition-transform duration-500 active:scale-105"
                 loading="lazy">
        </a>

        {{-- Soft gradient at bottom for readability --}}
        <div class="absolute inset-x-0 bottom-0 h-1/3
                    bg-linear-to-t from-black/20 to-transparent pointer-events-none"></div>

        {{-- ── Badges (top-left) ── --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
            @if ($productDiscountInfo['has_discount'])
                <span class="inline-flex items-center gap-0.5 bg-red-500 text-white
                             text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm leading-none">
                    @if ($productDiscountInfo['discount_percentage'] > 0)
                        {{ number_format($productDiscountInfo['discount_percentage'], 0) }}% OFF
                    @else
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($productDiscountInfo['discount_amount'], 2) }} OFF
                    @endif
                </span>
            @endif
            @if ($master->created_at > now()->subDays(30))
                <span class="inline-flex items-center bg-green-500 text-white
                             text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm leading-none">
                    {{ statictranslations('NEW') }}
                </span>
            @endif
            @if ($isBestseller)
                <span class="inline-flex items-center bg-amber-500 text-white
                             text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm leading-none">
                    🔥 {{ statictranslations('HOT') }}
                </span>
            @endif
        </div>

        {{-- ── Stock indicator (top-right) ── --}}
        <div class="absolute top-2 right-2 z-10">
            @if ($stock > 0)
                <span class="flex items-center gap-1 bg-white/90 backdrop-blur-sm
                             text-green-600 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm leading-none">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                    {{ statictranslations('In Stock') }}
                </span>
            @else
                <span class="flex items-center gap-1 bg-white/90 backdrop-blur-sm
                             text-red-500 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm leading-none">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>
                    {{ statictranslations('Out of Stock') }}
                </span>
            @endif
        </div>

        {{-- ── Wishlist button (bottom-right of image, always visible on mobile) ── --}}
        <button type="button"
                aria-label="{{ statictranslations('Add to wishlist') }}"
                data-product-variant-id="{{ $variantId }}"
                data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                data-toast-ignore
                class="wishlist-btn no-toast absolute bottom-2 right-2 z-10
                       w-8 h-8 rounded-full shadow-md
                       flex items-center justify-center
                       transition-all duration-200 active:scale-95 focus:outline-none
                       {{ $isWishlisted
                            ? 'bg-red-50 border border-red-200 is-wishlisted'
                            : 'bg-white/90 backdrop-blur-sm border border-white/60' }}"
                @if (!$variantId) disabled @endif>
            @if ($isWishlisted)
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z"/>
                </svg>
            @else
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            @endif
        </button>

        {{-- ── Compare button (bottom-left of image, always visible on mobile) ── --}}
        <button type="button"
                aria-label="{{ statictranslations('Compare product') }}"
                data-product-variant-id="{{ $variantId }}"
                data-init-compared="{{ $isCompared ? '1' : '0' }}"
                class="compare-btn no-toast absolute bottom-2 left-2 z-10
                       w-8 h-8 rounded-full {{ $isCompared ? 'bg-blue-50 border-blue-200 is-compared' : 'bg-white/90 border-white/60' }} backdrop-blur-sm border shadow-md
                       flex items-center justify-center transition-all duration-200 active:scale-95 focus:outline-none"
                @if (!$variantId) disabled @endif>
            <svg class="w-4 h-4 {{ $isCompared ? 'text-blue-700' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="7" width="7" height="13" rx="2" fill="currentColor" class="text-blue-100"/>
                <rect x="14" y="3" width="7" height="17" rx="2" fill="currentColor" class="{{ $isCompared ? 'text-blue-500' : 'text-blue-300' }}"/>
                <rect x="3" y="7" width="7" height="13" rx="2"/>
                <rect x="14" y="3" width="7" height="17" rx="2"/>
            </svg>
        </button>
    </div>

    {{-- ── CONTENT AREA ── --}}
    <div class="flex flex-col flex-1 p-3">

        {{-- Category pill --}}
        <span class="self-start text-[10px] font-semibold px-2 py-0.5 rounded-full
                     bg-primary/10 text-primary border border-primary/15 truncate max-w-full mb-1.5 leading-tight">
            {{ $categoryName }}
        </span>

        {{-- Product name --}}
        <a href="{{ $productUrl }}" class="block mb-1.5">
            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug
                       active:text-primary transition-colors duration-150">
                {{ $name }}
            </h3>
        </a>

        {{-- Rating — compact --}}
        <div class="flex items-center gap-1 mb-2">
            @if($reviewCount > 0)
                <div class="flex items-center text-yellow-400 text-[11px] leading-none">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))★
                        @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)◐
                        @else<span class="text-gray-300">☆</span>
                        @endif
                    @endfor
                </div>
                <span class="text-[10px] text-gray-500 leading-none">{{ $averageRating }} ({{ $reviewCount }})</span>
            @else
                <div class="flex items-center text-gray-200 text-[11px] leading-none">☆☆☆☆☆</div>
                <span class="text-[10px] text-gray-400 leading-none">{{ statictranslations('No reviews') }}</span>
            @endif
        </div>

        {{-- Warranty — compact single line --}}
        @if($warranty)
            <div class="flex items-center gap-1 mb-2">
                <svg class="w-3 h-3 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[10px] text-gray-500 truncate">{{ $warranty }}</span>
            </div>
        @endif

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- ── Price + Cart (bottom row) ── --}}
        <div class="flex items-end justify-between gap-1 pt-2 mt-1 border-t border-gray-100">

            {{-- Price --}}
            <div class="flex flex-col leading-none min-w-0">
                @if ($itemDiscountInfo['has_discount'])
                    <span class="text-[10px] text-gray-400 line-through mb-0.5">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['original_price'], 2) }}
                    </span>
                    <span class="text-base font-black text-red-600 leading-tight">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </span>
                @else
                    <span class="text-base font-black text-primary leading-tight">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </span>
                @endif
            </div>

            {{-- Cart button — large touch target ── --}}
            @if (!$isInCart && $stock > 0)
                <button class="add-to-cart-btn shrink-0 w-10 h-10 rounded-xl bg-primary shadow-md shadow-primary/30 flex items-center justify-center transition-all duration-200 active:scale-90 active:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                    onclick="addToCart('{{ $variantId }}', '{{ $master->id }}')"
                    data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}"
                    @if (!$variantId) disabled @endif>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </button>
            @elseif ($stock <= 0)
                <button class="shrink-0 w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center cursor-not-allowed opacity-50"
                    disabled title="{{ statictranslations('Out of Stock') }}">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
