@php
    // --- Data Preparation (supports Product or ProductVariant) ---
    $locale = app()->getLocale() ?? 'en';
    $isVariant = isset($product) && class_basename($product) === 'ProductVariant';
    $master = $isVariant ? $product->product : $product;

    $nameRaw = $master?->getTranslation('name', $locale) ?? '';
    $variantName = $isVariant ? $product->getTranslation('variant_name', $locale) ?? '' : '';
    $name = is_array($nameRaw) ? implode(' ', array_filter($nameRaw)) : (string) ($nameRaw ?? '');
    if ($variantName) {
        $v = is_array($variantName) ? implode(' ', $variantName) : $variantName;
        $name .= ' - ' . $v;
    }

    // Description handling
    $descRaw = $master?->getTranslation('description', $locale) ?? '';
    if (is_array($descRaw)) {
        $descRaw = implode(
            ' ',
            array_map(function ($v) {
                return is_array($v) ? implode(' ', $v) : $v;
            }, $descRaw),
        );
    }
    $description = Str::limit(strip_tags((string) $descRaw), 120);

    // Image Logic (prefer variant images, then product images, then cover, then placeholder)
    $variantImage = null;
    if ($isVariant) {
        $variantImage = optional($product->images()->ordered()->first())->image_path;
    }
    $productImage = optional($master->images()->forProduct($master->id)->ordered()->first())->image_path;

    if ($variantImage) {
        $imagePath = asset('storage/' . $variantImage);
    } elseif ($productImage) {
        $imagePath = asset('storage/' . $productImage);
    } elseif ($master->cover_image) {
        $imagePath = asset('storage/' . $master->cover_image);
    } else {
        $imagePath = 'https://via.placeholder.com/400x400?text=No+Image';
    }

    // Get discount info
    $real = $isVariant
        ? $product->price ?? ($master->real_price ?? ($master->price ?? 0))
        : $master->real_price ?? ($master->price ?? 0);
    $itemDiscountInfo = $isVariant ? $product->getDiscountInfo() : $master->getDiscountInfo();
    $productDiscountInfo = $master->getDiscountInfo();

    // Bestseller check
    $isBestseller = false;
    if ($isVariant) {
        $isBestseller = (bool) $product->is_bestseller;
    } else {
        $firstVariant = $master->productVariants?->first();
        $isBestseller = $firstVariant && (bool) $firstVariant->is_bestseller;
    }

    // Category
    $catRaw = $master->category?->name ?? 'General';
    $categoryName = is_array($catRaw) ? implode(' ', array_filter($catRaw)) : (string) $catRaw;
    // Ensure $variantId is always defined for use in buttons
    $variantId = $isVariant ? $product->id ?? null : $master->productVariants()->first()?->id ?? null;
@endphp

