<div class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    id="products-search"
                    form="filter-form"
                    value="{{ request('search') }}"
                    placeholder="{{ statictranslations('Search for products, brands, or categories...') }}"
                    class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button
                    type="button"
                    id="clear-search"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Sort Dropdown -->
        <div class="lg:w-64">
            <select
                name="sort"
                id="products-sort"
                form="filter-form"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                <option value="newest" @selected(request('sort') === 'newest' || !request('sort'))>{{ statictranslations('Sort by: Newest') }}</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ statictranslations('Price: Low to High') }}</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ statictranslations('Price: High to Low') }}</option>
            </select>
        </div>

        <!-- View Toggle (Mobile Hidden) -->
        <div class="hidden lg:flex items-center space-x-2 border-2 border-gray-200 rounded-xl p-1">
            <button id="view-grid-btn" data-view="grid" class="p-2 rounded-lg bg-primary text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </button>
            <button id="view-list-btn" data-view="list" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</div>
