@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('Payment Methods'))

@section('dashboard-content')
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 lg:mb-6 gap-3">
    <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ statictranslations('Manage Your Payment Methods') }}</h2>
    <button class="px-4 lg:px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary/90 transition-colors">
      <span class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ statictranslations('Add New Card') }}
      </span>
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
    {{-- Default Card --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-primary p-4 lg:p-6">
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 gap-3">
        <div class="flex items-center gap-3">
          <svg class="h-8 flex-shrink-0" viewBox="0 0 48 32" fill="none">
            <rect width="48" height="32" rx="4" fill="#1434CB"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-weight="bold" font-size="14">VISA</text>
          </svg>
          <h3 class="text-base lg:text-lg font-bold text-gray-900">{{ statictranslations('Visa ending in 1234') }}</h3>
        </div>
        <span class="px-3 py-1 text-xs font-semibold text-primary bg-primary/10 rounded-full self-start">{{ statictranslations('Default') }}</span>
      </div>
      <div class="text-gray-700 space-y-1 lg:space-y-2">
        <p class="font-semibold">{{ statictranslations('John Doe') }}</p>
        <p class="text-sm lg:text-base">{{ statictranslations('Expires:') }} <span class="font-medium">12/2028</span></p>
      </div>
      <div class="flex items-center gap-2 mt-4 lg:mt-6 pt-4 lg:pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity">{{ statictranslations('Edit') }}</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80 transition-opacity">{{ statictranslations('Delete') }}</button>
      </div>
    </div>

    {{-- Secondary Card --}}
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:border-gray-300 transition-colors">
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 gap-3">
        <div class="flex items-center gap-3">
          <svg class="h-8 flex-shrink-0" viewBox="0 0 48 32" fill="none">
            <rect width="48" height="32" rx="4" fill="#EB001B"/>
            <circle cx="18" cy="16" r="10" fill="#FF5F00"/>
            <circle cx="30" cy="16" r="10" fill="#F79E1B"/>
          </svg>
          <h3 class="text-base lg:text-lg font-bold text-gray-900">{{ statictranslations('Mastercard ending in 5678') }}</h3>
        </div>
        <button class="text-xs lg:text-sm font-semibold text-gray-600 hover:text-primary transition-colors self-start whitespace-nowrap">
          {{ statictranslations('Set as Default') }}
        </button>
      </div>
      <div class="text-gray-700 space-y-1 lg:space-y-2">
        <p class="font-semibold">{{ statictranslations('John Doe') }}</p>
        <p class="text-sm lg:text-base">{{ statictranslations('Expires:') }} <span class="font-medium">08/2026</span></p>
      </div>
      <div class="flex items-center gap-2 mt-4 lg:mt-6 pt-4 lg:pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity">Edit</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80 transition-opacity">Delete</button>
      </div>
    </div>

    {{-- Add New Card --}}
    <a href="#" class="flex flex-col items-center justify-center w-full min-h-[160px] lg:min-h-[190px] border-4 border-dashed border-gray-200 rounded-xl lg:rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
      <svg class="w-12 h-12 lg:w-16 lg:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      <span class="font-semibold mt-2 text-sm lg:text-base">Add New Payment Method</span>
    </a>
  </div>
@endsection
