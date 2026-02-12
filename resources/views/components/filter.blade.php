@props([
    'brands' => [],
    'categories' => [],
    'subCategories' => [],
    'availabilityCounts' => ['in_stock' => 0, 'out_of_stock' => 0],
    'isModal' => false,
    'maxPrice' => 5000,
])

@php
    // Keep attributes safe — remove props from the attributes bag so arrays don't become HTML attributes
if (!isset($attributes) || !$attributes instanceof \Illuminate\View\ComponentAttributeBag) {
    $attributes = new \Illuminate\View\ComponentAttributeBag($attributes ?? []);
}
$attributes = $attributes->except(['brands', 'categories', 'subCategories', 'isModal', 'maxPrice']);
$passedClass = $attributes->get('class');
if (is_array($passedClass)) {
    $attributes = $attributes->except('class')->merge(['class' => implode(' ', $passedClass)]);
}

// Helper function to count active products with active variants
$countActiveVariants = function ($products) {
    return $products
        ? $products->sum(function ($p) {
            return ($p->status ?? null) === 'active' && $p->productVariants
                ? $p->productVariants->where('status', 'active')->count()
                    : 0;
            })
            : 0;
    };
@endphp

@php
    $baseClass = 'bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6';
    if (!($isModal ?? false)) {
        $baseClass .= ' sticky top-24';
    }
@endphp

