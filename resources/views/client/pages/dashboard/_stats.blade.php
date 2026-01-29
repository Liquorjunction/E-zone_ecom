<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
  <a href="{{ route('dashboard.orders') }}" class="group bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow block focus:outline-none focus:ring-2 focus:ring-primary/50">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Total Orders</p>
        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $ordersCount ?? 0 }}</p>
      </div>
      <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      </div>
    </div>
  </a>

  <a href="{{ route('dashboard.wishlist') }}" class="group bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow block focus:outline-none focus:ring-2 focus:ring-primary/50">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Wishlist Items</p>
        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $wishlistCount ?? 0 }}</p>
      </div>
      <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </div>
    </div>
  </a>

  <a href="{{ route('dashboard.addresses') }}" class="group bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow block focus:outline-none focus:ring-2 focus:ring-primary/50 sm:col-span-2 lg:col-span-1">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Saved Addresses</p>
        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $addressesCount ?? 0 }}</p>
      </div>
      <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </div>
    </div>
  </a>
</div>
