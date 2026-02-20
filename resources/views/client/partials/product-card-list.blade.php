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

    $variantImage = null;
    if ($isVariant) {
        $variantImage = optional($product->images()->ordered()->first())->image_path;
    }

    $imagePath = $master->cover_image
        ? asset('storage/' . $master->cover_image)
        : asset('icon/product-placeholder-A.jpg');

    $real = $isVariant
        ? $product->price ?? ($master->real_price ?? ($master->price ?? 0))
        : $master->real_price ?? ($master->price ?? 0);
    $itemDiscountInfo = $isVariant ? $product->getDiscountInfo() : $master->getDiscountInfo();
    $productDiscountInfo = $master->getDiscountInfo();

    $variantId = $isVariant
        ? $product->id ?? null
        : ($master->productVariants && $master->productVariants->count() > 0
            ? $master->productVariants->first()->id
            : $master->id);
    $isWishlisted = isset($wishlistVariantIds) && $variantId ? in_array($variantId, $wishlistVariantIds) : false;

    $isBestseller = false;
    if ($isVariant) {
        $isBestseller = (bool) $product->is_bestseller;
    } else {
        $firstVariant = $master->productVariants?->first();
        $isBestseller = $firstVariant && (bool) $firstVariant->is_bestseller;
    }

    if ($isVariant) {
        $stock = $product->getTotalStockAttribute() ?? 0;
    } else {
        if ($master->productVariants && $master->productVariants->count() > 0) {
            $stock = $master->productVariants->sum(function ($v) {
                return method_exists($v, 'getTotalStockAttribute') ? $v->getTotalStockAttribute() : 0;
            });
        } else {
            $stock = method_exists($master, 'getTotalStockAttribute') ? $master->getTotalStockAttribute() : 0;
        }
    }

    if ($isVariant) {
        $reviews = $product->reviews ?? collect();
    } else {
        $reviews = collect();
        if ($master->productVariants && $master->productVariants->count() > 0) {
            foreach ($master->productVariants as $variant) {
                if ($variant->reviews) {
                    $reviews = $reviews->merge($variant->reviews);
                }
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
@endphp

<div class="product-card group bg-white rounded-2xl overflow-hidden transition-all duration-300
            border border-gray-100 hover:border-primary/20
            shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:shadow-[0_2px_4px_rgba(0,0,0,0.06)]
            hover:-translate-y-1.5 relative">

    {{-- Image area --}}
    <div class="relative aspect-square bg-secondary-bg overflow-hidden">
        <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}"
            class="block w-full h-full">
            <img src="{{ $imagePath ?? 'https://via.placeholder.com/400x400?text=No+Image' }}"
                 alt="{{ $name }}"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 loading="lazy">
        </a>

        {{-- Gradient overlay on hover --}}
        <div class="absolute inset-0 bg-linear-to-t from-black/30 via-transparent to-transparent
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

        {{-- Badges --}}
        @if ($productDiscountInfo['has_discount'] || $master->created_at > now()->subDays(30) || $isBestseller)
            <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                @if ($productDiscountInfo['has_discount'])
                    <span class="inline-flex items-center gap-1 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm product-badge">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                        </svg>
                        @if ($productDiscountInfo['discount_percentage'] > 0)
                            {{ number_format($productDiscountInfo['discount_percentage'], 0) }}% OFF
                        @else
                            ${{ number_format($productDiscountInfo['discount_amount'], 2) }} OFF
                        @endif
                    </span>
                @endif

                @if ($master->created_at > now()->subDays(30))
                    <span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm product-badge">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        NEW
                    </span>
                @endif

                @if ($isBestseller)
                    <span class="inline-flex items-center gap-1 bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm product-badge">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        BESTSELLER
                    </span>
                @endif
            </div>
        @endif

        {{-- Wishlist button --}}
        <button type="button"
                aria-label="Add to wishlist"
                data-product-variant-id="{{ $variantId }}"
                data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                data-toast-ignore
                class="wishlist-btn no-toast absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-sm shadow-md
                       flex items-center justify-center transition-all duration-200 hover:scale-110 hover:bg-white z-10
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-primary
                       {{ $isWishlisted ? 'opacity-100 is-wishlisted' : 'opacity-0 group-hover:opacity-100' }}"
                @if (!$variantId) disabled @endif>
            @if ($isWishlisted)
                <svg class="w-4.5 h-4.5 text-red-500 transition-all duration-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z" />
                </svg>
            @else
                <svg class="w-4.5 h-4.5 text-gray-400 transition-all duration-200 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z" />
                </svg>
            @endif
        </button>
    </div>

    {{-- Content --}}
    <div class="p-4">
        {{-- Category + Stock row --}}
        <div class="flex items-center justify-between mb-2.5">
            <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/15 truncate max-w-[65%]">
                {{ $categoryName }}
            </span>
            @if ($stock > 0)
                <div class="flex items-center gap-1 text-xs font-medium text-emerald-600 shrink-0">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    In Stock
                </div>
            @else
                <div class="flex items-center gap-1 text-xs font-medium text-red-500 shrink-0">
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                    Out of Stock
                </div>
            @endif
        </div>

        {{-- Product name --}}
        <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}">
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 mb-2.5 group-hover:text-primary transition-colors duration-200 leading-snug min-h-10">
                {{ $name }}
            </h3>
        </a>

        {{-- Rating --}}
        <div class="flex items-center gap-1.5 mb-3">
            @if($reviewCount > 0)
                <div class="flex items-center gap-0.5">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" opacity="0.4"/><path d="M10 2.927l.951 2.922a1 1 0 00.95.69h3.074l-2.487 1.808a1 1 0 00-.364 1.118l.951 2.921-2.487-1.807A1 1 0 0010 11.168V2.927z" fill="currentColor"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endif
                    @endfor
                </div>
                <span class="text-xs text-gray-500 font-medium">{{ $averageRating }} <span class="text-gray-400">({{ $reviewCount }})</span></span>
            @else
                <div class="flex items-center gap-0.5">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-3.5 h-3.5 text-gray-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-400">No reviews</span>
            @endif
        </div>

        {{-- Warranty --}}
        @if($warranty)
            <div class="flex items-center gap-1.5 mb-3 pb-3 border-b border-gray-100">
                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs text-gray-500 font-medium">{{ $warranty }}</span>
            </div>
        @endif

        {{-- Price + Add to cart --}}
        <div class="flex items-center justify-between gap-2 mt-1">
            <div class="flex-1 min-w-0">
                @if ($itemDiscountInfo['has_discount'])
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs text-gray-400 line-through">
                            {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['original_price'], 2) }}
                        </span>
                        <span class="text-xl font-bold text-red-500">
                            {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                        </span>
                    </div>
                @else
                    <span class="text-xl font-bold text-primary">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </span>
                @endif
            </div>

            @php
                $user = Auth::user();
                if ($isVariant) {
                    $isInCart = $product->cartItems()->whereHas('cart', function ($q) use ($user) { $q->where('user_id', $user?->id); })->exists();
                } else {
                    $isInCart = $master->cartItems()->whereHas('cart', function ($q) use ($user) { $q->where('user_id', $user?->id); })->exists();
                }
            @endphp

            @if (!$isInCart && $stock > 0)
                <button
                    class="add-to-cart-btn shrink-0 w-10 h-10 rounded-xl bg-primary shadow-md shadow-primary/25
                           flex items-center justify-center transition-all duration-300
                           hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/35 hover:scale-105 active:scale-95"
                    onclick="addToCart('{{ $variantId }}', '{{ $master->id }}')"
                    data-product-variant-id="{{ $variantId }}"
                    data-product-id="{{ $master->id }}"
                    @if (!$variantId) disabled @endif>
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </button>
            @elseif ($stock <= 0)
                <button class="shrink-0 w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center cursor-not-allowed" disabled title="Out of Stock">
                    <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </button>
            @elseif ($isInCart)
                <a href="{{ route('cart.index') }}"
                   class="shrink-0 w-10 h-10 rounded-xl bg-emerald-500 shadow-md shadow-emerald-500/25
                          flex items-center justify-center transition-all duration-300
                          hover:bg-emerald-600 hover:scale-105 active:scale-95">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </a>
            @endif
        </div>
    </div>
</div>
