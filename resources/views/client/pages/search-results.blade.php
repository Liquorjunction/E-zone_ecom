@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen">

  <!-- Search Hero Banner -->
  <div class="relative bg-linear-to-r from-primary to-primary/80 overflow-hidden">
    <div class="absolute inset-0">
      <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-white -mr-48 -mt-48"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-white -ml-48 -mb-48"></div>
    </div>

    <div class="container mx-auto px-4 py-16 relative">
      <div class="max-w-4xl">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-white/80 mb-4">
          <a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ statictranslations('Home') }}</a>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <span class="text-white font-medium">{{ statictranslations('Search Results') }}</span>
        </div>

        <!-- Search Title -->
        <div class="flex items-center mb-4">
          <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mr-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2">
              {{ statictranslations('Search Results') }}
            </h1>
            <p class="text-lg text-white/90">{{ statictranslations('Showing results for') }} "<span class="font-semibold">{{ request('q') ?: statictranslations('wireless headphones') }}</span>"</p>
          </div>
        </div>

        <!-- Search Stats -->
        <div class="flex flex-wrap gap-6 mt-8">
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="font-semibold">{{ statictranslations('156 Products Found') }}</span>
          </div>
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold">{{ statictranslations('Instant Results') }}</span>
          </div>
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            <span class="font-semibold">{{ statictranslations('Best Deals Available') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">

    <!-- Search and Sort Bar -->
    <div class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
          <form method="GET" action="{{ route('search') }}" class="relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ statictranslations('Search products...') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </form>
        </div>

        <!-- Sort Dropdown -->
        <div class="lg:w-64">
          <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
            <option>{{ statictranslations('Sort by: Featured') }}</option>
            <option>{{ statictranslations('Price: Low to High') }}</option>
            <option>{{ statictranslations('Price: High to Low') }}</option>
            <option>{{ statictranslations('Newest First') }}</option>
            <option>{{ statictranslations('Best Selling') }}</option>
            <option>{{ statictranslations('Top Rated') }}</option>
          </select>
        </div>

        <!-- View Toggle -->
        <div class="hidden lg:flex items-center space-x-2 border-2 border-gray-200 rounded-xl p-1">
          <button class="p-2 rounded-lg bg-primary text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </button>
          <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-6">

      <!-- Filters Sidebar -->
      <aside class="lg:w-80">
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
              <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              {{ statictranslations('Filters') }}
            </h2>
            <button class="text-sm font-medium text-primary hover:opacity-80 transition-opacity">{{ statictranslations('Clear All') }}</button>
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
                <input type="range" min="0" max="5000" value="2500" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary">
                <div class="flex items-center gap-2">
                  <input type="number" placeholder="Min" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                  <span class="text-gray-400">-</span>
                  <input type="number" placeholder="Max" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary">
                </div>
              </div>
            </div>

            <!-- Brand Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Brand
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Apple</span>
                  <span class="ml-auto text-xs text-gray-400">(45)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Samsung</span>
                  <span class="ml-auto text-xs text-gray-400">(38)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Sony</span>
                  <span class="ml-auto text-xs text-gray-400">(29)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Dell</span>
                  <span class="ml-auto text-xs text-gray-400">(24)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">HP</span>
                  <span class="ml-auto text-xs text-gray-400">(22)</span>
                </label>
              </div>
            </div>

            <!-- Condition Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Condition
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">New</span>
                  <span class="ml-auto text-xs text-gray-400">(156)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Refurbished</span>
                  <span class="ml-auto text-xs text-gray-400">(42)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Open Box</span>
                  <span class="ml-auto text-xs text-gray-400">(18)</span>
                </label>
              </div>
            </div>

            <!-- Category Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Category
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Computers</span>
                  <span class="ml-auto text-xs text-gray-400">(156)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Laptops</span>
                  <span class="ml-auto text-xs text-gray-400">(42)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Tablets</span>
                  <span class="ml-auto text-xs text-gray-400">(18)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Smartphones</span>
                  <span class="ml-auto text-xs text-gray-400">(15)</span>
                </label>
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
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 flex items-center">
                    <span class="text-yellow-400">★★★★★</span>
                    <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">(5 stars)</span>
                  </span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 flex items-center">
                    <span class="text-yellow-400">★★★★</span><span class="text-gray-300">★</span>
                    <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">& Up</span>
                  </span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 flex items-center">
                    <span class="text-yellow-400">★★★</span><span class="text-gray-300">★★</span>
                    <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">& Up</span>
                  </span>
                </label>
              </div>
            </div>

            <!-- Availability Filter -->
            <div class="pb-2">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Availability
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">In Stock</span>
                  <span class="ml-auto text-xs text-gray-400">(184)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Pre-Order</span>
                  <span class="ml-auto text-xs text-gray-400">(12)</span>
                </label>
              </div>
            </div>

          </div>

          <!-- Apply Filters Button -->
          <button class="w-full mt-6 px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
            Apply Filters
          </button>
        </div>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </span>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary">
            In Stock
            <button class="ml-2 hover:text-primary/70">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </span>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
          <p class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">1-12</span> of <span class="font-semibold text-gray-900">156</span> results for "<span class="font-semibold text-primary">{{ request('q') ?: 'wireless headphones' }}</span>"</p>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          @for($i = 1; $i <= 12; $i++)
          <div class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-gray-100">
            <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
              <img src="https://via.placeholder.com/400x400?text=Product+{{$i}}" alt="Product {{$i}}" class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">

              <!-- Badges -->
              <div class="absolute top-3 left-3 flex flex-col gap-2">
                @if($i % 3 == 0)
                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                  -{{ rand(10, 40) }}%
                </span>
                @endif
                @if($i % 4 == 0)
                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                  NEW
                </span>
                @endif
              </div>

              <!-- Wishlist Button -->
              <button class="absolute top-3 right-3 w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-red-50">
                <svg class="w-5 h-5 text-gray-600 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>

              <!-- Quick View -->
              <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/50 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <button class="w-full px-4 py-2 bg-white text-gray-900 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors">
                  Quick View
                </button>
              </div>
            </a>

            <div class="p-5">
              <!-- Category Badge -->
              <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">
                {{ ['Laptops', 'Smartphones', 'Headphones', 'Cameras', 'Tablets'][rand(0, 4)] }}
              </span>

              <a href="#">
                <h3 class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                  Premium Electronics Product {{$i}}
                </h3>
              </a>

              <!-- Rating -->
              <div class="flex items-center mt-2">
                <div class="flex items-center text-yellow-400 text-sm">
                  ★★★★★
                </div>
                <span class="ml-2 text-sm text-gray-500">({{ rand(10, 500) }})</span>
              </div>

              <!-- Features -->
              <ul class="mt-3 space-y-1 text-xs text-gray-600">
                {{-- <li class="flex items-center">
                  <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  Free Shipping
                </li> --}}
                <li class="flex items-center">
                  <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  1 Year Warranty
                </li>
              </ul>

              <!-- Price and Add to Cart -->
              <div class="mt-4 flex items-center justify-between">
                <div>
                  <p class="text-2xl font-bold text-primary">${{ rand(199, 999) }}.99</p>
                  @if($i % 3 == 0)
                  <p class="text-sm text-gray-400 line-through">${{ rand(299, 1299) }}.99</p>
                  @endif
                </div>
                <button class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 group-hover:bg-primary">
                  <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                  </svg>
                </button>
              </div>

              <!-- Stock Status -->
              @if($i % 5 == 0)
              <div class="mt-3 text-xs text-orange-600 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Only {{ rand(2, 8) }} left in stock
              </div>
              @else
              <div class="mt-3 text-xs text-green-600 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                In Stock
              </div>
              @endif
            </div>
          </div>
          @endfor
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <p class="text-sm text-gray-600">
            Showing <span class="font-semibold">1-12</span> of <span class="font-semibold">156</span> products
          </p>

          <div class="flex items-center space-x-2">
            <!-- Previous Button -->
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            <!-- Page Links -->
            <button class="px-4 py-2 bg-primary text-white rounded-lg font-medium shadow-lg">1</button>
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">2</button>
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">3</button>
            <span class="px-2 text-gray-400">...</span>
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">13</button>

            <!-- Next Button -->
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Mobile Filter Button -->
    <button class="lg:hidden fixed bottom-6 right-6 z-50 px-6 py-4 bg-primary text-white rounded-full shadow-2xl transition-all duration-300 hover:shadow-3xl transform hover:scale-110 flex items-center space-x-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
      </svg>
      <span class="font-semibold">Filters</span>
    </button>

    </div>
</div>

@push('scripts')
<script>
  // Mobile filter toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.querySelector('.lg\\:hidden.fixed');

    if (filterButton) {
      filterButton.addEventListener('click', function() {
        // Add mobile filter modal functionality here
        // For now, we'll keep the placeholder alert
        alert('Mobile filter modal coming soon!');
      });
    }
  });
</script>
@endpush
@endsection
