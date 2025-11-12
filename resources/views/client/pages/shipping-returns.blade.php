@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Shipping & Returns
          </h1>
          <p class="text-gray-600">Our policies on shipping, delivery, and returns.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Shipping & Returns</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
          <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          Shipping Information
        </h2>
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Free Shipping</div>
              <div class="text-sm text-gray-600">Orders over $50 qualify for free standard shipping.</div>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Standard Shipping</div>
              <div class="text-sm text-gray-600">5-7 business days - $4.99</div>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Express Delivery</div>
              <div class="text-sm text-gray-600">1-2 business days - $9.99</div>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Same Day Delivery</div>
              <div class="text-sm text-gray-600">Available in select areas - $14.99</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
          <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
          </svg>
          Returns & Exchanges
        </h2>
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">30-Day Returns</div>
              <div class="text-sm text-gray-600">Return any item within 30 days of delivery for a full refund.</div>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Free Exchanges</div>
              <div class="text-sm text-gray-600">Exchange for a different size or color at no additional cost.</div>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
              <div class="font-medium text-gray-900">Easy Process</div>
              <div class="text-sm text-gray-600">Print a return label from your account or drop off at any of our partner locations.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
