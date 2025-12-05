<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24']) }}>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filters
        </h2>
        <button class="text-sm font-medium text-primary hover:opacity-80 transition-opacity">Clear
            All</button>
    </div>

    <div class="space-y-6 max-h-[calc(100vh-250px)] overflow-y-auto pr-2">

        <!-- Price Range Filter -->
        <div class="border-b border-gray-100 pb-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Price Range
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>$0</span>
                    <span>$5000+</span>
                </div>
                <input type="range" min="0" max="5000" value="2500"
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary">
                <div class="flex items-center gap-2">
                    <input type="number" placeholder="Min"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                    <span class="text-gray-400">-</span>
                    <input type="number" placeholder="Max"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Brand Filter -->
        <div class="border-b border-gray-100 pb-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Brand
            </h3>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach ($brands as $brand)

                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ $brand->name }}</span>
                    <span class="ml-auto text-xs text-gray-400">({{ $brand->products ? $brand->products->count() : 0 }})</span>
                </label>

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
                <div class="category-item">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox"
                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary category-checkbox"
                            data-category-id="{{ $category->id }}">
                        <span
                            class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">{{ $category->{'category-name'} }}</span>
                        <span class="ml-auto text-xs text-gray-400">({{ $category->products ? $category->products->count() : 0 }})</span>
                        @if($category->subcategories && $category->subcategories->count() > 0)
                        <button class="ml-2 text-gray-400 hover:text-primary transition-colors toggle-subcategories"
                                data-category-id="{{ $category->id }}">
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        @endif
                    </label>
                    @if($category->subcategories && $category->subcategories->count() > 0)
                    <div class="subcategories-container ml-7 mt-2 space-y-1 hidden" data-category-id="{{ $category->id }}">
                        @foreach ($category->subcategories as $subcategory)
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox"
                                class="w-3 h-3 text-primary border-gray-300 rounded focus:ring-primary subcategory-checkbox"
                                data-category-id="{{ $category->id }}"
                                data-subcategory-id="{{ $subcategory->id }}">
                            <span
                                class="ml-3 text-xs text-gray-600 group-hover:text-primary transition-colors">{{ $subcategory->{'sub-category-name'} }}</span>
                            <span class="ml-auto text-xs text-gray-400">({{ $subcategory->products ? $subcategory->products->count() : 0 }})</span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
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
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span class="ml-3 flex items-center">
                        <span class="text-yellow-400">★★★★★</span>
                        <span
                            class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">(5
                            stars)</span>
                    </span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span class="ml-3 flex items-center">
                        <span class="text-yellow-400">★★★★</span><span class="text-gray-300">★</span>
                        <span
                            class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">&
                            Up</span>
                    </span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span class="ml-3 flex items-center">
                        <span class="text-yellow-400">★★★</span><span class="text-gray-300">★★</span>
                        <span
                            class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">&
                            Up</span>
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
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">In
                        Stock</span>
                    <span class="ml-auto text-xs text-gray-400">(184)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Pre-Order</span>
                    <span class="ml-auto text-xs text-gray-400">(12)</span>
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
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">50%
                        or more</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">40%
                        or more</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">30%
                        or more</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">20%
                        or more</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <span
                        class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">10%
                        or more</span>
                </label>
            </div>
        </div>

    </div>

    <!-- Apply Filters Button -->
    <button
        class="w-full mt-6 px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
        Apply Filters
    </button>
</div>
