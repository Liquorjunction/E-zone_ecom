@extends('client.layouts.app')

@section('content')
@php
  // Placeholder data for design
  $showrooms = [
    [
      'name' => 'Ezone Flagship - Jaipur',
      'address' => '123 C-Scheme, Pink City, Jaipur, Rajasthan, 302001',
      'contact' => '+91 141 234 5678',
      'email' => 'jaipur@ezone.com'
    ],
    [
      'name' => 'Ezone Tech Hub - Bengaluru',
      'address' => '456 Electronics City, Phase 1, Bengaluru, Karnataka, 560100',
      'contact' => '+91 80 456 7890',
      'email' => 'bengaluru@ezone.com'
    ],
    [
      'name' => 'Ezone Metro - Delhi',
      'address' => '789 Connaught Place, New Delhi, Delhi, 110001',
      'contact' => '+91 11 789 1234',
      'email' => 'delhi@ezone.com'
    ],
    [
      'name' => 'Ezone NYC - New York',
      'address' => '100 5th Ave, New York, NY 10011, United States',
      'contact' => '+1 (212) 555-0123',
      'email' => 'nyc@ezone.com'
    ],
    [
      'name' => 'Ezone Central - London',
      'address' => '200 Regent St, London, W1B 5SR, United Kingdom',
      'contact' => '+44 20 7123 4567',
      'email' => 'london@ezone.com'
    ],
    [
      'name' => 'Ezone Marina - Dubai',
      'address' => 'Dubai Marina Mall, Dubai, United Arab Emirates',
      'contact' => '+971 4 555 0199',
      'email' => 'dubai@ezone.com'
    ]
  ];
@endphp

<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Our Showrooms
          </h1>
          <p class="text-gray-600">Find an Ezone location near you, around the world.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Our Showrooms</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-8 bg-gray-200 rounded-2xl shadow-sm border-2 border-gray-100 h-96 flex items-center justify-center">
      <p class="text-gray-500 font-semibold text-lg">[Worldwide Map Placeholder]</p>
      </div>

    <div class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
      <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Search by city, state, or zip code..."
              class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <div class="lg:w-64">
          <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
            <option value="">Filter by Country</option>
            <option value="in">India</option>
            <option value="us">United States</option>
            <option value="uk">United Kingdom</option>
            <option value="ae">UAE</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      @foreach($showrooms as $showroom)
      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 flex flex-col transition-all duration-300 hover:shadow-lg">
        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $showroom['name'] }}</h3>

        <div class="space-y-3 text-gray-700 flex-1">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ $showroom['address'] }}</span>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span>{{ $showroom['contact'] }}</span>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>{{ $showroom['email'] }}</span>
          </div>
        </div>

        <a href="#" class="block w-full px-6 py-3 bg-primary/10 text-primary text-center font-bold rounded-xl transition-all duration-300 hover:bg-primary hover:text-white mt-6">
          Get Directions
        </a>
      </div>
      @endforeach

    </div>
  </div>
</div>
@endsection
