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
                'Category' => (string) ($product?->category?->name ?? '—'),
                'SKU' => (string) ($variant->sku ?? '—'),
            ];

            $attributes = $variant->getTranslation('attributes', $locale) ?? $variant->attributes;
            $productSpecs = $product?->getTranslation('specifications', $locale) ?? $product?->specifications;

            foreach ([$attributes, $productSpecs] as $source) {
                if (!is_array($source)) {
                    continue;
                }

                foreach ($source as $key => $value) {
                    if (!is_string($key) || trim($key) === '') {
                        continue;
                    }

                    if (is_array($value)) {
                        $value = implode(', ', array_filter(array_map('strval', $value)));
                    }

                    $specs[$key] = (string) ($value ?: '—');
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
                <x-slot:title>Compare Products</x-slot:title>
                <x-slot:content>Compare products side by side.</x-slot:content>
            </x-breadcrumbs>

            @if ($products->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 overflow-x-auto">
                    <div class="min-w-max lg:min-w-full">
                        <table class="w-full text-sm text-left">

                            <thead>
                                <tr class="border-b-2 border-gray-100">

                                    <th class="sticky left-0 bg-white p-6 min-w-[200px] z-10">
                                        <h3 class="text-xl font-bold text-gray-900">Features</h3>
                                        <p class="text-gray-500 text-sm">Products ({{ $products->count() }} of {{ $maxCompare }})</p>
                                    </th>

                                    @foreach ($products as $product)
                                        <th class="p-4 w-1/4 min-w-[300px] text-center border-l-2 border-gray-100">
                                            <div class="flex justify-end mb-2">
                                                <form action="{{ route('compare.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_variant_id" value="{{ $product['id'] }}">
                                                    <button class="text-gray-400 hover:text-red-500 transition-colors" type="submit" aria-label="Remove from compare">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                                {{ str_repeat('★', (int) floor($product['rating'])) . str_repeat('☆', max(0, 5 - (int) floor($product['rating']))) }} <span
                                                    class="ml-2 text-sm text-gray-500">({{ $product['reviews'] }})</span>
                                            </div>
                                            <p class="text-xl font-bold text-primary mb-3">{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($product['price'], 2) }}</p>
                                            <button
                                                class="add-to-cart-btn w-full px-4 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300"
                                                data-product-variant-id="{{ $product['id'] }}"
                                                data-product-id="{{ $product['product_id'] }}"
                                                onclick="addToCart('{{ $product['id'] }}', '{{ $product['product_id'] }}')">
                                                Add to Cart
                                            </button>
                                        </th>
                                    @endforeach

                                    @for ($i = $products->count(); $i < $maxCompare; $i++)
                                        <th class="p-4 w-1/4 min-w-[300px] text-center border-l-2 border-gray-100">
                                            <div class="flex items-center justify-center h-full">
                                                <a href="{{ route('products.index') }}"
                                                    class="flex flex-col items-center justify-center w-full h-full aspect-square border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
                                                    <svg class="w-16 h-16" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <span class="font-semibold mt-2">Add Product</span>
                                                </a>
                                            </div>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($features as $feature)
                                    <tr class="even:bg-gray-50">
                                        <td
                                            class="sticky left-0 bg-white even:bg-gray-50 p-4 font-semibold text-gray-700 border-b border-gray-100 z-10">
                                            {{ $feature }}
                                        </td>

                                        @foreach ($products as $product)
                                            <td class="p-4 text-gray-900 border-b border-l-2 border-gray-100 text-center">
                                                {{ $product['specs'][$feature] ?? '—' }}
                                            </td>
                                        @endforeach

                                        @for ($i = $products->count(); $i < $maxCompare; $i++)
                                            <td class="p-4 text-gray-400 border-b border-l-2 border-gray-100 text-center">
                                                —
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center py-20">
                    <div class="text-center max-w-md">
                        <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Nothing to Compare</h2>
                        <p class="text-gray-600 mb-8">You haven't added any products to compare. Start browsing and add
                            products to see a side-by-side comparison.</p>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