<div
    class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary transition-all duration-300 overflow-hidden">
    <div class="flex flex-col md:flex-row h-full">

        <div class="w-full md:w-64 md:shrink-0 bg-gray-50 relative p-4 flex items-center justify-center overflow-hidden">
            <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}"
                class="block w-full h-48 md:h-full">
                <img src="{{ $imagePath }}" alt="{{ $name }}"
                    class="w-full h-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105">
            </a>

            <div class="absolute top-3 left-3 flex flex-col gap-2">
                @if ($productDiscountInfo['has_discount'])
                    <span
                        class="inline-flex items-center gap-1 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        @if ($productDiscountInfo['discount_percentage'] > 0)
                            -{{ number_format($productDiscountInfo['discount_percentage'], 0) }}% OFF
                        @else
                            ${{ number_format($productDiscountInfo['discount_amount'], 2) }} OFF
                        @endif
                    </span>
                @endif
                @if ($master->created_at > now()->subDays(30))
                    <span
                        class="inline-flex items-center gap-1 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        NEW
                    </span>
                @endif
                @if ($isBestseller)
                    <span
                        class="inline-flex items-center gap-1 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        BESTSELLER
                    </span>
                @endif
            </div>
        </div>

        <div class="flex-1 p-5 md:p-6 flex flex-col border-b md:border-b-0 md:border-r border-gray-100">

            <div class="flex items-center justify-between mb-2">
                <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}"
                    class="text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-primary transition">
                    {{ $categoryName }}
                </a>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-600">4.5</span>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                <a
                    href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}">
                    {{ $name }}
                </a>
            </h3>

            <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                {{ $description }}
            </p>
        </div>

        <div
            class="w-full md:w-56 bg-gray-50/50 p-5 md:p-6 flex flex-col justify-center items-center md:items-end gap-4 border-t md:border-t-0">

            <div class="text-center md:text-right">
                @if ($itemDiscountInfo['has_discount'])
                    <div class="mb-1">
                        <span class="inline-block text-xs text-gray-400 line-through opacity-75">
                            {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['original_price'], 2) }}
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </div>
                @else
                    <div class="text-2xl font-bold text-primary">
                        {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
                    </div>
                @endif
                @php
                    // Stock calculation (match grid card logic)
                    if ($isVariant) {
                        $stock = $product->getTotalStockAttribute() ?? 0;
                    } else {
                        if ($master->productVariants && $master->productVariants->count() > 0) {
                            $stock = $master->productVariants->sum(function ($v) {
                                return method_exists($v, 'getTotalStockAttribute') ? $v->getTotalStockAttribute() : 0;
                            });
                        } else {
                            $stock = method_exists($master, 'getTotalStockAttribute')
                                ? $master->getTotalStockAttribute()
                                : 0;
                        }
                    }
                @endphp
                @if ($stock > 0)
                    <div class="text-xs font-medium mt-1 flex items-center justify-end gap-1 text-green-600">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        In Stock
                        <span class="ml-1 text-gray-400">({{ $stock }})</span>
                    </div>
                @else
                    <div class="text-xs font-medium mt-1 flex items-center justify-end gap-1 text-red-600">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Out of Stock
                    </div>
                @endif
            </div>

            <div class="w-full space-y-2">

                @php
                    $user = Auth::user();
                    if ($isVariant) {
                        $isInCart = $product
                            ->cartItems()
                            ->whereHas('cart', function ($q) use ($user) {
                                $q->where('user_id', $user?->id);
                            })
                            ->exists();
                    } else {
                        $isInCart = $master
                            ->cartItems()
                            ->whereHas('cart', function ($q) use ($user) {
                                $q->where('user_id', $user?->id);
                            })
                            ->exists();
                    }
                @endphp

                @if (!$isInCart && $stock > 0)
                    <button
                        class="add-to-cart-btn w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-primary text-white font-semibold shadow-md hover:bg-primary/90 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-60 disabled:cursor-not-allowed"
                        onclick="addToCart('{{ $variantId }}', '{{ $master->id }}')"
                        data-product-variant-id="{{ $variantId }}" data-product-id="{{ $master->id }}"
                        @if (!$variantId) disabled @endif
                        aria-label="Add to Cart"
                        title="Add to Cart">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Add to Cart</span>
                    </button>
                @elseif ($stock <= 0)
                    <button
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-gray-200 text-gray-500 font-semibold shadow-inner cursor-not-allowed border border-gray-300"
                        disabled
                        aria-label="Out of Stock"
                        title="Out of Stock">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Out of Stock</span>
                    </button>
                @else
                    <button
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-green-500 text-white font-semibold shadow-md cursor-not-allowed border border-green-600"
                        disabled
                        aria-label="Added to Cart"
                        title="Added to Cart">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Added</span>
                    </button>
                @endif

                <div class="flex gap-2 mt-2">
                    @php
                        $variantId = $isVariant
                            ? $product->id ?? null
                            : $master->productVariants()->first()?->id ?? null;
                        $isWishlisted =
                            isset($wishlistVariantIds) && $variantId
                                ? in_array($variantId, $wishlistVariantIds)
                                : false;
                    @endphp
                    <button type="button" data-product-variant-id="{{ $variantId }}"
                        data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                        class="wishlist-btn flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors
                            {{ $isWishlisted ? 'bg-red-50 border-red-400 text-red-600 hover:bg-red-100 hover:border-red-500' : 'bg-white border-gray-300 text-gray-700 hover:text-primary hover:border-primary hover:bg-primary/5' }}
                            disabled:opacity-60 disabled:cursor-not-allowed"
                        title="Add to Wishlist" @if (!$variantId) disabled @endif>
                        <svg class="w-4 h-4 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400 group-hover:text-primary' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                        <span>{{ $isWishlisted ? 'Wishlisted' : 'Save' }}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
