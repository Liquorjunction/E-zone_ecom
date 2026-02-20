@extends('client.layouts.app')

@section('content')
    @php
        $locale = app()->getLocale();
    @endphp

    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>Our Showrooms Around the World</x-slot:title>
                <x-slot:content>Discover our showrooms across the globe.</x-slot:content>
            </x-breadcrumbs>

            <div
                class="mb-8 bg-gray-200 rounded-2xl shadow-sm border-2 border-gray-100 flex items-center justify-center">
                <img src="{{ asset('icon/showroom.png') }}" alt="Worldwide Map" class="object-contain w-full h-auto rounded-2xl">
            </div>

            {{-- <form method="GET" action="{{ route('showrooms') }}" class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by city, state, country, or zip code..."
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
                            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="lg:w-64">
                        <select name="country"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                            <option value="">Filter by Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" @selected(request('country') === $country)>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:w-40">
                        <button type="submit"
                            class="w-full px-6 py-3 bg-primary text-white font-bold rounded-xl transition-all duration-300 hover:bg-primary/90">
                            Search
                        </button>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse ($showrooms as $showroom)
                    @php
                        $name = $showroom->getTranslation('name', $locale) ?? $showroom->name;
                        $address = $showroom->getTranslation('address', $locale) ?? $showroom->address;
                        $city = $showroom->getTranslation('city', $locale) ?? $showroom->city;
                        $state = $showroom->getTranslation('state', $locale) ?? $showroom->state;
                        $country = $showroom->getTranslation('country', $locale) ?? $showroom->country;
                        $location = trim(implode(', ', array_filter([$address, $city, $state, $country, $showroom->pincode])));
                    @endphp
                    <div
                        class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 flex flex-col transition-all duration-300 hover:shadow-lg">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $name }}</h3>

                        <div class="space-y-3 text-gray-700 flex-1">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $location }}</span>
                            </div>
                            @if (!empty($showroom->phone))
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $showroom->phone }}</span>
                                </div>
                            @endif
                            @if (!empty($showroom->email))
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $showroom->email }}</span>
                                </div>
                            @endif
                        </div>

                        @if (!empty($showroom->slug))
                            <a href="{{ route('showrooms.show', $showroom->slug) }}"
                                class="block w-full px-6 py-3 bg-primary/10 text-primary text-center font-bold rounded-xl transition-all duration-300 hover:bg-primary hover:text-white mt-6">
                                View Details
                            </a>
                        @else
                            <span
                                class="block w-full px-6 py-3 bg-gray-100 text-gray-500 text-center font-bold rounded-xl mt-6">
                                View Details
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border-2 border-gray-100 p-8 text-center text-gray-600">
                        No showrooms found for the selected filters.
                    </div>
                @endforelse

            </div>

            <div class="mt-8">
                {{ $showrooms->links() }}
            </div> --}}
        </div>
    </div>
@endsection
