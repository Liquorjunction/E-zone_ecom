@extends('client.layouts.app')

@section('content')
    {{-- Initialize Alpine Data --}}
    <div class="bg-gray-50 min-h-screen pb-20" x-data="{ view: 'grid', search: '' }">

        {{-- 1. HEADER / BREADCRUMBS (UNTOUCHED) --}}
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-8">
                <x-breadcrumbs>
                    <x-slot:title>All Brands</x-slot:title>
                    <x-slot:content>Discover the best deals on our platform.</x-slot:content>
                </x-breadcrumbs>
            </div>
        </div>

        {{-- Main Content Container --}}
        <div class="container mx-auto px-4 -mt-8 relative z-10">

            {{-- 2. NEW FILTER BAR (Card Design) --}}
            {{-- This "floating" card overlaps the header slightly or sits nicely below it --}}
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">

                {{-- Left Side: Search --}}
                <div class="relative w-full md:max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        x-model="search"
                        type="text"
                        placeholder="Search for a brand..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-gray-200 border rounded-lg focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 text-sm placeholder-gray-400"
                    >
                </div>

                {{-- Right Side: Meta & Toggles --}}
                <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-6">

                    {{-- Counter Text --}}
                    <span class="text-sm text-gray-500 font-medium hidden sm:block">
                        Showing <span class="text-gray-900 font-bold">{{ count($brands) }}</span> brands
                    </span>

                    {{-- View Toggles --}}
                    <div class="flex items-center bg-gray-100 p-1 rounded-lg">
                        <button
                            @click="view = 'grid'"
                            :class="{ 'bg-white text-primary shadow-sm': view === 'grid', 'text-gray-400 hover:text-gray-600': view !== 'grid' }"
                            class="p-2 rounded-md transition-all duration-200 focus:outline-none"
                            title="Grid View"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button
                            @click="view = 'list'"
                            :class="{ 'bg-white text-primary shadow-sm': view === 'list', 'text-gray-400 hover:text-gray-600': view !== 'list' }"
                            class="p-2 rounded-md transition-all duration-200 focus:outline-none"
                            title="List View"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. GRID / LIST CONTENT --}}
            <div :class="view === 'grid'
                ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6'
                : 'flex flex-col gap-4'"
            >
                @forelse ($brands as $brand)
                    {{-- Brand Card --}}
                    <div
                        x-show="search === '' || '{{ strtolower($brand->name) }}'.includes(search.toLowerCase())"
                        class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden"
                        :class="view === 'list' ? 'flex items-center p-4' : 'flex flex-col p-6'"
                    >

                        {{-- Image Area --}}
                        <div
                            class="relative flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-50/50"
                            :class="view === 'list' ? 'w-20 h-20' : 'w-full h-40 mb-4'"
                        >
                            @if($brand->image)
                                <img src="{{ asset('storage/' . $brand->image) }}"
                                     alt="{{ $brand->name }}"
                                     class="max-w-full max-h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                                >
                            @else
                                <span class="text-2xl font-bold text-gray-300">{{ substr($brand->name, 0, 1) }}</span>
                            @endif
                        </div>

                        {{-- Content Area --}}
                        <div class="flex-1 min-w-0" :class="view === 'list' ? 'ml-6' : 'text-center'">
                            <h2 class="text-lg font-bold text-gray-800 truncate group-hover:text-primary transition-colors">
                                {{ $brand->name }}
                            </h2>

                            {{-- Optional: Short description or Count --}}
                            {{-- <p class="text-xs text-gray-400 mt-1 line-clamp-1">Explore products from {{ $brand->name }}</p> --}}

                            <div class="mt-4">
                                <a href="{{ route('brand-products', $brand->slug) }}"
                                   class="inline-flex items-center text-sm font-semibold text-primary hover:text-primary-dark transition-colors"
                                >
                                    <span>Browse Collection</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No brands found</h3>
                        <p class="text-gray-500 mt-1">Try adjusting your search filters.</p>
                    </div>
                @endforelse

                {{-- Empty Search State --}}
                <div x-show="search !== '' && $el.parentElement.querySelectorAll('div[x-show]').length > 0 && Array.from($el.parentElement.querySelectorAll('div[x-show]')).every(el => el.style.display === 'none')"
                     class="hidden col-span-full py-12 text-center"
                     :class="{ '!block': true }"
                >
                    <p class="text-gray-500">No brands match your search "<span x-text="search" class="font-bold text-gray-900"></span>".</p>
                    <button @click="search = ''" class="mt-2 text-primary text-sm hover:underline">Clear Search</button>
                </div>
            </div>
        </div>
    </div>
@endsection
