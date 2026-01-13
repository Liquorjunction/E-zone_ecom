@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            {!! Breadcrumbs::render('products.detail', $product) !!}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-12 mb-12">

                <!-- Image Gallery Section -->
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-4">
                        <div class="aspect-square relative group">
                            <img id="main-product-image"
                                src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                alt="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }}"
                                class="w-full h-full object-contain p-4 transition-transform duration-300" loading="lazy"
                                decoding="async">

                            <!-- Badges -->
                            @php
                                $currentVariant = null;
                                if (request()->has('variant')) {
                                    $requestedSlug = request()->get('variant');
                                    $currentVariant = $product->productVariants->where('slug', $requestedSlug)->first();
                                }
                                if (!$currentVariant && $product->productVariants && $product->productVariants->count() > 0) {
                                    $currentVariant = $product->productVariants->first();
                                }
                            @endphp
                            <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                                @if ($product->created_at > now()->subDays(30))
                                    <span
                                        class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg">
                                        NEW
                                    </span>
                                @endif
                                @if ($currentVariant && $currentVariant->is_bestseller)
                                    <span
                                        class="bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg">
                                        BESTSELLER
                                    </span>
                                @endif
                            </div>

                            <!-- Wishlist Button -->
                            @php
                                $variantId =
                                    class_basename($product) === 'ProductVariant'
                                        ? $product->id ?? null
                                        : $product->productVariants->first()?->id ?? null;
                                $isWishlisted =
                                    isset($wishlistVariantIds) && $variantId
                                        ? in_array($variantId, $wishlistVariantIds)
                                        : false;
                            @endphp
                            <button type="button" data-product-variant-id="{{ $variantId }}"
                                data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                                class="wishlist-btn absolute top-4 right-4 w-11 h-11 rounded-xl bg-white/90 backdrop-blur-sm shadow-lg flex items-center justify-center hover:bg-red-50 transition-all z-10 group/heart {{ $isWishlisted ? 'is-wishlisted' : '' }}"
                                @if (!$variantId) disabled @endif>
                                <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500' : 'text-gray-600' }} group-hover/heart:text-red-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>

                            <!-- Zoom Indicator -->
                            <div
                                class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                Click to zoom
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        <div class="p-4 border-t border-gray-200 bg-gray-50">
                            <div class="grid grid-cols-5 gap-2">
                                @if ($product->images && $product->images->count() > 0)
                                    @foreach ($product->images->take(5) as $image)
                                        <button type="button" data-image="{{ asset('storage/' . $image->image_path) }}"
                                            class="thumb-button bg-white rounded-lg shadow-sm border-2 border-gray-200 overflow-hidden hover:border-primary transition-all focus:border-primary focus:ring-2 focus:ring-primary/30 aspect-square">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                alt="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }} - {{ $loop->iteration }}"
                                                class="w-full h-full object-cover" loading="lazy" decoding="async">
                                        </button>
                                    @endforeach
                                @else
                                    <button type="button"
                                        class="thumb-button bg-white rounded-lg shadow-sm border-2 border-primary overflow-hidden aspect-square ring-2 ring-primary/30"
                                        data-image="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}">
                                        <img src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                            alt="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }}"
                                            class="w-full h-full object-cover" loading="lazy" decoding="async">
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info Section -->
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="space-y-4">
                        <!-- Tags -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1">
                                Electronics
                            </span>
                            <span id="product-sku"
                                class="inline-flex items-center gap-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1">
                                SKU:
                                {{ is_array($product->sku) ? $product->sku[app()->getLocale()] ?? ($product->sku['en'] ?? '') : $product->sku ?? '' }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 id="product-title"
                            data-base-name="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }}"
                            class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                            {{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }}
                        </h1>

                        <!-- Rating & Stock -->
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center text-yellow-400 text-lg">
                                    ★★★★★
                                </div>
                                <span class="text-gray-600 text-sm font-medium">(247 reviews)</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-sm font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                In Stock
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3" />
                                </svg>
                                Free returns 30d
                            </span>
                        </div>
                    </div>

                    <!-- Price Box -->
                    <div
                        class="bg-linear-to-r from-primary/5 via-primary/10 to-primary/5 border-2 border-primary/20 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-baseline gap-3">
                                @php
                                    $variantCount = $product->productVariants ? $product->productVariants->count() : 0;
                                    // Use discount info passed from controller, or fetch it if not available
                                    if (!isset($discountInfo) || !is_array($discountInfo)) {
                                        $discountInfo = $product->getDiscountInfo();
                                    }
                                    if (!isset($variantDiscounts)) {
                                        $variantDiscounts = [];
                                    }

                                    $hasDiscount = isset($discountInfo['has_discount']) && $discountInfo['has_discount'] === true && ($discountInfo['original_price'] ?? 0) > ($discountInfo['final_price'] ?? 0);
                                    $originalPrice = $discountInfo['original_price'] ?? 0;
                                    $finalPrice = $discountInfo['final_price'] ?? 0;
                                    $discountPercentage = $hasDiscount ? ($discountInfo['discount_percentage'] ?? 0) : 0;
                                    $discountName = $discountInfo['discount_name'] ?? '';
                                @endphp

                                @if ($variantCount > 1)
                                    @if ($hasDiscount)
                                        <div class="flex flex-col gap-2">
                                            <span class="text-2xl text-gray-400 line-through">${{ number_format($originalPrice, 2) }}</span>
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-sm text-gray-600 font-medium">From</span>
                                                <span id="product-price" class="text-4xl font-bold text-red-600">${{ number_format($finalPrice, 2) }}</span>
                                            </div>
                                            @if ($discountPercentage > 0)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-500 text-white text-sm font-bold self-start">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                                    </svg>
                                                    Save {{ number_format($discountPercentage, 0) }}%
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $minPrice = $product->productVariants->min('price');
                                        @endphp
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-sm text-gray-600 font-medium">From</span>
                                            <span id="product-price" class="text-4xl font-bold text-primary">${{ number_format($minPrice, 2) }}</span>
                                        </div>
                                    @endif
                                @else
                                    @if ($hasDiscount)
                                        <div class="flex flex-col gap-2">
                                            <span class="text-2xl text-gray-400 line-through">${{ number_format($originalPrice, 2) }}</span>
                                            <span id="product-price" class="text-4xl font-bold text-red-600">${{ number_format($finalPrice, 2) }}</span>
                                            @if ($discountPercentage > 0)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-500 text-white text-sm font-bold self-start">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                                    </svg>
                                                    Save {{ number_format($discountPercentage, 0) }}%
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span id="product-price" class="text-4xl font-bold text-primary">${{ number_format($finalPrice > 0 ? $finalPrice : $product->price, 2) }}</span>
                                    @endif
                                @endif

                                <span class="text-sm text-gray-500 font-medium">incl. taxes</span>
                            </div>
                        </div>
                        @if ($hasDiscount && !empty($discountName))
                            <div
                                class="mt-4 flex items-center gap-2 px-4 py-3 bg-green-50 rounded-lg border border-green-200">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-semibold text-green-800">{{ $discountName }} applied!</span>
                            </div>
                        @endif
                    </div>

                    <!-- Category & Brand -->
                    <div class="flex flex-wrap gap-4 text-sm">
                        @if ($product->category)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium">Category:</span>
                                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                                    class="text-primary hover:text-primary-dark font-semibold transition-colors">
                                    {{ method_exists($product->category ?? null, 'getTranslation') ? $product->category->getTranslation('category-name', app()->getLocale()) : (is_array(optional($product->category)->{'category-name'}) ? optional($product->category)->{'category-name'}[app()->getLocale()] ?? (optional($product->category)->{'category-name'}['en'] ?? '') : optional($product->category)->{'category-name'}) }}
                                </a>
                            </div>
                        @endif
                        @if ($product->brand)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium">Brand:</span>
                                <a href="{{ route('brand-products', $product->brand->slug) }}"
                                    class="text-primary hover:text-primary-dark font-semibold transition-colors">
                                    {{ method_exists($product->brand ?? null, 'getTranslation') ? $product->brand->getTranslation('name', app()->getLocale()) : (is_array(optional($product->brand)->name) ? optional($product->brand)->name[app()->getLocale()] ?? (optional($product->brand)->name['en'] ?? '') : optional($product->brand)->name) }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Short Description -->
                    <div class="prose prose-sm max-w-none prose-content">
                        <div class="text-gray-600 leading-relaxed line-clamp-3">
                            {!! is_array($product->description)
                                ? $product->description[app()->getLocale()] ?? ($product->description['en'] ?? '')
                                : $product->description !!}
                        </div>
                    </div>

                    @php
                        $rawSpecs = $product->specifications;
                        if (is_string($rawSpecs)) {
                            $decoded = json_decode($rawSpecs, true);
                            $rawSpecs = is_array($decoded) ? $decoded : null;
                        }

                        $specifications = [];
                        if (is_array($rawSpecs)) {
                            foreach ($rawSpecs as $key => $item) {
                                if (
                                    is_array($item) &&
                                    (isset($item['title']) || isset($item['value']) || isset($item['description']))
                                ) {
                                    $title = $item['title'] ?? ($item['label'] ?? $key);
                                    $value = $item['value'] ?? ($item['description'] ?? '');
                                } else {
                                    $title = is_numeric($key) ? 'Spec ' . ($key + 1) : $key;
                                    $value = $item;
                                }

                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? ($title['en'] ?? reset($title));
                                }
                                if (is_array($value)) {
                                    $value = $value[app()->getLocale()] ?? ($value['en'] ?? reset($value));
                                }

                                if (filled($title) && filled($value)) {
                                    $specifications[] = ['title' => $title, 'value' => $value];
                                }
                            }
                        }

                        $rawDimensions = $product->dimensions;
                        if (is_string($rawDimensions)) {
                            $decoded = json_decode($rawDimensions, true);
                            $rawDimensions = is_array($decoded) ? $decoded : null;
                        }

                        $dimensions = [];
                        if (is_array($rawDimensions)) {
                            foreach ($rawDimensions as $key => $item) {
                                $title = is_numeric($key) ? 'Dimension ' . ($key + 1) : $key;
                                $value = $item;

                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? ($title['en'] ?? reset($title));
                                }
                                if (is_array($value)) {
                                    $value = $value[app()->getLocale()] ?? ($value['en'] ?? reset($value));
                                }

                                if (filled($title) && filled($value)) {
                                    $dimensions[] = ['title' => $title, 'value' => $value];
                                }
                            }
                        }
                    @endphp

                    <!-- Variants Selection -->
                    @if ($product->productVariants && $product->productVariants->count() > 0)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-base font-bold text-gray-900">Select Variant</label>
                                <span class="text-xs text-gray-500">Click to view details</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($product->productVariants as $variant)
                                    @php
                                        // Use the variant accessor which sums inventory rows.
                                        // Fallback to summing the loaded collection if needed.
                                        $qty =
                                            (int) ($variant->total_stock ??
                                                ($variant->inventory->sum('quantity') ?? 0));
                                        $variantImagePaths = [];
                                        if ($variant->relationLoaded('images')) {
                                            $variantImagePaths = $variant->images
                                                ->map(fn($i) => asset('storage/' . $i->image_path))
                                                ->toArray();
                                        } else {
                                            $imgs = $variant->images()->ordered()->get();
                                            $variantImagePaths = $imgs
                                                ->map(fn($i) => asset('storage/' . $i->image_path))
                                                ->toArray();
                                        }
                                        $variantFirstImage = $variantImagePaths[0] ?? '';
                                        $variantLabel = is_array($variant->variant_name)
                                            ? $variant->variant_name[app()->getLocale()] ??
                                                ($variant->variant_name['en'] ?? reset($variant->variant_name))
                                            : $variant->variant_name ?? ($variant->name ?? '');
                                        $variantAttributesJson = is_array($variant->attributes)
                                            ? json_encode($variant->attributes, JSON_UNESCAPED_UNICODE)
                                            : (is_string($variant->attributes)
                                                ? $variant->attributes
                                                : json_encode([]));

                                        // Get discount info for this variant and normalize to always include price fields
                                        $variantDiscount = isset($variantDiscounts[$variant->id]) ? $variantDiscounts[$variant->id] : null;
                                        $variantDiscountPayload = is_array($variantDiscount) ? $variantDiscount : [];
                                        $variantDiscountPayload['has_discount'] = $variantDiscountPayload['has_discount'] ?? false;
                                        $variantDiscountPayload['original_price'] = $variantDiscountPayload['original_price'] ?? ($variant->price ?? 0);
                                        $variantDiscountPayload['final_price'] = $variantDiscountPayload['final_price'] ?? ($variant->price ?? 0);
                                        $variantDiscountPayload['discount_percentage'] = $variantDiscountPayload['discount_percentage'] ?? 0;
                                        $variantDiscountPayload['discount_name'] = $variantDiscountPayload['discount_name'] ?? null;
                                        $variantHasDiscount = ($variantDiscountPayload['has_discount'] ?? false) && ($variantDiscountPayload['original_price'] ?? 0) > ($variantDiscountPayload['final_price'] ?? 0);
                                    @endphp
                                    <button type="button"
                                        class="variant-button group border-2 border-gray-200 hover:border-primary rounded-xl px-4 py-3 bg-white hover:shadow-md transition-all duration-200 cursor-pointer text-left"
                                        data-variant-id="{{ $variant->id }}" data-slug="{{ $variant->slug }}"
                                        data-image="{{ $variantFirstImage }}" data-images='@json($variantImagePaths)'
                                        data-variant-name="{{ e($variantLabel) }}"
                                        data-attributes='{{ $variantAttributesJson }}'
                                        data-price="{{ is_null($variant->price) ? '' : $variant->price }}"
                                        data-sku="{{ is_array($variant->sku) ? $variant->sku[app()->getLocale()] ?? ($variant->sku['en'] ?? reset($variant->sku)) : $variant->sku ?? '' }}"
                                        data-qty="{{ $qty }}"
                                        data-discount='@json($variantDiscountPayload)'>
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                                {{ is_array($variant->variant_name) ? $variant->variant_name[app()->getLocale()] ?? ($variant->variant_name['en'] ?? reset($variant->variant_name)) : (is_array($variant->name) ? $variant->name[app()->getLocale()] ?? ($variant->name['en'] ?? reset($variant->name)) : $variant->variant_name ?? ($variant->name ?? 'Variant')) }}
                                            </div>
                                            <div class="flex flex-col items-end gap-1">
                                                <span
                                                    class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-medium">
                                                    {{ is_array($variant->sku) ? $variant->sku[app()->getLocale()] ?? ($variant->sku['en'] ?? reset($variant->sku)) : $variant->sku ?? '' }}
                                                </span>
                                                @if ($variant->is_bestseller)
                                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 font-bold">BESTSELLER</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2 line-clamp-1">
                                            {{ is_string($variant->attributes) ? $variant->attributes : (is_array($variant->attributes) ? json_encode($variant->attributes) : '') }}
                                        </div>
                                        <div class="flex items-center justify-between">
                                            @if ($variantHasDiscount)
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-gray-400 line-through">${{ number_format($variantDiscount['original_price'], 2) }}</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-lg font-bold text-red-600">${{ number_format($variantDiscount['final_price'], 2) }}</span>
                                                        @if (($variantDiscount['discount_percentage'] ?? 0) > 0)
                                                            <span class="text-[9px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded">
                                                                -{{ number_format($variantDiscount['discount_percentage'], 0) }}%
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-lg font-bold text-primary">
                                                    ${{ number_format($variant->price, 2) }}
                                                </div>
                                            @endif
                                            @if ($qty > 0)
                                                <span
                                                    class="text-[10px] font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-md stock-badge border border-green-200">
                                                    In Stock
                                                </span>
                                            @else
                                                <span
                                                    class="text-[10px] font-semibold text-red-700 bg-red-50 px-2 py-1 rounded-md stock-badge border border-red-200">
                                                    Out of Stock
                                                </span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add to Cart Form -->
                    <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="cart-variant-id" value="">

                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Quantity Selector -->
                            <div class="flex items-center border-2 border-gray-200 rounded-xl w-fit shadow-sm bg-white">
                                <button type="button" id="qty-decrease"
                                    class="w-12 h-12 text-gray-600 hover:text-primary hover:bg-gray-50 transition-colors rounded-l-xl"
                                    aria-label="Decrease quantity">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="text" name="quantity" id="cart-quantity" value="1"
                                    class="w-14 text-center text-lg font-bold text-gray-900 border-x-2 border-gray-200 focus:outline-none bg-transparent"
                                    readonly aria-label="Quantity">
                                <button type="button" id="qty-increase"
                                    class="w-12 h-12 text-gray-600 hover:text-primary hover:bg-gray-50 transition-colors rounded-r-xl"
                                    aria-label="Increase quantity">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex-1 flex gap-3">
                                <button id="add-to-cart-btn" type="submit"
                                    class="flex-1 bg-linear-to-r from-primary to-primary-dark text-white text-base font-bold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Add to Cart
                                </button>
                                <button type="submit" name="buy_now" value="1" id="buy-now-btn"
                                    class="flex-1 bg-gray-900 text-white text-base font-bold py-3.5 px-6 rounded-xl shadow-lg hover:bg-black hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Buy Now
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Trust Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-gray-200">
                        <div class="flex flex-col items-center text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-900">Secure Payment</span>
                        </div>
                        <div class="flex flex-col items-center text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-900">Free Shipping</span>
                        </div>
                        <div class="flex flex-col items-center text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-900">Easy Returns</span>
                        </div>
                        <div class="flex flex-col items-center text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-900">24/7 Support</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Tabs Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-12 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex overflow-x-auto">
                        <button
                            class="tab-button active px-6 py-4 text-gray-900 font-bold border-b-3 border-primary transition-colors whitespace-nowrap"
                            data-tab="description">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Description
                        </button>
                        <button
                            class="tab-button px-6 py-4 text-gray-600 hover:text-gray-900 font-semibold transition-colors whitespace-nowrap"
                            data-tab="specifications">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Specifications
                        </button>
                        <button
                            class="tab-button px-6 py-4 text-gray-600 hover:text-gray-900 font-semibold transition-colors whitespace-nowrap"
                            data-tab="reviews">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Reviews (247)
                        </button>
                        <button
                            class="tab-button px-6 py-4 text-gray-600 hover:text-gray-900 font-semibold transition-colors whitespace-nowrap"
                            data-tab="shipping">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            Shipping & Returns
                        </button>
                    </nav>
                </div>

                <div class="p-6 lg:p-8">
                    <!-- Description Tab -->
                    <div id="description-tab" class="tab-content active prose-content">
                        <div class="prose prose-lg max-w-none">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">About This Product</h3>
                            <div class="text-gray-700 leading-relaxed">
                                {!! is_array($product->description)
                                    ? $product->description[app()->getLocale()] ?? ($product->description['en'] ?? '')
                                    : $product->description !!}
                            </div>

                            @if ($product->warranty)
                                <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-xl">
                                    <h4 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Warranty Information
                                    </h4>
                                    <div class="text-gray-700">
                                        {!! is_array($product->warranty)
                                            ? $product->warranty[app()->getLocale()] ?? ($product->warranty['en'] ?? '')
                                            : $product->warranty !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div id="specifications-tab" class="tab-content hidden">
                        <div id="specifications-root">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div id="product-specs">
                                    @if (!empty($specifications) || !empty($dimensions))
                                        <div class="space-y-6">
                                            @if (!empty($specifications))
                                                <div
                                                    class="bg-linear-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                                    <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                                        <svg class="w-6 h-6 text-primary mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                        </svg>
                                                        Technical Specifications
                                                    </h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        @foreach ($specifications as $spec)
                                                            <div
                                                                class="bg-white border border-gray-200 rounded-lg p-4 hover:border-primary/50 transition-colors">
                                                                <dt class="text-sm font-semibold text-gray-500 mb-1">
                                                                    {{ $spec['title'] }}</dt>
                                                                <dd class="text-base font-medium text-gray-900">
                                                                    {{ $spec['value'] }}</dd>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($dimensions))
                                                <div
                                                    class="bg-linear-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                                    <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                                        <svg class="w-6 h-6 text-primary mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                        </svg>
                                                        Dimensions & Weight
                                                    </h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        @foreach ($dimensions as $dim)
                                                            <div
                                                                class="bg-white border border-gray-200 rounded-lg p-4 hover:border-primary/50 transition-colors">
                                                                <dt class="text-sm font-semibold text-gray-500 mb-1">
                                                                    {{ $dim['title'] }}</dt>
                                                                <dd class="text-base font-medium text-gray-900">
                                                                    {{ $dim['value'] }}</dd>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-16">
                                            <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-gray-500 text-lg">No specifications available for this product.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Variant specs container - JS will populate this -->
                                <div id="variant-specs"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div id="reviews-tab" class="tab-content hidden">
                        <div class="space-y-8">
                            <div
                                class="bg-linear-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-2xl font-bold text-gray-900">Customer Reviews</h4>
                                    <button
                                        class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg font-semibold transition-colors shadow-sm">
                                        Write a Review
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="text-center">
                                        <div class="text-5xl font-bold text-gray-900 mb-2">4.8</div>
                                        <div class="flex items-center justify-center text-yellow-400 text-2xl mb-2">
                                            ★★★★★
                                        </div>
                                        <div class="text-sm text-gray-600 font-medium">Based on 247 reviews</div>
                                    </div>
                                    <div class="space-y-3">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-semibold text-gray-700 w-12">{{ $i }}
                                                    ★</span>
                                                <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden">
                                                    <div class="bg-yellow-400 h-3 rounded-full transition-all"
                                                        style="width: {{ [75, 20, 3, 1, 1][$i - 1] }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm text-gray-600 font-medium w-12 text-right">{{ [186, 49, 8, 3, 1][$i - 1] }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div
                                        class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-12 h-12 bg-linear-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                                    {{ substr(['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">
                                                        {{ ['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1] }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ ['2 weeks ago', '1 month ago', '3 weeks ago', '1 week ago', '2 days ago'][$i - 1] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center text-yellow-400 text-lg">
                                                ★★★★★
                                            </div>
                                        </div>
                                        <h5 class="font-bold text-gray-900 mb-2 text-lg">
                                            {{ ['Excellent Sound Quality', 'Perfect for Workouts', 'Great Battery Life', 'Comfortable for Long Sessions', 'Worth Every Penny'][$i - 1] }}
                                        </h5>
                                        <p class="text-gray-700 leading-relaxed">
                                            {{ [
                                                'These headphones exceeded my expectations. The sound quality is incredible and the noise cancellation works perfectly for my daily commute.',
                                                'I use these for gym sessions and they stay put perfectly. The battery lasts all day and the sound is crystal clear.',
                                                '30 hours of battery life is no joke! I can go days without charging. The build quality feels premium.',
                                                'Super comfortable even after wearing for 8+ hours. The leather cushions are soft and the fit is perfect.',
                                                'Best headphones I\'ve ever owned. The audio quality, comfort, and features make them worth every cent.',
                                            ][$i - 1] }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-4 text-sm">
                                            <button
                                                class="flex items-center text-gray-600 hover:text-primary transition-colors font-medium">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                Helpful ({{ rand(5, 25) }})
                                            </button>
                                            <button
                                                class="text-gray-500 hover:text-red-500 transition-colors font-medium">Report</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="text-center">
                                <button
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold transition-colors">
                                    Load More Reviews
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Tab -->
                    <div id="shipping-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div
                                class="bg-linear-to-br from-blue-50 to-white border border-blue-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <svg class="w-7 h-7 mr-3 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    Shipping Options
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">Free Standard Shipping</div>
                                            <div class="text-sm text-gray-600">Orders over $50 • 5-7 business days</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">Express Delivery</div>
                                            <div class="text-sm text-gray-600">$9.99 • 1-2 business days</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">Same Day Delivery</div>
                                            <div class="text-sm text-gray-600">$14.99 • Select areas only</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-linear-to-br from-green-50 to-white border border-green-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <svg class="w-7 h-7 mr-3 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                                    </svg>
                                    Returns & Exchanges
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">30-Day Returns</div>
                                            <div class="text-sm text-gray-600">Full refund on eligible items</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">Free Exchanges</div>
                                            <div class="text-sm text-gray-600">Different size or color at no cost</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900 mb-1">Easy Process</div>
                                            <div class="text-sm text-gray-600">Print label or drop off anywhere</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if ($relatedProducts && $relatedProducts->count() > 0)
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">You Might Also Like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($relatedProducts as $relatedProduct)
                            <div
                                class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-200">
                                <a href="{{ route('products.detail', $relatedProduct->slug) }}"
                                    class="block relative aspect-square bg-gray-100 overflow-hidden">
                                    <img src="{{ $relatedProduct->cover_image ? asset('storage/' . $relatedProduct->cover_image) : asset('images/placeholder-product.jpg') }}"
                                        alt="{{ is_array($relatedProduct->name) ? $relatedProduct->name[app()->getLocale()] ?? ($relatedProduct->name['en'] ?? '') : $relatedProduct->name }}"
                                        class="w-full h-full object-contain p-4 transition-transform group-hover:scale-110 duration-300">
                                    @if ($relatedProduct->created_at > now()->subDays(30))
                                        <div class="absolute top-3 left-3">
                                            <span
                                                class="bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">
                                                NEW
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-4">
                                    <a href="{{ route('products.detail', $relatedProduct->slug) }}">
                                        <h3
                                            class="font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary mb-2 min-h-12">
                                            {{ is_array($relatedProduct->name) ? $relatedProduct->name[app()->getLocale()] ?? ($relatedProduct->name['en'] ?? '') : $relatedProduct->name }}
                                        </h3>
                                    </a>

                                    <div class="flex items-center mb-3">
                                        <div class="flex items-center text-yellow-400">
                                            ★★★★★
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">(0)</span>
                                    </div>

                                    @php
                                        $relatedDiscount = isset($relatedProduct->discountInfo) ? $relatedProduct->discountInfo : null;
                                    @endphp

                                    <div class="flex items-center justify-between">
                                        @if ($relatedDiscount && $relatedDiscount['has_discount'])
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-400 line-through">${{ number_format($relatedDiscount['original_price'], 2) }}</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xl font-bold text-red-600">${{ number_format($relatedDiscount['final_price'], 2) }}</span>
                                                    @if ($relatedDiscount['discount_percentage'] > 0)
                                                        <span class="text-[10px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded">
                                                            -{{ number_format($relatedDiscount['discount_percentage'], 0) }}%
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xl font-bold text-primary">${{ number_format($relatedProduct->price, 2) }}</span>
                                        @endif
                                        <button
                                            class="bg-primary hover:bg-primary-dark text-white p-2 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-detail.js') }}"></script>
@endpush
