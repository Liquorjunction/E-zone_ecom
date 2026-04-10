@extends('client.layouts.app')


@section('content')
    <script>
        window.CURRENCY_SYMBOL = @json(config('app.CURRENCY_SYMBOL'));
        window.CHECKOUT_URL = @json(route('checkout', ['step' => 2]));
        window.PRODUCT_ID = @json($product->id);
        window.PRODUCT_VARIANT_ID = null;
        window.IS_AUTHENTICATED = @json(auth('web')->check());
        window.APP_LOCALE = @json(app()->getLocale());
        window.TRANSLATIONS = @json(json_decode(file_get_contents(base_path('lang/' . app()->getLocale() . '.json')), true));
    </script>

    {{-- ─── Page wrapper ────────────────────────────────────────────────── --}}
    <div class="min-h-screen bg-gray-50/70 py-8 pb-16">
        <div class="container mx-auto px-4 max-w-7xl">

            {{-- Breadcrumbs --}}
            <div class="mb-6">
                {!! Breadcrumbs::render('products.detail', $product, $breadcrumbContext ?? null) !!}
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 MAIN GRID — image gallery + product info
            ═══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-14 mb-12">

                {{-- ────────────────────────────────── LEFT: Image Gallery --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden sticky top-4">

                        {{-- Main image container --}}
                        <div class="aspect-square relative group cursor-zoom-in select-none">
                            <img id="main-product-image"
                                src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                alt="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }}"
                                class="w-full h-full object-contain p-1 rounded-3xl transition-opacity duration-200" loading="lazy"
                                decoding="async">

                            {{-- Badges top-left --}}
                            @php
                                $currentVariant = null;
                                if (request()->has('variant')) {
                                    $currentVariant = $product->productVariants
                                        ->where('slug', request()->get('variant'))
                                        ->first();
                                }
                                if (
                                    !$currentVariant &&
                                    $product->productVariants &&
                                    $product->productVariants->count() > 0
                                ) {
                                    $currentVariant = $product->productVariants->first();
                                }
                            @endphp
                            <div class="absolute top-4 left-4 flex flex-col gap-2 z-10 pointer-events-none">
                                @if ($product->created_at > now()->subDays(30))
                                    <span
                                        class="bg-emerald-500 text-white text-[11px] font-bold tracking-wider uppercase px-3 py-1.5 rounded-lg shadow-lg">
                                        {{ statictranslations('New') }}
                                    </span>
                                @endif
                                @if ($currentVariant && $currentVariant->is_bestseller)
                                    <span
                                        class="bg-amber-500 text-white text-[11px] font-bold tracking-wider uppercase px-3 py-1.5 rounded-lg shadow-lg">
                                        {{ statictranslations('Bestseller') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Wishlist button top-right --}}
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
                                class="wishlist-btn absolute top-4 right-4 w-10 h-10 rounded-xl bg-white/90 backdrop-blur-sm shadow-md border border-gray-100 flex items-center justify-center hover:bg-red-50 hover:border-red-200 transition-all z-10 group/heart {{ $isWishlisted ? 'is-wishlisted' : '' }}"
                                @if (!$variantId) disabled @endif>
                                <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500 fill-red-500' : 'text-gray-500' }} group-hover/heart:text-red-500 transition-colors"
                                    fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>

                            {{-- "Click to zoom" hint --}}
                            <div
                                class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-sm text-white text-[11px] font-medium px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                                Zoom
                            </div>
                        </div>

                        {{-- Thumbnail row --}}
                        <div class="px-4 pb-4 pt-3 border-t border-gray-100 bg-gray-50/60">
                            <div class="flex gap-2 overflow-x-auto pb-1 snap-x snap-mandatory scrollbar-thin">
                                @if ($product->images && $product->images->count() > 0)
                                    @foreach ($product->images->take(6) as $image)
                                        <button type="button" data-image="{{ asset('storage/' . $image->image_path) }}"
                                            class="thumb-button shrink-0 snap-start w-16 h-16 rounded-xl bg-white border-2 border-gray-200 overflow-hidden hover:border-primary transition-all focus:outline-none focus:ring-2 focus:ring-primary/30">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                alt="{{ is_array($product->name) ? $product->name[app()->getLocale()] ?? ($product->name['en'] ?? '') : $product->name }} - {{ $loop->iteration }}"
                                                class="w-full h-full object-cover" loading="lazy" decoding="async">
                                        </button>
                                    @endforeach
                                @else
                                    <button type="button"
                                        class="thumb-button shrink-0 w-16 h-16 rounded-xl bg-white border-2 border-primary overflow-hidden ring-2 ring-primary/30"
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

                {{-- ────────────────────────────────── RIGHT: Product Info --}}
                <div class="flex flex-col gap-6">

                    {{-- ── Header: tags, title, rating, stock ── --}}
                    <div class="space-y-4">
                        {{-- Pill tags --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1">
                                {{ statictranslations('Electronics') }}
                            </span>
                            <span id="product-sku"
                                class="inline-flex items-center gap-1 rounded-lg bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1">
                                SKU:
                                {{ is_array($product->sku) ? $product->sku[app()->getLocale()] ?? ($product->sku['en'] ?? '') : $product->sku ?? '' }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h1 id="product-title"
                            data-base-name="{{ is_array($product->name) ? strip_tags(html_entity_decode($product->name[app()->getLocale()] ?? ($product->name['en'] ?? ''), ENT_QUOTES, 'UTF-8')) : strip_tags(html_entity_decode($product->name, ENT_QUOTES, 'UTF-8')) }}"
                            class="text-2xl lg:text-3xl xl:text-4xl font-extrabold text-gray-900 leading-tight tracking-tight">
                            {!! is_array($product->name)
                                ? strip_tags(
                                    html_entity_decode($product->name[app()->getLocale()] ?? ($product->name['en'] ?? ''), ENT_QUOTES, 'UTF-8'),
                                )
                                : strip_tags(html_entity_decode($product->name, ENT_QUOTES, 'UTF-8')) !!}
                        </h1>

                        {{-- Rating & stock row --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <div
                                class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5">
                                <div id="productHeaderStars" class="flex items-center text-amber-400 text-sm leading-none">
                                    ☆☆☆☆☆
                                </div>
                                <span id="productHeaderReviewCount" class="text-gray-600 text-xs font-medium">(0
                                    {{ statictranslations('reviews') }})</span>
                            </div>

                            @php
                                $totalStock = 0;
                                if ($product->productVariants && $product->productVariants->count() > 0) {
                                    $totalStock = $product->productVariants->sum(function ($v) {
                                        return method_exists($v, 'getSellableStock')
                                            ? $v->getSellableStock()
                                            : 0;
                                    });
                                } else {
                                    $totalStock = 0;
                                }
                                $isInStock = $totalStock > 0;
                            @endphp
                            @if ($isInStock)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    {{ statictranslations('In Stock') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ statictranslations('Out of Stock') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ── Price box ── --}}
                    @php
                        $variantCount = $product->productVariants ? $product->productVariants->count() : 0;
                        if (!isset($discountInfo) || !is_array($discountInfo)) {
                            $discountInfo = $product->getDiscountInfo();
                        }
                        if (!isset($variantDiscounts)) {
                            $variantDiscounts = [];
                        }
                        $hasDiscount =
                            isset($discountInfo['has_discount']) &&
                            $discountInfo['has_discount'] === true &&
                            ($discountInfo['original_price'] ?? 0) > ($discountInfo['final_price'] ?? 0);
                        $originalPrice = $discountInfo['original_price'] ?? 0;
                        $finalPrice = $discountInfo['final_price'] ?? 0;
                        $discountPercentage = $hasDiscount ? $discountInfo['discount_percentage'] ?? 0 : 0;
                        $discountName = $discountInfo['discount_name'] ?? '';
                    @endphp
                    <div id="price-box"
                        class="bg-linear-to-r from-primary/5 via-primary/10 to-primary/5 border-2 border-primary/20 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-start justify-between flex-wrap gap-4">
                            <div class="flex items-baseline gap-3" id="price-flex-row">
                                <div class="flex flex-col gap-1.5" id="js-price-wrapper">
                                    @if ($variantCount > 1)
                                        @if ($hasDiscount)
                                            <div class="text-xl text-gray-400 line-through">
                                                {{ config('app.CURRENCY_SYMBOL') }} {{ number_format($originalPrice, 2) }}
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-xs text-gray-500 font-medium">{{ statictranslations('From') }}</span>
                                                <span id="product-price"
                                                    class="text-4xl font-extrabold text-red-600 tracking-tight">{{ config('app.CURRENCY_SYMBOL') }}
                                                    {{ number_format($finalPrice, 2) }}</span>
                                            </div>
                                            @if ($discountPercentage > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500 text-white text-xs font-bold self-start">
                                                    {{ statictranslations('Save') }} {{ number_format($discountPercentage, 0) }}%
                                                </span>
                                            @endif
                                        @else
                                            @php $minPrice = $product->productVariants->min('price'); @endphp
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-xs text-gray-500 font-medium">{{ statictranslations('From') }}</span>
                                                <span id="product-price"
                                                    class="text-4xl font-extrabold text-primary tracking-tight">{{ config('app.CURRENCY_SYMBOL') }}
                                                    {{ number_format($minPrice, 2) }}</span>
                                            </div>
                                        @endif
                                    @else
                                        @if ($hasDiscount)
                                            <div class="text-xl text-gray-400 line-through">
                                                {{ config('app.CURRENCY_SYMBOL') }} {{ number_format($originalPrice, 2) }}
                                            </div>
                                            <span id="product-price"
                                                class="text-4xl font-extrabold text-red-600 tracking-tight">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($finalPrice, 2) }}</span>
                                            @if ($discountPercentage > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500 text-white text-xs font-bold self-start">
                                                    {{ statictranslations('Save') }} {{ number_format($discountPercentage, 0) }}%
                                                </span>
                                            @endif
                                        @else
                                            <span id="product-price"
                                                class="text-4xl font-extrabold text-primary tracking-tight">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($finalPrice > 0 ? $finalPrice : $product->price, 2) }}</span>
                                        @endif
                                    @endif
                                    <span class="text-xs text-gray-400 font-medium">{{ statictranslations('incl. taxes') }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($hasDiscount && !empty($discountName))
                            <div
                                class="discount-applied-badge mt-3 flex items-center gap-2 px-3 py-2.5 bg-emerald-50 rounded-xl border border-emerald-200">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-semibold text-emerald-800">{{ $discountName }} {{ statictranslations('applied') }}!</span>
                            </div>
                        @endif
                    </div>

                    {{-- ── Category & Brand ── --}}
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm border-t border-gray-100 pt-4">
                        @if ($product->category)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="text-gray-500">{{ statictranslations('Category') }}:</span>
                                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                                    class="text-primary hover:text-primary-dark font-semibold transition-colors hover:underline">
                                    {{ method_exists($product->category ?? null, 'getTranslation') ? $product->category->getTranslation('category-name', app()->getLocale()) : (is_array(optional($product->category)->{'category-name'}) ? optional($product->category)->{'category-name'}[app()->getLocale()] ?? (optional($product->category)->{'category-name'}['en'] ?? '') : optional($product->category)->{'category-name'}) }}
                                </a>
                            </div>
                        @endif
                        @if ($product->brand)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-gray-500">{{ statictranslations('Brand') }}:</span>
                                <a href="{{ route('brand.products', $product->brand->slug) }}"
                                    class="text-primary hover:text-primary-dark font-semibold transition-colors hover:underline">
                                    {{ method_exists($product->brand ?? null, 'getTranslation') ? $product->brand->getTranslation('name', app()->getLocale()) : (is_array(optional($product->brand)->name) ? optional($product->brand)->name[app()->getLocale()] ?? (optional($product->brand)->name['en'] ?? '') : optional($product->brand)->name) }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- ── Short description ── --}}
                    <div class="prose prose-sm max-w-none prose-content text-gray-600 leading-relaxed line-clamp-3">
                        {!! is_array($product->description)
                            ? $product->description[app()->getLocale()] ?? ($product->description['en'] ?? '')
                            : $product->description !!}
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

                    {{-- ── Variant selection ── --}}
                    @if ($product->productVariants && $product->productVariants->count() > 0)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold text-gray-800 uppercase tracking-wide">{{ statictranslations('Select Variant') }}</label>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($product->productVariants as $variant)
                                    @php
                                        $qty = (int) (method_exists($variant, 'getSellableStock') ? $variant->getSellableStock() : 0);
                                        $variantImagePaths = [];
                                        if ($variant->relationLoaded('images')) {
                                            $variantImagePaths = $variant->images
                                                ->map(fn($i) => asset('storage/' . $i->image_path))
                                                ->toArray();
                                        } else {
                                            $variantImagePaths = $variant
                                                ->images()
                                                ->ordered()
                                                ->get()
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
                                        $variantDiscount = isset($variantDiscounts[$variant->id])
                                            ? $variantDiscounts[$variant->id]
                                            : null;
                                        $variantDiscountPayload = is_array($variantDiscount) ? $variantDiscount : [];
                                        $variantDiscountPayload['has_discount'] =
                                            $variantDiscountPayload['has_discount'] ?? false;
                                        $variantDiscountPayload['original_price'] =
                                            $variantDiscountPayload['original_price'] ?? ($variant->price ?? 0);
                                        $variantDiscountPayload['final_price'] =
                                            $variantDiscountPayload['final_price'] ?? ($variant->price ?? 0);
                                        $variantDiscountPayload['discount_percentage'] =
                                            $variantDiscountPayload['discount_percentage'] ?? 0;
                                        $variantDiscountPayload['discount_name'] =
                                            $variantDiscountPayload['discount_name'] ?? null;
                                        $variantHasDiscount =
                                            ($variantDiscountPayload['has_discount'] ?? false) &&
                                            ($variantDiscountPayload['original_price'] ?? 0) >
                                                ($variantDiscountPayload['final_price'] ?? 0);
                                        $skuValue = is_array($variant->sku)
                                            ? $variant->sku[app()->getLocale()] ??
                                                ($variant->sku['en'] ?? reset($variant->sku))
                                            : $variant->sku ?? '';
                                        $displaySku =
                                            strlen($skuValue) > 14
                                                ? substr($skuValue, 0, 6) . '...' . substr($skuValue, -4)
                                                : $skuValue;
                                    @endphp
                                    <button type="button"
                                        class="variant-button group border-2 border-gray-200 hover:border-primary rounded-2xl px-4 py-3 bg-white hover:shadow-md transition-all duration-200 cursor-pointer text-left relative overflow-hidden"
                                        data-variant-id="{{ $variant->id }}" data-slug="{{ $variant->slug }}"
                                        data-image="{{ $variantFirstImage }}" data-images='@json($variantImagePaths)'
                                        data-variant-name="{{ e($variantLabel) }}"
                                        data-attributes='{{ $variantAttributesJson }}'
                                        data-price="{{ is_null($variant->price) ? '' : $variant->price }}"
                                        data-sku="{{ $skuValue }}" data-qty="{{ $qty }}"
                                        data-discount='@json($variantDiscountPayload)'>

                                        {{-- Out of stock overlay --}}
                                        @if ($qty <= 0)
                                            <div class="absolute inset-0 bg-white/60 rounded-2xl pointer-events-none">
                                            </div>
                                        @endif

                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                                    {{ $variantLabel }}
                                                </div>
                                                @if ($variant->is_bestseller)
                                                    <span
                                                        class="text-[9px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-bold border border-amber-200 shrink-0">BEST</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center justify-between gap-2">
                                                @if ($variantHasDiscount)
                                                    <div class="flex flex-col">
                                                        <div class="text-xs text-gray-400 line-through">
                                                            {{ config('app.CURRENCY_SYMBOL') }}
                                                            {{ number_format($variantDiscountPayload['original_price'], 2) }}
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span
                                                                class="text-base font-extrabold text-red-600">{{ config('app.CURRENCY_SYMBOL') }}
                                                                {{ number_format($variantDiscountPayload['final_price'], 2) }}</span>
                                                            @if (($variantDiscountPayload['discount_percentage'] ?? 0) > 0)
                                                                <span
                                                                    class="text-[9px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded">-{{ number_format($variantDiscountPayload['discount_percentage'], 0) }}%</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-base font-extrabold text-primary">
                                                        {{ config('app.CURRENCY_SYMBOL') }}
                                                        {{ number_format($variant->price, 2) }}
                                                    </div>
                                                @endif
                                                @if ($qty > 0)
                                                    <span
                                                        class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg stock-badge border border-emerald-200 whitespace-nowrap shrink-0">{{ statictranslations('In Stock') }}</span>
                                                @else
                                                    <span
                                                        class="text-[10px] font-semibold text-red-700 bg-red-50 px-2 py-1 rounded-lg stock-badge border border-red-200 whitespace-nowrap shrink-0">{{ statictranslations('Out of Stock') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Add to Cart form ── --}}
                    @php
                        $totalStock = 0;
                        if ($product->productVariants && $product->productVariants->count() > 0) {
                            $totalStock = $product->productVariants->sum(function ($v) {
                                return method_exists($v, 'getSellableStock')
                                    ? $v->getSellableStock()
                                    : 0;
                            });
                        } else {
                            $totalStock = 0;
                        }
                    @endphp
                    @if ($totalStock > 0)
                        <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}"
                            data-add-url="{{ route('cart.add') }}"
                            data-buy-now-url="{{ route('cart.buyNow') }}"
                            data-item-status-url="{{ route('cart.itemStatus') }}"
                            class="space-y-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="cart-variant-id" value="">

                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Quantity selector --}}
                                <div
                                    class="flex items-center border-2 border-gray-200 hover:border-primary/40 rounded-xl w-fit bg-white transition-colors shadow-sm">
                                    <button type="button" id="qty-decrease"
                                        class="w-11 h-11 text-gray-500 hover:text-primary hover:bg-gray-50 transition-colors rounded-l-xl flex items-center justify-center"
                                        aria-label="Decrease quantity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" name="quantity" id="cart-quantity" value="1"
                                        class="w-12 text-center text-base font-bold text-gray-900 border-x-2 border-gray-200 focus:outline-none bg-transparent"
                                        min="1" @if ($totalStock > 0) max="{{ $totalStock }}" @endif aria-label="Quantity">
                                    <button type="button" id="qty-increase"
                                        class="w-11 h-11 text-gray-500 hover:text-primary hover:bg-gray-50 transition-colors rounded-r-xl flex items-center justify-center"
                                        aria-label="Increase quantity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Action buttons --}}
                                <div class="flex-1 flex gap-3">
                                    <button id="add-to-cart-btn" type="submit" name="add_to_cart" value="1"
                                        class="flex-1 bg-linear-to-r from-primary to-primary-dark text-white text-sm font-bold py-3 px-5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2 active:translate-y-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span id="add-to-cart-btn-label">{{ statictranslations('Add to Cart') }}</span>
                                    </button>
                                    <button type="submit" name="buy_now" value="1" id="buy-now-btn"
                                        formaction="{{ route('cart.buyNow') }}"
                                        class="flex-1 bg-gray-900 text-white text-sm font-bold py-3 px-5 rounded-xl shadow-md hover:bg-black hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2 active:translate-y-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        {{ statictranslations('Buy Now') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    {{-- ── Trust badges ── --}}
                    {{-- <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                        <div
                            class="flex flex-col items-center text-center gap-1 p-3 rounded-xl bg-white border border-gray-100 hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-gray-700 leading-tight">Secure Payment</span>
                        </div>
                        <div
                            class="flex flex-col items-center text-center gap-1 p-3 rounded-xl bg-white border border-gray-100 hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-gray-700 leading-tight">Free Shipping</span>
                        </div>
                        <div
                            class="flex flex-col items-center text-center gap-1 p-3 rounded-xl bg-white border border-gray-100 hover:border-primary/30 hover:shadow-sm transition-all">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-semibold text-gray-700 leading-tight">Easy Returns</span>
                        </div>
                    </div> --}}

                </div>{{-- /right col --}}
            </div>{{-- /main grid --}}

            {{-- ═══════════════════════════════════════════════════════════════
                 TABS SECTION
            ═══════════════════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-3xl shadow-md border border-gray-100 mb-12 overflow-hidden">

                {{-- Tab nav --}}
                <div class="border-b border-gray-100 bg-gray-50/60">
                    <nav class="flex overflow-x-auto scrollbar-none px-2 pt-2 gap-1">
                        <button
                            class="tab-button active px-5 py-3 text-gray-900 font-bold border-b-[3px] border-primary bg-white rounded-t-xl transition-all whitespace-nowrap text-sm flex items-center gap-2"
                            data-tab="description">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            {{ statictranslations('Description') }}
                        </button>
                        <button
                            class="tab-button px-5 py-3 text-gray-500 hover:text-gray-900 font-semibold transition-all whitespace-nowrap text-sm flex items-center gap-2 rounded-t-xl hover:bg-white/70"
                            data-tab="specifications">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ statictranslations('Specifications') }}
                        </button>
                        <button
                            class="tab-button px-5 py-3 text-gray-500 hover:text-gray-900 font-semibold transition-all whitespace-nowrap text-sm flex items-center gap-2 rounded-t-xl hover:bg-white/70"
                            data-tab="reviews">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            {{ statictranslations('Reviews') }} (<span id="reviewsTabCount">0</span>)
                        </button>
                        {{-- <button
                            class="tab-button px-5 py-3 text-gray-500 hover:text-gray-900 font-semibold transition-all whitespace-nowrap text-sm flex items-center gap-2 rounded-t-xl hover:bg-white/70"
                            data-tab="shipping">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            {{ statictranslations('Shipping & Returns') }}
                        </button> --}}
                    </nav>
                </div>

                <div class="p-6 lg:p-8">

                    {{-- ── Description tab ── --}}
                    <div id="description-tab" class="tab-content active prose-content">
                        <div class="prose prose-lg max-w-none">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ statictranslations('About This Product') }}</h3>
                            <div class="text-gray-700 leading-relaxed">
                                {!! is_array($product->description)
                                    ? $product->description[app()->getLocale()] ?? ($product->description['en'] ?? '')
                                    : $product->description !!}
                            </div>
                            @php
                                $hasWarranty = !empty($product->warranty);
                                $hasDimensions = !empty($product->dimensions);
                                $count = ($hasWarranty ? 1 : 0) + ($hasDimensions ? 1 : 0);
                            @endphp
                            @if ($hasWarranty || $hasDimensions)
                                <div class="grid grid-cols-1 {{ $count > 1 ? 'md:grid-cols-2 gap-6' : '' }} mt-8">
                                    @if ($hasWarranty)
                                        <div class="p-5 bg-blue-50 border border-blue-200 rounded-2xl col-span-1">
                                            <h4 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                {{ statictranslations('Warranty Information') }}
                                            </h4>
                                            <div class="text-gray-700 text-sm">
                                                {!! is_array($product->warranty)
                                                    ? $product->warranty[app()->getLocale()] ?? ($product->warranty['en'] ?? '')
                                                    : $product->warranty !!}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($hasDimensions)
                                        <div class="p-5 bg-gray-50/60 border border-gray-200 rounded-2xl col-span-1">
                                            <h4 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                </svg>
                                                {{ statictranslations('Product Dimensions') }}
                                            </h4>
                                            <div class="text-gray-700 text-sm">
                                                {!! is_array($product->dimensions)
                                                    ? $product->dimensions[app()->getLocale()] ?? ($product->dimensions['en'] ?? '')
                                                    : $product->dimensions !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── Specifications tab ── --}}
                    <div id="specifications-tab" class="tab-content hidden">
                        <div id="specifications-root">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div id="product-specs">
                                    @if (!empty($specifications) || !empty($dimensions))
                                        <div class="space-y-6">
                                            @if (!empty($specifications))
                                                <div
                                                    class="bg-gray-50/60 border border-gray-200 rounded-2xl p-5 shadow-sm">
                                                    <h4
                                                        class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-primary" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                        </svg>
                                                        {{ statictranslations('Technical Specifications') }}
                                                    </h4>
                                                    <div class="divide-y divide-gray-100">
                                                        @foreach ($specifications as $spec)
                                                            <div
                                                                class="flex items-start gap-3 py-3 hover:bg-white rounded-lg px-2 -mx-2 transition-colors">
                                                                <dt
                                                                    class="text-sm text-gray-500 font-medium w-2/5 shrink-0 pt-0.5">
                                                                    {{ $spec['title'] }}</dt>
                                                                <dd class="text-sm font-semibold text-gray-900">
                                                                    {{ $spec['value'] }}</dd>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if (!empty($dimensions))
                                                <div
                                                    class="bg-gray-50/60 border border-gray-200 rounded-2xl p-5 shadow-sm">
                                                    <h4
                                                        class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-primary" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                        </svg>
                                                        {{ statictranslations('Dimensions & Weight') }}
                                                    </h4>
                                                    <div class="divide-y divide-gray-100">
                                                        @foreach ($dimensions as $dim)
                                                            <div
                                                                class="flex items-start gap-3 py-3 hover:bg-white rounded-lg px-2 -mx-2 transition-colors">
                                                                <dt
                                                                    class="text-sm text-gray-500 font-medium w-2/5 shrink-0 pt-0.5">
                                                                    {{ $dim['title'] }}</dt>
                                                                <dd class="text-sm font-semibold text-gray-900">
                                                                    {{ $dim['value'] }}</dd>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-16">
                                            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-gray-400 font-medium">{{ statictranslations('No specifications available.') }}</p>
                                        </div>
                                    @endif
                                </div>
                                {{-- Variant specs (JS populated) --}}
                                <div id="variant-specs"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Reviews tab ── --}}
                    <div id="reviews-tab" class="tab-content hidden">
                        <div class="space-y-8">
                            <div class="bg-gray-50/60 border border-gray-200 rounded-2xl p-6 shadow-sm">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-xl font-bold text-gray-900">{{ statictranslations('Customer Reviews') }}</h4>
                                    @auth('web')
                                        {{-- Write review button can go here --}}
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm inline-flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{ statictranslations('Login to Review') }}
                                        </a>
                                    @endauth
                                </div>
                                <div id="reviewStats" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="text-center">
                                        <div id="avgRating"
                                            class="text-6xl font-extrabold text-gray-900 mb-1 tracking-tight">-</div>
                                        <div id="avgRatingStars"
                                            class="flex items-center justify-center text-amber-400 text-2xl mb-2">☆☆☆☆☆
                                        </div>
                                        <div id="reviewCount" class="text-sm text-gray-500 font-medium">{{ statictranslations('Loading reviews...') }}
                                        </div>
                                    </div>
                                    <div id="ratingBreakdown" class="space-y-2.5"></div>
                                </div>
                            </div>

                            <div id="reviewsList" class="space-y-4">
                                <div class="text-center py-8">
                                    <div
                                        class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto mb-3">
                                    </div>
                                    <p class="text-gray-400 text-sm">{{ statictranslations('Loading reviews...') }}</p>
                                </div>
                            </div>

                            <div id="loadMoreContainer" class="text-center hidden">
                                <button id="loadMoreReviews"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl text-sm font-semibold transition-colors">
                                    {{ statictranslations('Load More Reviews') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ── Shipping & Returns tab ── --}}
                    <div id="shipping-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Shipping options --}}
                            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5">
                                <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    {{ statictranslations('Shipping Options') }}
                                </h4>
                                <div class="space-y-3">
                                    @foreach ([['icon' => 'M5 13l4 4L19 7', 'color' => 'green', 'title' => 'Free Standard Shipping', 'desc' => 'Orders over $50 · 5–7 business days'], ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue', 'title' => 'Express Delivery', 'desc' => '$9.99 · 1–2 business days'], ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple', 'title' => 'Same Day Delivery', 'desc' => '$14.99 · Select areas only']] as $opt)
                                        <div
                                            class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                            <div
                                                class="w-9 h-9 bg-{{ $opt['color'] }}-100 rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-{{ $opt['color'] }}-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $opt['icon'] }}" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $opt['title'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $opt['desc'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Returns --}}
                            <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-5">
                                <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                                    </svg>
                                    {{ statictranslations('Returns & Exchanges') }}
                                </h4>
                                <div class="space-y-3">
                                    @foreach ([['color' => 'orange', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => '30-Day Returns', 'desc' => 'Full refund on eligible items'], ['color' => 'indigo', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Free Exchanges', 'desc' => 'Different size or color at no cost'], ['color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Easy Process', 'desc' => 'Print label or drop off anywhere']] as $opt)
                                        <div
                                            class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                                            <div
                                                class="w-9 h-9 bg-{{ $opt['color'] }}-100 rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-{{ $opt['color'] }}-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $opt['icon'] }}" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $opt['title'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $opt['desc'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                </div>{{-- /tab panels --}}
            </div>{{-- /tabs card --}}

            {{-- ═══════════════════════════════════════════════════════════════
                 RELATED PRODUCTS
            ═══════════════════════════════════════════════════════════════ --}}
            @if ($relatedProducts && $relatedProducts->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ statictranslations('You Might Also Like') }}</h2>
                        <div class="h-px flex-1 mx-6 bg-gray-200 hidden sm:block"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($relatedProducts as $relatedProduct)
                            @include('client.partials.product-card', [
                                'product' => $relatedProduct,
                                'wishlistVariantIds' => $wishlistVariantIds ?? collect(),
                            ])
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-image-gallery.js') }}"></script>
    <script src="{{ asset('js/product-detail.js') }}"></script>
    <script src="{{ asset('js/product-reviews.js') }}"></script>
@endpush
