@extends('client.layouts.app')

@section('content')
    @php
        $locale = app()->getLocale() ?? 'en';
        $compareVariants = $compareVariants ?? collect();
        $features = $features ?? ['Brand', 'Category', 'SKU'];
        $maxCompare = 4;

        $products = $compareVariants->map(function ($variant) use ($locale) {
            $product = $variant->product;

            $productName = $product?->getTranslation('name', $locale)
                ?? $product?->name
                ?? $variant->getTranslation('variant_name', $locale)
                ?? $variant->variant_name
                ?? 'Product';

            if (is_array($productName)) {
                $productName = implode(' ', array_filter($productName));
            }

            $productLink = $product && $product->slug
                ? route('products.detail', $product->slug) . ($variant->slug ? ('?variant=' . $variant->slug) : '')
                : '#';

            $image = $product?->cover_image
                ? asset('storage/' . $product->cover_image)
                : asset('icon/product-placeholder-A.jpg');

            $discount = method_exists($variant, 'getDiscountInfo')
                ? $variant->getDiscountInfo()
                : ['final_price' => $variant->price ?? 0];

            $reviews = $variant->reviews ?? collect();
            $approved = $reviews->where('is_approved', 'approved');
            $reviewCount = $approved->count();
            $averageRating = $reviewCount > 0 ? round($approved->avg('rating'), 1) : 0;

            $specs = [
                'Brand' => (string) ($product?->brand?->name ?? '—'),
                'Category' => (function () use ($product, $locale) {
                    $category = $product?->category;

                    if (!$category) {
                        return '—';
                    }

                    if (method_exists($category, 'getTranslation') && method_exists($category, 'getTranslatableAttributes')) {
                        $translatable = $category->getTranslatableAttributes();

                        if (in_array('category-name', $translatable, true)) {
                            $translated = $category->getTranslation('category-name', $locale);
                            if (is_string($translated) && trim($translated) !== '') {
                                return $translated;
                            }
                        }
                    }

                    $raw = $category->getAttribute('category-name') ?? $category->getAttribute('name');

                    if (is_array($raw)) {
                        $picked = $raw[$locale] ?? ($raw['en'] ?? reset($raw));
                        return is_string($picked) && trim($picked) !== '' ? $picked : '—';
                    }

                    return is_string($raw) && trim($raw) !== '' ? $raw : '—';
                })(),
                'SKU' => (string) ($variant->sku ?? '—'),
            ];

            $normalize = function ($source) {
                $locale = app()->getLocale() ?? 'en';

                if (is_string($source)) {
                    $decoded = json_decode($source, true);
                    $source = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                }

                if (!is_array($source)) {
                    return [];
                }

                $isLocaleBucket = function (array $arr) use ($locale) {
                    $keys = array_keys($arr);
                    $knownLocales = ['en', 'fr', 'ar', $locale];

                    return !empty($keys)
                        && count(array_filter($keys, fn ($key) => is_string($key) && in_array($key, $knownLocales, true))) >= 1
                        && count(array_filter($keys, fn ($key) => is_string($key) && !in_array($key, $knownLocales, true))) === 0;
                };

                $normalizeDisplay = function ($value) use ($locale, $isLocaleBucket) {
                    if (is_array($value)) {
                        if ($isLocaleBucket($value)) {
                            $picked = $value[$locale] ?? ($value['en'] ?? reset($value));

                            if (is_array($picked)) {
                                return implode(', ', array_filter(array_map('strval', $picked)));
                            }

                            return trim((string) $picked);
                        }

                        return implode(', ', array_filter(array_map('strval', $value)));
                    }

                    if (is_bool($value)) {
                        return $value ? 'Yes' : 'No';
                    }

                    if (is_null($value)) {
                        return '—';
                    }

                    return trim((string) $value);
                };

                if ($isLocaleBucket($source)) {
                    $source = $source[$locale] ?? ($source['en'] ?? reset($source));

                    if (!is_array($source)) {
                        return [];
                    }
                }

                $result = [];
                foreach ($source as $key => $value) {
                    if (is_array($value) && (isset($value['title']) || isset($value['label']) || isset($value['value']) || isset($value['description']))) {
                        $title = $value['title'] ?? ($value['label'] ?? (is_string($key) ? $key : null));
                        $displayValue = $value['value'] ?? ($value['description'] ?? null);

                        $title = $normalizeDisplay($title);
                        $displayValue = $normalizeDisplay($displayValue);

                        if ($title !== '' && $displayValue !== '') {
                            $result[$title] = $displayValue;
                        }

                        continue;
                    }

                    if (!is_string($key) || trim($key) === '') {
                        continue;
                    }

                    $displayValue = $normalizeDisplay($value);
                    $result[$key] = $displayValue !== '' ? $displayValue : '—';
                }

                return $result;
            };

            $localizedAttribute = function ($model, $attribute) use ($locale) {
                if (!$model) {
                    return null;
                }

                if (method_exists($model, 'getTranslation') && method_exists($model, 'getTranslatableAttributes')) {
                    $translatable = $model->getTranslatableAttributes();

                    if (in_array($attribute, $translatable, true)) {
                        return $model->getTranslation($attribute, $locale);
                    }
                }

                return $model->{$attribute} ?? null;
            };

            $sources = [
                $localizedAttribute($variant, 'attributes'),
                $localizedAttribute($variant, 'specifications'),
                $localizedAttribute($product, 'attributes'),
                $localizedAttribute($product, 'specifications'),
            ];

            foreach ($sources as $source) {
                foreach ($normalize($source) as $key => $value) {
                    $specs[$key] = $value;
                }
            }

            return [
                'id' => $variant->id,
                'product_id' => $product?->id,
                'name' => (string) $productName,
                'link' => $productLink,
                'image' => $image,
                'price' => (float) ($discount['final_price'] ?? $variant->price ?? 0),
                'rating' => $averageRating,
                'reviews' => $reviewCount,
                'specs' => $specs,
            ];
        })->values();
    @endphp

    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Compare Products') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('Compare products side by side.') }}</x-slot:content>
            </x-breadcrumbs>

            @if ($products->count() > 0)

                {{-- ===================== DESKTOP VIEW (lg and above) ===================== --}}
                <div class="hidden lg:block bg-white rounded-2xl shadow-sm border-2 border-gray-100 overflow-x-auto">
                    <div class="min-w-max lg:min-w-full">
                        <table class="w-full text-sm text-left">

                            <thead>
                                <tr class="border-b-2 border-gray-100">

                                    <th class="sticky left-0 bg-white p-6 min-w-50 z-10">
                                        <h3 class="text-xl font-bold text-gray-900">{{ statictranslations('Features') }}</h3>
                                        <p class="text-gray-500 text-sm">{{ statictranslations('Products') }} ({{ $products->count() }} of {{ $maxCompare }})</p>
                                    </th>

                                    @foreach ($products as $product)
                                        <th class="p-4 w-1/4 min-w-75 text-center border-l-2 border-gray-100">
                                            <div class="flex justify-end mb-2">
                                                <form action="{{ route('compare.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_variant_id" value="{{ $product['id'] }}">
                                                    <button class="text-gray-400 hover:text-red-500 transition-colors" type="submit" aria-label="Remove from compare">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <a href="{{ $product['link'] }}"
                                                class="block relative aspect-square bg-gray-100 rounded-xl overflow-hidden group mb-4">
                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                                    class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                                            </a>
                                            <a href="{{ $product['link'] }}"
                                                class="font-semibold text-gray-900 transition-colors hover:text-primary mb-2 line-clamp-2">
                                                {{ $product['name'] }}
                                            </a>
                                            <div class="flex items-center justify-center text-yellow-400 text-sm my-2">
                                                {{ str_repeat('★', (int) floor($product['rating'])) . str_repeat('☆', max(0, 5 - (int) floor($product['rating']))) }}
                                                <span class="ml-2 text-sm text-gray-500">({{ $product['reviews'] }})</span>
                                            </div>
                                            <p class="text-xl font-bold text-primary mb-3">{{ money_format_number($product['price']) }}</p>
                                            <button
                                                class="add-to-cart-btn w-full px-4 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300"
                                                data-product-variant-id="{{ $product['id'] }}"
                                                data-product-id="{{ $product['product_id'] }}"
                                                onclick="addToCart('{{ $product['id'] }}', '{{ $product['product_id'] }}')">
                                                {{ statictranslations('Add to Cart') }}
                                            </button>
                                        </th>
                                    @endforeach

                                    @if ($products->count() < $maxCompare)
                                        <th class="p-4 w-1/4 min-w-75 text-center border-l-2 border-gray-100">
                                            <div class="flex items-center justify-center h-full">
                                                <a href="{{ route('products.index') }}"
                                                    class="flex flex-col items-center justify-center w-full h-full aspect-square border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
                                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <span class="font-semibold mt-2">{{ statictranslations('Add Product') }}</span>
                                                </a>
                                            </div>
                                        </th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($features as $feature)
                                    <tr class="even:bg-gray-50">
                                        <td class="sticky left-0 bg-white even:bg-gray-50 p-4 font-semibold text-gray-700 border-b border-gray-100 z-10">
                                            {{ $feature }}
                                        </td>

                                        @foreach ($products as $product)
                                            <td class="p-4 text-gray-900 border-b border-l-2 border-gray-100 text-center">
                                                {{ $product['specs'][$feature] ?? '—' }}
                                            </td>
                                        @endforeach

                                        @if ($products->count() < $maxCompare)
                                            <td class="p-4 text-gray-400 border-b border-l-2 border-gray-100 text-center">
                                                —
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
                {{-- ===================== END DESKTOP VIEW ===================== --}}

                {{-- ===================== MOBILE VIEW (below lg) ===================== --}}
                <div class="block lg:hidden space-y-4">

                    {{-- Horizontal scrollable product cards --}}
                    <div class="overflow-x-auto pb-2 -mx-1 px-1">
                        <div class="flex gap-3 min-w-max">

                            @foreach ($products as $product)
                                <div class="bg-white rounded-2xl border-2 border-gray-100 p-3 w-36 flex flex-col items-center gap-2 relative shadow-sm">

                                    {{-- Remove button --}}
                                    <form action="{{ route('compare.remove') }}" method="POST" class="absolute top-2 right-2">
                                        @csrf
                                        <input type="hidden" name="product_variant_id" value="{{ $product['id'] }}">
                                        <button type="submit"
                                            class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100 transition-colors"
                                            aria-label="{{ statictranslations('Remove from compare') }}">
                                            <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Product image --}}
                                    <a href="{{ $product['link'] }}"
                                        class="block w-20 h-20 rounded-xl overflow-hidden bg-gray-100 group shrink-0">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                                    </a>

                                    {{-- Name --}}
                                    <a href="{{ $product['link'] }}"
                                        class="text-xs font-semibold text-gray-900 text-center leading-tight line-clamp-2 hover:text-primary transition-colors w-full">
                                        {{ $product['name'] }}
                                    </a>

                                    {{-- Stars --}}
                                    <div class="text-yellow-400 text-xs leading-none">
                                        {{ str_repeat('★', (int) floor($product['rating'])) . str_repeat('☆', max(0, 5 - (int) floor($product['rating']))) }}
                                        <span class="text-gray-400">({{ $product['reviews'] }})</span>
                                    </div>

                                    {{-- Price --}}
                                    <p class="text-sm font-bold text-primary">
                                        {{ money_format_number($product['price']) }}
                                    </p>

                                    <button
                                        class="add-to-cart-btn w-full flex items-center justify-center gap-2 px-3 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:shadow-md transform hover:-translate-y-0.5 transition-all"
                                        data-product-variant-id="{{ $product['id'] }}"
                                        data-product-id="{{ $product['product_id'] }}"
                                        onclick="addToCart('{{ $product['id'] }}', '{{ $product['product_id'] }}')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        {{ statictranslations('Add to Cart') }}
                                    </button>

                                </div>
                            @endforeach

                            {{-- Add product slot --}}
                            @if ($products->count() < $maxCompare)
                                <a href="{{ route('products.index') }}"
                                    class="flex flex-col items-center justify-center w-36 rounded-2xl border-2 border-dashed border-gray-200 text-gray-400 hover:border-primary hover:text-primary transition-colors gap-2 shrink-0 py-6">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span class="text-xs font-semibold">{{ statictranslations('Add Product') }}</span>
                                </a>
                            @endif

                        </div>
                    </div>

                    {{-- Feature comparison rows --}}
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1">
                            {{ statictranslations('Specifications') }}
                        </p>

                        @foreach ($features as $feature)
                            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">

                                {{-- Feature label --}}
                                <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                                    <span class="text-xs font-semibold text-gray-500">{{ $feature }}</span>
                                </div>

                                {{-- Values per product --}}
                                <div class="flex divide-x divide-gray-100">
                                    @foreach ($products as $product)
                                        <div class="flex-1 px-2 py-2.5 text-center min-w-0">
                                            <p class="text-xs text-gray-400 truncate mb-0.5">
                                                {{ Str::limit($product['name'], 12) }}
                                            </p>
                                            <p class="text-xs font-semibold text-gray-900 wrap-break-word leading-tight">
                                                {{ $product['specs'][$feature] ?? '—' }}
                                            </p>
                                        </div>
                                    @endforeach

                                    @if ($products->count() < $maxCompare)
                                        <div class="flex-1 px-2 py-2.5 text-center">
                                            <p class="text-xs text-gray-300">—</p>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
                {{-- ===================== END MOBILE VIEW ===================== --}}

            @else
                {{-- Empty state — shared for both breakpoints --}}
                <div class="flex items-center justify-center py-20">
                    <div class="text-center max-w-md">
                        <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ statictranslations('Nothing to Compare') }}</h2>
                        <p class="text-gray-600 mb-8">{{ statictranslations("You haven't added any products to compare. Start browsing and add products to see a side-by-side comparison.") }}</p>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            {{ statictranslations('Start Shopping') }}
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
