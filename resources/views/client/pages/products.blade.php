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
                    <x-filter :brands="$brands" :categories="$categories" :subCategories="$subCategories" :availabilityCounts="$availabilityCounts ?? []" :minPrice="$minPrice ?? 0" :maxPrice="$maxPrice ?? 5000" />
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">

                    <!-- Active Filters -->
                    @php
                        $subcategoryLookup = [];
                        foreach ($categories as $cat) {
                            if (!is_object($cat) || !$cat->subcategories) continue;
                            foreach ($cat->subcategories as $sub) {
                                if (is_object($sub) && isset($sub->slug)) {
                                    $subcategoryLookup[$sub->slug] = $sub->{'sub-category-name'} ?? $sub->slug;
                                }
                            }
                        }
                    @endphp
                    <div id="active-filters-container" class="mb-6 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                        @if(request('brand'))
                            @php $brands = (array) request('brand'); @endphp
                            @foreach($brands as $brand)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-brand="{{ $brand }}">
                                    <span class="capitalize">{{ str_replace('-', ' ', $brand) }}</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="brand" data-filter-value="{{ $brand }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('category'))
                            @php $cats = (array) request('category'); @endphp
                            @foreach($cats as $c)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-category="{{ $c }}">
                                    <span class="capitalize">{{ str_replace('-', ' ', $c) }}</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="category" data-filter-value="{{ $c }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('subcategory'))
                            @php $subs = (array) request('subcategory'); @endphp
                            @foreach($subs as $sub)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-subcategory="{{ $sub }}">
                                    <span>Subcategory: {{ $subcategoryLookup[$sub] ?? str_replace('-', ' ', $sub) }}</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="subcategory" data-filter-value="{{ $sub }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('availability'))
                            @php $avail = (array) request('availability'); @endphp
                            @foreach($avail as $a)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-availability="{{ $a }}">
                                    <span class="capitalize">{{ str_replace('_', ' ', $a) }}</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="availability" data-filter-value="{{ $a }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('rating'))
                            @php $ratings = (array) request('rating'); @endphp
                            @foreach($ratings as $rating)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-rating="{{ $rating }}">
                                    <span>{{ $rating }} Star{{ $rating != '1' ? 's' : '' }} and up</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="rating" data-filter-value="{{ $rating }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('discount'))
                            @php $discounts = (array) request('discount'); @endphp
                            @foreach($discounts as $disc)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-discount="{{ $disc }}">
                                    <span>{{ $disc }}% off or more</span>
                                    <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="discount" data-filter-value="{{ $disc }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-price="true">
                                <span>${{ request('min_price', 0) }} - ${{ request('max_price', 5000) }}</span>
                                <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="price">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </span>
                        @endif
                    </div>

                    <!-- Results Count -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}-{{ $products->lastItem() }}</span> of
                            <span class="font-semibold text-gray-900">{{ $products->total() }}</span> variants</p>
                    </div>

                    <!-- Products Grid + Spinner -->
                    <div class="relative" data-ajax-pagination data-results-target="#products-results" data-pagination-target="#products-pagination" data-loading-target="#filter-loading">
                        <div id="filter-loading" class="absolute inset-0 bg-white/70 z-50 flex items-center justify-center">
                            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>

                        <div id="products-results">
                            @if($products->isEmpty())
                                <div class="flex flex-col items-center justify-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">No products found</h3>
                                    <p class="text-gray-500 mt-1">Try adjusting your search or filters to find what you're looking for.</p>
                                    <a href="{{ route('products.index') }}" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                        Clear Filters
                                    </a>
                                </div>
                            @else
                                @if(request('view') === 'list')
                                    <div class="space-y-4">
                                        @foreach($products as $product)
                                            @include('client.partials.product-card-list', ['product' => $product])
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Mobile: 2-col grid with mobile card --}}
                                    <div class="grid grid-cols-2 gap-3 sm:hidden">
                                        @foreach($products as $product)
                                            @include('client.partials.product-card-mobile', ['product' => $product])
                                        @endforeach
                                    </div>
                                    {{-- Tablet+: standard grid with desktop card --}}
                                    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($products as $product)
                                            @include('client.partials.product-card', ['product' => $product])
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-10" id="products-pagination">
                                    {{ $products->withQueryString()->links('client.components.pagination') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mobile Filter Button -->
            <button id="mobile-filter-btn" class="lg:hidden fixed bottom-6 right-6 z-50 px-6 py-4 bg-primary text-white rounded-full shadow-2xl transition-all duration-300 hover:shadow-3xl transform hover:scale-110 flex items-center space-x-2">
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
                            <button id="close-modal" type="button" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="overflow-y-auto max-h-[calc(90vh-160px)]">
                            <x-filter :brands="$brands" :categories="$categories" :subCategories="$subCategories" :availabilityCounts="$availabilityCounts ?? []" :minPrice="$minPrice ?? 0" :maxPrice="$maxPrice ?? 5000" :isModal="true" />
                        </div>

                        <div class="sticky bottom-0 bg-white border-t p-4 flex gap-3">
                            <button type="button" id="close-modal-btn" class="flex-1 inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                            <button type="button" id="apply-mobile-filters" class="flex-1 inline-flex justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90">Apply Filters</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/price-range-slider.js') }}"></script>
    <script src="{{ asset('js/products.js') }}"></script>
@endpush
