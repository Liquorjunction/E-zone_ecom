@props([
    'brands' => [],
    'categories' => [],
    'subCategories' => [],
    'availabilityCounts' => ['in_stock' => 0, 'out_of_stock' => 0],
    'minPrice' => 0,
    'maxPrice' => 5000,
    'isModal' => false,
])

@php
    // Keep attributes safe — remove props from the attributes bag so arrays don't become HTML attributes
if (!isset($attributes) || !$attributes instanceof \Illuminate\View\ComponentAttributeBag) {
    $attributes = new \Illuminate\View\ComponentAttributeBag($attributes ?? []);
}
$attributes = $attributes->except(['brands', 'categories', 'subCategories', 'isModal']);
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
                {{ statictranslations('Filters') }}
            </h2>
            <a href="{{ route('products.index') }}"
                class="text-sm font-medium text-primary hover:opacity-80 transition-opacity cursor-pointer hover:underline">
                {{ statictranslations('Clear All') }}
            </a>
        </div>

        <div
            class="space-y-6 pr-2 @unless ($isModal ?? false) max-h-[calc(100vh-250px)] overflow-y-auto @endunless">

            <!-- Price Range Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    {{ statictranslations('Price Range') }}
                </h3>
                {{--
                    data-floor = absolute minimum (0 or DB floor)
                    data-ceil  = maximum from DB (maxPrice)
                    JS reads these — no step, no native range input, no snapping.
                --}}
                <div class="space-y-3"
                     data-price-range
                     data-floor="{{ 0 }}"
                     data-ceil="{{ $maxPrice }}"
                     data-default-min="{{ 0 }}"
                     data-default-max="{{ $maxPrice }}"
                     data-price-dirty="false">

                    {{-- Static label row --}}
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ config('app.CURRENCY_SYMBOL') }}0</span>
                        <span>{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($maxPrice) }}+</span>
                    </div>

                    {{--
                        Custom slider track.
                        NO <input type="range"> — those cause native step-snapping.
                        Two <div> thumbs are dragged via pointer events in JS.
                    --}}
                        <div class="relative h-10 flex items-center touch-none select-none w-11/12 mx-auto" data-range-root>
                            {{-- Grey background rail --}}
                            <div class="absolute left-0 right-0 h-1.5 bg-gray-200 rounded-full w-full"></div>

                            {{-- Coloured active track (JS sets left / right) --}}
                            <div data-range-track
                                class="absolute h-1.5 bg-primary rounded-full"
                                style="left:0%;right:0%"></div>

                        {{-- Min thumb --}}
                        <div data-thumb="min"
                            role="slider"
                            aria-label="{{ statictranslations('Minimum price') }}"
                            tabindex="0"
                            class="absolute w-5 h-5 -translate-x-1/2 rounded-full bg-white border-2 border-primary shadow cursor-grab active:cursor-grabbing focus:outline-none focus:ring-2 focus:ring-primary/40 z-10"
                            style="left:0%"></div>

                        {{-- Max thumb --}}
                        <div data-thumb="max"
                            role="slider"
                            aria-label="{{ statictranslations('Maximum price') }}"
                            tabindex="0"
                            class="absolute w-5 h-5 -translate-x-1/2 rounded-full bg-white border-2 border-primary shadow cursor-grab active:cursor-grabbing focus:outline-none focus:ring-2 focus:ring-primary/40 z-10"
                            style="left:100%"></div>
                    </div>

                    {{-- Number inputs — free-form, validated on blur / Enter --}}
                    <div class="flex items-center gap-2">
                        <div class="flex flex-1 items-center bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 focus-within:border-primary">
                            <span class="text-sm text-gray-500 mr-1 whitespace-nowrap">{{ config('app.CURRENCY_SYMBOL') }}</span>
                            <input type="number"
                                   min="0"
                                   max="{{ $maxPrice }}"
                                   placeholder="0"
                                   data-input="min"
                                   data-name="min_price"
                                   value="{{ request('min_price', '') }}"
                                   class="w-full bg-transparent border-none outline-none focus:ring-0 text-sm">
                        </div>
                        <span class="text-gray-300 font-light">—</span>
                        <div class="flex flex-1 items-center bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 focus-within:border-primary">
                            <span class="text-sm text-gray-500 mr-1 whitespace-nowrap">{{ config('app.CURRENCY_SYMBOL') }}</span>
                            <input type="number"
                                   min="0"
                                   max="{{ $maxPrice }}"
                                   placeholder="{{ $maxPrice }}"
                                   data-input="max"
                                   data-name="max_price"
                                   value="{{ request('max_price', '') }}"
                                   class="w-full bg-transparent border-none outline-none focus:ring-0 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    {{ statictranslations('Brand') }}
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach ($brands as $brand)
                        @if (is_object($brand) && $countActiveVariants($brand->products) > 0)
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
                    {{ statictranslations('Category') }}
                </h3>
                <div class="space-y-2">
                    @foreach ($categories as $category)
                        @if (is_object($category) && $countActiveVariants($category->products) > 0)
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
                                            @if ($countActiveVariants($subcategory->products) > 0)
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
                                            @endif
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
                    {{ statictranslations('Customer Rating') }}
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="5"
                            {{ in_array('5', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★★★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">({{ statictranslations('5 stars') }})</span>
                        </span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="4"
                            {{ in_array('4', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★★</span><span class="text-gray-300">★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">({{ statictranslations('4 stars and up') }})</span>
                        </span>
                        </span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="rating[]" value="3"
                            {{ in_array('3', (array) request('rating', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 flex items-center">
                            <span class="text-yellow-400">★★★</span><span class="text-gray-300">★★</span>
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">({{ statictranslations('3 stars and up') }})</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Availability Filter -->
            <div class="border-b border-gray-100 pb-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    {{ statictranslations('Availability') }}
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="availability[]" value="in_stock"
                            {{ in_array('in_stock', (array) request('availability', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('In Stock') }}</span>
                        <span
                            class="ml-auto text-xs text-gray-400">({{ $availabilityCounts['in_stock'] ?? 0 }})</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="availability[]" value="out_of_stock"
                            {{ in_array('out_of_stock', (array) request('availability', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('Out of Stock') }}</span>
                        <span
                            class="ml-auto text-xs text-gray-400">({{ $availabilityCounts['out_of_stock'] ?? 0 }})</span>
                    </label>
                </div>
            </div>

            <!-- Discount Filter -->
            <div class="pb-2">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                    {{ statictranslations('Discount') }}
                </h3>
                <div class="space-y-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="50"
                            {{ in_array('50', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('50% or more') }}</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="40"
                            {{ in_array('40', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('40% or more') }}</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="30"
                            {{ in_array('30', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('30% or more') }}</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="20"
                            {{ in_array('20', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('20% or more') }}</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="discount[]" value="10"
                            {{ in_array('10', (array) request('discount', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ statictranslations('10% or more') }}</span>
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
                        {{ statictranslations('Apply Filters') }}
                    </button>
                </div>
            </div>
        @endunless

    </div>
</form>
