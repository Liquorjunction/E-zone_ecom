@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <!-- Page Header -->
            <x-breadcrumbs>
                <x-slot:title>Our Products</x-slot:title>
                <x-slot:content>Discover the latest in electronics and technology</x-slot:content>
            </x-breadcrumbs>

            <!-- Search and Sort Bar -->
            <x-search-sort-bar />

            <!-- Main Content -->
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Filters Sidebar -->
                <aside class="lg:w-80 hidden lg:block">
                    <x-filter :brands="$brands" :categories="$categories" :subCategories="$subCategories" />
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">

                    <!-- Active Filters -->
                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary">
                            Apple
                            <button class="ml-2 hover:text-primary/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary">
                            In Stock
                            <button class="ml-2 hover:text-primary/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <!-- Results Count -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}-{{ $products->lastItem() }}</span> of
                            <span class="font-semibold text-gray-900">{{ $products->total() }}</span> results</p>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @foreach ($products as $product)
                            <div
                                class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-gray-100">
                                <a href="{{ route('products.detail', $product->slug) }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
                                    <img src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                        alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : (string) $product->name }}"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">

                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                                        @if ($product->created_at->diffInDays(now()) < 30)
                                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                                NEW
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Wishlist Button -->
                                    <button
                                        class="absolute top-3 right-3 w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-red-50">
                                        <svg class="w-5 h-5 text-gray-600 hover:text-red-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>

                                    <!-- Quick View -->
                                    <div
                                        class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/50 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button
                                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors">
                                            Quick View
                                        </button>
                                    </div>
                                </a>

                                <div class="p-5">
                                    <!-- Category Badge -->
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">
                                        {{ $product->category ? (is_array($product->category->{'category-name'}) ? ($product->category->{'category-name'}[app()->getLocale()] ?? $product->category->{'category-name'}['en'] ?? '') : (string) $product->category->{'category-name'}) : 'Uncategorized' }}
                                    </span>

                                    <a href="{{ route('products.detail', $product->slug) }}">
                                        <h3
                                            class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                            {{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : (string) $product->name }}
                                        </h3>
                                    </a>

                                    <!-- Rating -->
                                    <div class="flex items-center mt-2">
                                        <div class="flex items-center text-yellow-400 text-sm">
                                            ★★★★★
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">(0)</span>
                                    </div>

                                    <!-- Features -->
                                    <ul class="mt-3 space-y-1 text-xs text-gray-600">
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Free Shipping
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ is_array($product->warranty) ? ($product->warranty[app()->getLocale()] ?? $product->warranty['en'] ?? '') : ($product->warranty ?? '1 Year Warranty') }}
                                        </li>
                                    </ul>

                                    <!-- Price and Add to Cart -->
                                    <div class="mt-4 flex items-center justify-between">
                                        <div>
                                            <p class="text-2xl font-bold text-primary">${{ number_format($product->price, 2) }}</p>
                                        </div>
                                        <button
                                            class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 group-hover:bg-primary">
                                            <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Stock Status -->
                                    <div class="mt-3 text-xs text-green-600 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        In Stock
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                        {{ $products->links() }}
                    </div>

                </div>
            </div>

            <!-- Mobile Filter Button -->
            <button id="mobile-filter-btn"
                class="lg:hidden fixed bottom-6 right-6 z-50 px-6 py-4 bg-primary text-white rounded-full shadow-2xl transition-all duration-300 hover:shadow-3xl transform hover:scale-110 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-semibold">Filters</span>
            </button>

            <!-- Mobile Filter Modal -->
            <div id="mobile-filter-modal" class="lg:hidden fixed inset-0 z-50 hidden bg-black bg-opacity-50">
                <div class="flex items-end justify-center min-h-screen">
                    <div class="bg-white w-full max-h-[90vh] rounded-t-3xl overflow-hidden transform transition-transform duration-300 translate-y-full" id="modal-content">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Filters</h2>
                            <button id="close-modal" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                            <x-filter :brands="$brands" :categories="$categories" :subCategories="$subCategories" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/products.js') }}"></script>
    @endpush
@endsection
