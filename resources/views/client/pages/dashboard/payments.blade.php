@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Payment Methods')

@section('dashboard-content')
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Manage Your Payment Methods</h2>
    <button class="px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg ...">
      <span class="flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
        Add New Card
      </span>
    </button>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-primary p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <svg class="h-8" viewBox="0 0 48 32" fill="none">
            <rect width="48" height="32" rx="4" fill="#1434CB"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-weight="bold" font-size="14">VISA</text>
          </svg>
          <h3 class="text-lg font-bold text-gray-900">Visa ending in 1234</h3>
        </div>
        <span class="px-3 py-1 text-xs font-semibold text-primary bg-primary/10 rounded-full">Default</span>
      </div>
      <div class="text-gray-700 space-y-2">
        <p class="font-semibold">John Doe</p>
        <p>Expires: <span class="font-medium">12/2028</span></p>
      </div>
      <div class="flex items-center gap-2 mt-6 pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80">Edit</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80">Delete</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <svg class="h-8" viewBox="0 0 48 32" fill="none">
            <rect width="48" height="32" rx="4" fill="#EB001B"/>
            <circle cx="18" cy="16" r="10" fill="#FF5F00"/>
            <circle cx="30" cy="16" r="10" fill="#F79E1B"/>
          </svg>
          <h3 class="text-lg font-bold text-gray-900">Mastercard ending in 5678</h3>
        </div>
        <button class="text-sm font-semibold text-gray-600 hover:text-primary">Set as Default</button>
      </div>
      <div class="text-gray-700 space-y-2">
        <p class="font-semibold">John Doe</p>
        <p>Expires: <span class="font-medium">08/2026</span></p>
      </div>
      <div class="flex items-center gap-2 mt-6 pt-6 border-t-2 border-gray-100">
        <button class="text-sm font-semibold text-primary hover:opacity-80">Edit</button>
        <span class="text-gray-300">|</span>
        <button class="text-sm font-semibold text-red-500 hover:opacity-80">Delete</button>
      </div>
    </div>

    <a href="#" class="flex flex-col items-center justify-center w-full min-h-[190px] border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
      <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
      <span class="font-semibold mt-2">Add New Payment Method</span>
    </a>
  </div>
@endsection
