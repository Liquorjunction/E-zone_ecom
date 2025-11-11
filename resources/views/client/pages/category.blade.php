@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen">

  <!-- Category Hero Banner -->
  <div class="relative bg-linear-to-r from-primary to-primary/80 overflow-hidden">
    <div class="absolute inset-0">
      <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-white -mr-48 -mt-48"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full opacity-10 blur-3xl bg-white -ml-48 -mb-48"></div>
    </div>

    <div class="container mx-auto px-4 py-16 relative">
      <div class="max-w-3xl">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-white/80 mb-4">
          <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <a href="{{ route('products') }}" class="hover:text-white transition-colors">Products</a>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <span class="text-white font-medium">Computers & Laptops</span>
        </div>

        <!-- Category Title -->
        <div class="flex items-center mb-4">
          <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mr-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2">
              Computers & Laptops
            </h1>
            <p class="text-lg text-white/90">Discover cutting-edge computing solutions for work and play</p>
          </div>
        </div>

        <!-- Category Stats -->
        <div class="flex flex-wrap gap-6 mt-8">
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="font-semibold">156 Products</span>
          </div>
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
            <span class="font-semibold">7 Brands</span>
          </div>
          <div class="flex items-center text-white/90">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            <span class="font-semibold">Hot Deals Available</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">

    <!-- Subcategories Section -->
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
        Browse by Subcategory
      </h2>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <!-- Subcategory Card 1 -->
        <a href="#" class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:scale-110">
            <svg class="w-8 h-8 text-primary transition-colors group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">Desktop PCs</h3>
          <p class="text-xs text-gray-500">45 products</p>
        </a>

        <!-- Subcategory Card 2 -->
        <a href="#" class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:scale-110">
            <svg class="w-8 h-8 text-primary transition-colors group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">Laptops</h3>
          <p class="text-xs text-gray-500">67 products</p>
        </a>

        <!-- Subcategory Card 3 -->
        <a href="#" class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:scale-110">
            <svg class="w-8 h-8 text-primary transition-colors group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">Monitors</h3>
          <p class="text-xs text-gray-500">34 products</p>
        </a>

        <!-- Subcategory Card 4 -->
        <a href="#" class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:scale-110">
            <svg class="w-8 h-8 text-primary transition-colors group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">Components</h3>
          <p class="text-xs text-gray-500">28 products</p>
        </a>

        <!-- Subcategory Card 5 -->
        <a href="#" class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:scale-110">
            <svg class="w-8 h-8 text-primary transition-colors group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
          </div>
          <h3 class="font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors">Peripherals</h3>
          <p class="text-xs text-gray-500">52 products</p>
        </a>
      </div>
    </div>

    <!-- Search and Sort Bar -->
    <div class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Search in Computers & Laptops..."
              class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <!-- Sort Dropdown -->
        <div class="lg:w-64">
          <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
            <option>Sort by: Featured</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Newest First</option>
            <option>Best Selling</option>
            <option>Top Rated</option>
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
              Filters
            </h2>
            <button class="text-sm font-medium text-primary hover:opacity-80 transition-opacity">Clear All</button>
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
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Dell</span>
                  <span class="ml-auto text-xs text-gray-400">(24)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">HP</span>
                  <span class="ml-auto text-xs text-gray-400">(22)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Lenovo</span>
                  <span class="ml-auto text-xs text-gray-400">(18)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Asus</span>
                  <span class="ml-auto text-xs text-gray-400">(15)</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Acer</span>
                  <span class="ml-auto text-xs text-gray-400">(12)</span>
                </label>
              </div>
            </div>

            <!-- Processor Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Processor
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Intel Core i9</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Intel Core i7</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">Intel Core i5</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">AMD Ryzen 9</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">AMD Ryzen 7</span>
                </label>
              </div>
            </div>

            <!-- RAM Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                RAM
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">4GB</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">8GB</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">16GB</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">32GB</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">64GB+</span>
                </label>
              </div>
            </div>

            <!-- Storage Filter -->
            <div class="border-b border-gray-100 pb-6">
              <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-1 h-5 rounded-full bg-primary mr-2"></span>
                Storage
              </h3>
              <div class="space-y-2">
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">256GB SSD</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">512GB SSD</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">1TB SSD</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 text-sm text-gray-700 group-hover:text-primary transition-colors">2TB+ SSD</span>
                </label>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="pb-2">
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
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 flex items-center">
                    <span class="text-yellow-400">★★</span><span class="text-gray-300">★★★</span>
                    <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">& Up</span>
                  </span>
                </label>
                <label class="flex items-center group cursor-pointer">
                  <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                  <span class="ml-3 flex items-center">
                    <span class="text-yellow-400">★</span><span class="text-gray-300">★★★★</span>
                    <span class="ml-2 text-sm text-gray-700 group-hover:text-primary transition-colors">& Up</span>
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  @endsection
