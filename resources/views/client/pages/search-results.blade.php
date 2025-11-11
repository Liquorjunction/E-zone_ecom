@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white py-8">
  <div class="container mx-auto px-4">

    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Search Results
          </h1>
          <p class="text-gray-600">Showing results for "<span class="font-semibold text-primary">{{ request('q') ?: 'wireless headphones' }}</span>"</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Search Results</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Sort Bar -->
    <div class="mb-6 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-4">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
          <form method="GET" action="{{ route('search') }}" class="relative">
            <input 