<form id="filter-form" method="GET" action="{{ route('products.index') }}">
    <input type="hidden" name="view" id="view-input" value="{{ request('view', 'grid') }}">
    <div {{ $attributes->merge(['class' => $baseClass]) }}>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </h2>
            <a href="{{ route('products.index') }}"
                class="text-sm font-medium text-primary hover:opacity-80 transition-opacity cursor-pointer hover:underline">
                Clear All
            </a>
        </div>

        <div
            class="space-y-6 pr-2 @unless ($isModal ?? false) max-h-[calc(100vh-250px)] overflow-y-auto @endunless">

            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Price Range
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm font-medium text-gray-700">
                        <span id="min-price-display">${{ request('min_price') ? number_format(request('min_price'), 0) : '0' }}</span>
                        <span class="text-gray-400">-</span>
                        <span id="max-price-display">${{ request('max_price') ? number_format(request('max_price'), 0) : number_format($maxPrice, 0) }}</span>
                    </div>

                    <!-- Dual Range Slider Container -->
                    <div class="relative pt-2 pb-2">
                        <!-- Background Track -->
                        <div class="absolute w-full h-2 bg-gray-200 rounded-lg pointer-events-none"></div>

                        <!-- Active Range Track -->
                        <div id="price-range-track" class="absolute h-2 bg-primary rounded-lg pointer-events-none"
                             style="left: {{ request('min_price') ? (request('min_price') / $maxPrice * 100) : 0 }}%; right: {{ request('max_price') ? (100 - (request('max_price') / $maxPrice * 100)) : 0 }}%;"></div>

                        <!-- Min Price Slider -->
                        <input type="range" id="min-price-range" min="0" max="{{ $maxPrice }}" step="10"
                            value="{{ request('min_price', 0) }}"
                            class="absolute w-full appearance-none cursor-pointer bg-transparent"
                            style="top: 0; z-index: 5; pointer-events: auto; height: 2px;">

                        <!-- Max Price Slider -->
                        <input type="range" id="max-price-range" min="0" max="{{ $maxPrice }}" step="10"
                            value="{{ request('max_price', $maxPrice) }}"
                            class="absolute w-full appearance-none cursor-pointer bg-transparent"
                            style="top: 0; z-index: 5; pointer-events: auto; height: 2px;">
                    </div>

                    <!-- Number Inputs -->
                    <div class="flex items-center gap-2 pt-2">
                        <input type="number" name="min_price" id="min-price" placeholder="Min"
                            value="{{ request('min_price') }}" min="0" max="{{ $maxPrice }}" step="10"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                        <span class="text-gray-400">-</span>
                        <input type="number" name="max_price" id="max-price" placeholder="Max"
                            value="{{ request('max_price') }}" min="0" max="{{ $maxPrice }}" step="10"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                    </div>
                </div>

                <style>
                    /* Custom styling for range sliders */
                    input[type="range"] {
                        -webkit-appearance: none;
                        appearance: none;
                    }

                    input[type="range"]::-webkit-slider-thumb {
                        -webkit-appearance: none;
                        appearance: none;
                        width: 18px;
                        height: 18px;
                        border-radius: 50%;
                        background: white;
                        border: 2px solid #3b82f6;
                        cursor: pointer;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                        position: relative;
                        z-index: 10;
                    }

                    input[type="range"]::-moz-range-thumb {
                        width: 18px;
                        height: 18px;
                        border-radius: 50%;
                        background: white;
                        border: 2px solid #3b82f6;
                        cursor: pointer;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    }

                    input[type="range"]::-webkit-slider-thumb:hover {
                        border-color: #2563eb;
                        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
                    }

                    input[type="range"]::-moz-range-thumb:hover {
                        border-color: #2563eb;
                        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
                    }

                    input[type="range"]:focus {
                        outline: none;
                    }
                </style>
            </div>

            <!-- Brand Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Brand
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach ($brands as $brand)
                        @if (is_object($brand))
                            <label class="flex items-center group cursor-pointer">
                                <input type="checkbox" name="brand[]" value="{{ $brand->slug }}"
                                    {{ in_array($brand->slug, (array) request('brand', [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <span
                                    class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ $brand->name }}</span>
                                <span
                                    class="ml-auto text-xs text-gray-400">({{ $countActiveVariants($brand->products) }})</span>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Category Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Category
                </h3>
                <div class="space-y-2">
                    @foreach ($categories as $category)
                        @if (is_object($category))
                            <div class="category-item">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" name="category[]" value="{{ $category->slug }}"
                                        {{ in_array($category->slug, (array) request('category', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary category-checkbox"
                                        data-category-slug="{{ $category->slug }}">
                                    <span
                                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ $category->{'category-name'} }}</span>
                                    <span
                                        class="ml-auto text-xs text-gray-400">({{ $countActiveVariants($category->products) }})</span>
                                    @if ($category->subcategories && $category->subcategories->count() > 0)
                                        <button type="button"
                                            class="ml-2 text-gray-400 hover:text-primary transition-colors toggle-subcategories"
                                            data-category-id="{{ $category->id }}"
                                            data-category-slug="{{ $category->slug }}">
                                            <svg class="w-4 h-4 transform transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    @endif
                                </label>
                                @if ($category->subcategories && $category->subcategories->count() > 0)
                                    <div class="subcategories-container ml-7 mt-2 space-y-1 hidden"
                                        data-category-slug="{{ $category->slug }}">
                                        @foreach ($category->subcategories as $subcategory)
                                            <label class="flex items-center group cursor-pointer">
                                                <input type="checkbox" name="subcategory[]"
                                                    value="{{ $subcategory->slug }}"
                                                    {{ in_array($subcategory->slug, (array) request('subcategory', [])) ? 'checked' : '' }}
                                                    class="w-3 h-3 text-primary border-gray-300 rounded focus:ring-primary subcategory-checkbox"
                                                    data-category-slug="{{ $category->slug }}"
                                                    data-subcategory-id="{{ $subcategory->id }}">
                                                <span
                                                    class="ml-3 text-xs text-gray-600 group-hover:text-primary transition-colors">{{ $subcategory->{'sub-category-name'} }}</span>
                                                <span
                                                    class="ml-auto text-xs text-gray-400">({{ $countActiveVariants($subcategory->products) }})</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Customer Rating
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="5"
                            {{ in_array('5', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★★★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">(5
                                stars)</span>
                        </span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="4"
                            {{ in_array('4', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★★</span><span class="text-gray-300">★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">(4
                                stars)</span>
                        </span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="3"
                            {{ in_array('3', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★</span><span class="text-gray-300">★★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">(3
                                stars)</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Availability Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Availability
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="availability[]" value="in_stock"
                            {{ in_array('in_stock', (array) request('availability', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">In
                            Stock</span>
                        <span
                            class="ml-auto text-xs text-gray-400">({{ $availabilityCounts['in_stock'] ?? 0 }})</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="availability[]" value="out_of_stock"
                            {{ in_array('out_of_stock', (array) request('availability', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Out of
                            Stock</span>
                        <span
                            class="ml-auto text-xs text-gray-400">({{ $availabilityCounts['out_of_stock'] ?? 0 }})</span>
                    </label>
                </div>
            </div>

            <!-- Discount Filter -->
            <div class="pb-2">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    Discount
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="50"
                            {{ in_array('50', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">50% or
                            more</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="40"
                            {{ in_array('40', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">40% or
                            more</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="30"
                            {{ in_array('30', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">30% or
                            more</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="20"
                            {{ in_array('20', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">20% or
                            more</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="10"
                            {{ in_array('10', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">10% or
                            more</span>
                    </label>
                </div>
            </div>

        </div>

        <!-- Apply Filters Button (kept inside container so it can be sticky) -->
        @unless ($isModal ?? false)
            <div class="mt-6 md:hidden lg:hidden">
                <div class="sticky bottom-4 z-40 bg-white pt-4 -mx-6 px-6">
                    <button type="submit"
                        class="w-full px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
                        Apply Filters
                    </button>
                </div>
            </div>
        @endunless

    </div>
</form>
