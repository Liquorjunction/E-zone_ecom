@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            {!! Breadcrumbs::render('products.detail', $product) !!}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 overflow-hidden">
                        <div class="aspect-square relative">
                            <img id="main-product-image" src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($product->created_at > now()->subDays(30))
                                    <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                        NEW
                                    </span>
                                @endif
                            </div>
                            <button
                                class="absolute top-4 right-4 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-red-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-600 hover:text-red-500 transition-colors" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        @if($product->images && $product->images->count() > 0)
                            @foreach($product->images->take(6) as $image)
                                <button type="button" data-image="{{ asset('storage/' . $image->image_path) }}"
                                    class="thumb-button bg-white rounded-xl shadow-sm border-2 border-gray-100 overflow-hidden hover:border-primary transition-colors focus:border-primary focus:ring-2 focus:ring-primary/50">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }} - {{ $loop->iteration }}" class="w-full h-24 object-cover" loading="lazy" decoding="async">
                                </button>
                            @endforeach
                        @else
                            <button type="button" class="thumb-button bg-white rounded-xl shadow-sm border-2 border-gray-100 overflow-hidden hover:border-primary transition-colors focus:border-primary focus:ring-2 focus:ring-primary/50" data-image="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}">
                                <img src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                    alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}" class="w-full h-24 object-cover" loading="lazy" decoding="async">
                            </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-6 lg:sticky lg:top-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($product->is_bestseller)
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.034a1 1 0 00-1.176 0l-2.802 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.88 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Bestseller
                                </span>
                            @endif
                            @if($product->created_at > now()->subDays(30))
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold px-3 py-1">New Arrival</span>
                            @endif
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1">Electronics</span>
                            <span id="product-sku" class="inline-flex items-center gap-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1">SKU: {{ is_array($product->sku) ? ($product->sku[app()->getLocale()] ?? ($product->sku['en'] ?? '')) : ($product->sku ?? '') }}</span>
                        </div>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-1">
                            {{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <div class="flex items-center text-yellow-400 text-lg">
                                ★★★★★
                            </div>
                            <span class="text-gray-600 text-sm">(0 reviews)</span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                In Stock
                            </span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">Free returns 30d</span>
                        </div>
                    </div>

                    <div class="flex items-center flex-wrap gap-3 p-4 bg-gradient-to-r from-primary/10 via-white to-white border border-primary/20 rounded-2xl shadow-sm">
                        <div class="flex items-baseline gap-2">
                            @php
                                $variantCount = $product->productVariants ? $product->productVariants->count() : 0;
                                $minPrice = $variantCount ? $product->productVariants->min('price') : $product->price;
                                $maxPrice = $variantCount ? $product->productVariants->max('price') : $product->price;
                            @endphp

                            @if($variantCount > 1 && $minPrice != $maxPrice)
                                <span id="product-price" class="text-4xl font-bold text-primary">${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}</span>
                            @else
                                <span id="product-price" class="text-4xl font-bold text-primary">${{ number_format($minPrice, 2) }}</span>
                            @endif

                            <span class="text-sm text-gray-500">incl. taxes</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white text-primary text-sm font-semibold shadow-xs border border-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M4 7h16l-1 12H5L4 7zm5 3v6m6-6v6"/></svg>
                            Fast dispatch
                        </span>
                    </div>

                    {{-- Categories and Tags Chips --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Category:</span>
                        @if($product->category)
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ method_exists($product->category ?? null, 'getTranslation') ? $product->category->getTranslation('category-name', app()->getLocale()) : (is_array(optional($product->category)->{'category-name'}) ? (optional($product->category)->{'category-name'}[app()->getLocale()] ?? optional($product->category)->{'category-name'}['en'] ?? '') : optional($product->category)->{'category-name'}) }}
                            </a>
                        @endif
                        @if($product->subCategory)
                            <a href="{{ route('products.index', ['subcategory' => $product->subCategory->slug]) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ method_exists($product->subCategory ?? null, 'getTranslation') ? $product->subCategory->getTranslation('sub-category-name', app()->getLocale()) : (is_array(optional($product->subCategory)->{'sub-category-name'}) ? (optional($product->subCategory)->{'sub-category-name'}[app()->getLocale()] ?? optional($product->subCategory)->{'sub-category-name'}['en'] ?? '') : optional($product->subCategory)->{'sub-category-name'}) }}
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Brand:</span>
                        @if($product->brand)
                                <a href="{{ route('brand-products', $product->brand->slug) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ method_exists($product->brand ?? null, 'getTranslation') ? $product->brand->getTranslation('name', app()->getLocale()) : (is_array(optional($product->brand)->name) ? (optional($product->brand)->name[app()->getLocale()] ?? optional($product->brand)->name['en'] ?? '') : optional($product->brand)->name) }}
                            </a>
                        @endif
                    </div>

                    {{-- Information About Product and more infos --}}
                    <div class="space-y-2">
                        <h2 class="text-lg font-bold text-gray-700 mb-2">Description:</h2>
                        <div class="text-gray-600 prose max-w-none">
                            {!! is_array($product->description) ? ($product->description[app()->getLocale()] ?? $product->description['en'] ?? '') : $product->description !!}
                        </div>
                    </div>

                    {{-- Product Features --}}
                    @php
                        // Normalize specifications and dimensions: they may be JSON strings or arrays with varying shapes.
                        $rawSpecs = $product->specifications;
                        if (is_string($rawSpecs)) {
                            $decoded = json_decode($rawSpecs, true);
                            $rawSpecs = is_array($decoded) ? $decoded : null;
                        }

                        $specifications = [];
                        if (is_array($rawSpecs)) {
                            foreach ($rawSpecs as $key => $item) {
                                // Common importer shape: [{title: ..., value: ...}]
                                if (is_array($item) && (isset($item['title']) || isset($item['value']) || isset($item['description']))) {
                                    $title = $item['title'] ?? $item['label'] ?? $key;
                                    $value = $item['value'] ?? $item['description'] ?? '';
                                } else {
                                    // Fallback shape: associative map key => value
                                    $title = is_numeric($key) ? 'Spec '.($key + 1) : $key;
                                    $value = $item;
                                }

                                // Resolve translatable arrays
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
                                $title = is_numeric($key) ? 'Dimension '.($key + 1) : $key;
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

                    {{-- Variants --}}
                    @if($product->productVariants && $product->productVariants->count() > 0)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-semibold text-gray-800">Variants</label>
                                <span class="text-xs text-gray-500">Tap to view details</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($product->productVariants as $variant)
                                    @php
                                        $qty = optional($variant->inventory)->quantity ?? 0;
                                    @endphp
                                    <button type="button" class="variant-button group border border-gray-200 hover:border-primary/50 rounded-2xl px-4 py-3 bg-white shadow-sm hover:shadow-md transition-all duration-150 cursor-pointer" data-variant-id="{{ $variant->id }}" data-price="{{ is_null($variant->price) ? '' : $variant->price }}" data-sku="{{ is_array($variant->sku) ? ($variant->sku[app()->getLocale()] ?? ($variant->sku['en'] ?? reset($variant->sku))) : ($variant->sku ?? '') }}" data-qty="{{ $qty }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="text-sm font-semibold text-gray-900 line-clamp-1">{{ is_array($variant->variant_name) ? ($variant->variant_name[app()->getLocale()] ?? ($variant->variant_name['en'] ?? reset($variant->variant_name))) : (is_array($variant->name) ? ($variant->name[app()->getLocale()] ?? ($variant->name['en'] ?? reset($variant->name))) : ($variant->variant_name ?? $variant->name ?? 'Variant')) }}</div>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ is_array($variant->sku) ? ($variant->sku[app()->getLocale()] ?? ($variant->sku['en'] ?? reset($variant->sku))) : ($variant->sku ?? '') }}</span>
                                            </div>
                                            <div class="text-xs text-gray-600 mt-0.5 line-clamp-1">{{ is_string($variant->attributes) ? $variant->attributes : (is_array($variant->attributes) ? json_encode($variant->attributes) : '') }}</div>
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="text-base font-bold text-primary">${{ number_format($variant->price, 2) }}</div>
                                            @if($qty > 0)
                                                <span class="text-[11px] font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full stock-badge">In stock</span>
                                            @else
                                                <span class="text-[11px] font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full stock-badge">Out of stock</span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center border border-gray-200 rounded-xl w-fit shadow-sm bg-white">
                            <button class="w-12 h-12 text-gray-600 hover:text-primary transition-colors" aria-label="Decrease quantity">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="text" value="1"
                                class="w-12 text-center text-lg font-semibold text-gray-900 border-x border-gray-200 focus:outline-none"
                                readonly aria-label="Quantity">
                            <button class="w-12 h-12 text-gray-600 hover:text-primary transition-colors" aria-label="Increase quantity">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <button
                            class="flex-1 w-full bg-gradient-to-r from-primary to-primary-dark text-white text-lg font-semibold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                            Add to Cart
                        </button>
                        <button
                            class="flex-1 w-full bg-gray-900 text-white text-lg font-semibold py-3.5 px-6 rounded-xl shadow-lg hover:bg-black transition-all duration-300 transform hover:-translate-y-0.5">
                            Buy Now
                        </button>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 mb-12">
                <div class="border-b border-gray-100">
                    <nav class="flex">
                        <button
                            class="tab-button active px-8 py-4 text-gray-700 font-semibold border-b-2 border-primary transition-colors"
                            data-tab="description">
                            Description
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="specifications">
                            Specifications
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="reviews">
                            Reviews (247)
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="shipping">
                            Shipping & Returns
                        </button>
                    </nav>
                </div>

                <div class="p-8">
                    <div id="description-tab" class="tab-content active">
                        <div class="prose max-w-none">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}</h3>
                            <div class="text-gray-700 mb-6 leading-relaxed">
                                {!! is_array($product->description) ? ($product->description[app()->getLocale()] ?? $product->description['en'] ?? '') : $product->description !!}
                            </div>

                            @if($product->warranty)
                                <div class="mt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Warranty:</h4>
                                    <div class="text-gray-700">
                                        {!! is_array($product->warranty) ? ($product->warranty[app()->getLocale()] ?? $product->warranty['en'] ?? '') : $product->warranty !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="specifications-tab" class="tab-content hidden">
                        @if(!empty($specifications) || !empty($dimensions))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                @if(!empty($specifications))
                                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Specifications</h4>
                                        <table class="w-full text-sm">
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($specifications as $spec)
                                                    <tr class="py-2">
                                                        <td class="py-3 text-gray-500 font-medium w-1/3">{{ $spec['title'] }}</td>
                                                        <td class="py-3 text-gray-900">{{ $spec['value'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                @if(!empty($dimensions))
                                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Dimensions</h4>
                                        <table class="w-full text-sm">
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($dimensions as $dim)
                                                    <tr class="py-2">
                                                        <td class="py-3 text-gray-500 font-medium w-1/3">{{ $dim['title'] }}</td>
                                                        <td class="py-3 text-gray-900">{{ $dim['value'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                No specifications available for this product.
                            </div>
                        @endif
                    </div>

                    <div id="reviews-tab" class="tab-content hidden">
                        <div class="space-y-8">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-semibold text-gray-900">Customer Reviews</h4>
                                    <button
                                        class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-dark transition-colors">
                                        Write a Review
                                    </button>
                                </div>
                                <div class="flex items-center space-x-8">
                                    <div class="text-center">
                                        <div class="text-4xl font-bold text-gray-900">4.8</div>
                                        <div class="flex items-center justify-center text-yellow-400 text-lg mb-1">
                                            ★★★★★
                                        </div>
                                        <div class="text-sm text-gray-600">247 reviews</div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">5★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 75%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">186</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">4★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 20%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">49</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">3★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 3%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">8</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">2★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">3</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">1★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ substr(['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">
                                                        {{ ['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1] }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ ['2 weeks ago', '1 month ago', '3 weeks ago', '1 week ago', '2 days ago'][$i - 1] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center text-yellow-400">
                                                ★★★★★
                                            </div>
                                        </div>
                                        <h5 class="font-semibold text-gray-900 mb-2">
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
                                        <div class="flex items-center space-x-4 mt-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                Helpful ({{ rand(5, 25) }})
                                            </span>
                                            <button class="text-primary hover:underline">Report</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="text-center">
                                <button
                                    class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                    Load More Reviews
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="shipping-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Shipping Information
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Free Shipping</div>
                                            <div class="text-sm text-gray-600">Orders over $50 qualify for free standard
                                                shipping</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Express Delivery</div>
                                            <div class="text-sm text-gray-600">1-2 business days - $9.99</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Same Day Delivery</div>
                                            <div class="text-sm text-gray-600">Available in select areas - $14.99</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                                    </svg>
                                    Returns & Exchanges
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">30-Day Returns</div>
                                            <div class="text-sm text-gray-600">Return any item within 30 days for a full
                                                refund</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Free Exchanges</div>
                                            <div class="text-sm text-gray-600">Exchange for a different size or color at no
                                                cost</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Easy Process</div>
                                            <div class="text-sm text-gray-600">Print return label or drop off at any
                                                location</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts && $relatedProducts->count() > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">You Might Also Like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-gray-100">
                                <a href="{{ route('products.detail', $relatedProduct->slug) }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
                                    <img src="{{ $relatedProduct->cover_image ? asset('storage/' . $relatedProduct->cover_image) : asset('images/placeholder-product.jpg') }}"
                                        alt="{{ is_array($relatedProduct->name) ? ($relatedProduct->name[app()->getLocale()] ?? $relatedProduct->name['en'] ?? '') : $relatedProduct->name }}"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                                    @if($relatedProduct->created_at > now()->subDays(30))
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                NEW
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-4">
                                    <a href="{{ route('products.detail', $relatedProduct->slug) }}">
                                        <h3 class="font-semibold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary mb-2">
                                            {{ is_array($relatedProduct->name) ? ($relatedProduct->name[app()->getLocale()] ?? $relatedProduct->name['en'] ?? '') : $relatedProduct->name }}
                                        </h3>
                                    </a>

                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center text-yellow-400 text-sm">
                                            ★★★★☆
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">(0)</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-lg font-bold text-primary">${{ number_format($relatedProduct->price, 2) }}</span>
                                        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.dataset.tab;

                    // Deactivate all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-primary', 'text-gray-700');
                        btn.classList.add('text-gray-500');
                    });

                    // Activate clicked button
                    button.classList.add('active', 'border-primary', 'text-gray-700');
                    button.classList.remove('text-gray-500');

                    // Hide all content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    });

                    // Show target content
                    const targetContent = document.getElementById(targetTab + '-tab');
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                        targetContent.classList.add('active');
                    }
                });
            });

            // Thumbnail click -> swap main image
            const thumbButtons = document.querySelectorAll('.thumb-button');
            const mainImage = document.getElementById('main-product-image');
            thumbButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const src = btn.getAttribute('data-image');
                    if (src && mainImage) {
                        mainImage.src = src;
                    }
                });
            });

            // Variant selection handling
            const variantButtons = document.querySelectorAll('.variant-button');
            const priceEl = document.getElementById('product-price');
            const skuEl = document.getElementById('product-sku');
            const qtyInput = document.querySelector('input[aria-label="Quantity"]');
            let addToCartBtn = null;
            document.querySelectorAll('button').forEach(b => {
                if (!addToCartBtn && b.textContent && b.textContent.trim().toLowerCase().includes('add to cart')) {
                    addToCartBtn = b;
                }
            });
            if (!addToCartBtn) {
                addToCartBtn = document.querySelector('button:nth-of-type(1)');
            }

            function setActiveVariant(btn) {
                variantButtons.forEach(v => v.classList.remove('ring-2', 'ring-primary/30'));
                btn.classList.add('ring-2', 'ring-primary/30');

                const price = btn.getAttribute('data-price');
                const sku = btn.getAttribute('data-sku');
                const qty = parseInt(btn.getAttribute('data-qty') || '0', 10);

                if (priceEl && price) {
                    priceEl.textContent = '$' + parseFloat(price).toFixed(2);
                }
                if (skuEl && sku) {
                    skuEl.textContent = 'SKU: ' + sku;
                }

                // Update stock / quantity and Add to Cart state
                if (qtyInput) {
                    if (qty > 0) {
                        qtyInput.removeAttribute('disabled');
                        qtyInput.value = '1';
                    } else {
                        qtyInput.value = '0';
                        qtyInput.setAttribute('disabled', 'disabled');
                    }
                }
                if (addToCartBtn) {
                    if (qty > 0) {
                        addToCartBtn.removeAttribute('disabled');
                        addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        addToCartBtn.setAttribute('disabled', 'disabled');
                        addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }

            if (variantButtons.length > 0) {
                // Attach click handlers
                variantButtons.forEach(btn => {
                    btn.addEventListener('click', () => setActiveVariant(btn));
                });

                // Activate first variant by default
                setActiveVariant(variantButtons[0]);
            }
        });
    </script>
@endpush
