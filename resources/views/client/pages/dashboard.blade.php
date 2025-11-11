@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            My Account
          </h1>
          <p class="text-gray-600">Welcome back, John Doe!</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{-- route('home') --}}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">My Account</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

      <aside class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">

          <div class="flex items-center gap-4 mb-6 pb-6 border-b-2 border-gray-100">
            <img src="https://via.placeholder.com/64x64" alt="User Avatar" class="w-16 h-16 rounded-full">
            <div>
              <h3 class="text-lg font-bold text-gray-900">John Doe</h3>
              <p class="text-sm text-gray-600">john.doe@example.com</p>
            </div>
          </div>

          <nav class="space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary font-semibold rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
              <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
              <span>My Orders</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              <span>My Profile</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              <span>Saved Addresses</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
              <span>Wishlist</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
              <span>Logout</span>
            </a>
          </nav>

        </div>
      </aside>

      <main class="lg:col-span-3 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <p class="text-3xl font-bold text-gray-900">12</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
              </div>
            </div>
          </div>
          <div class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Wishlist Items</p>
                <p class="text-3xl font-bold text-gray-900">8</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
              </div>
            </div>
          </div>
          <div class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Saved Addresses</p>
                <p class="text-3xl font-bold text-gray-900">3</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100">
          <div class="flex items-center justify-between p-6 border-b-2 border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
              <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
              Recent Orders
            </h2>
            <a href="#" class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity">View All</a>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full min-w-max">
              <thead>
                <tr class="text-left text-sm font-semibold text-gray-600 border-b-2 border-gray-100">
                  <th class="p-4">Order ID</th>
                  <th class="p-4">Date</th>
                  <th class="p-4">Status</th>
                  <th class="p-4">Total</th>
                  <th class="p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b-2 border-gray-100">
                  <td class="p-4 font-semibold text-primary">#EZ-2024-001</td>
                  <td class="p-4 text-gray-700">Oct 28, 2025</td>
                  <td class="p-4">
                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Delivered</span>
                  </td>
                  <td class="p-4 font-semibold text-gray-900">$862.91</td>
                  <td class="p-4">
                    <a href="#" class="px-4 py-2 border-2 border-gray-200 text-gray-700 text-center font-semibold rounded-xl text-sm transition-all duration-300 hover:border-primary hover:text-primary">
                      View Details
                    </a>
                  </td>
                </tr>
                <tr class="border-b-2 border-gray-100">
                  <td class="p-4 font-semibold text-primary">#EZ-2024-002</td>
                  <td class="p-4 text-gray-700">Oct 25, 2025</td>
                  <td class="p-4">
                    <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Processing</span>
                  </td>
                  <td class="p-4 font-semibold text-gray-900">$199.99</td>
                  <td class="p-4">
                    <a href="#" class="px-4 py-2 border-2 border-gray-200 text-gray-700 text-center font-semibold rounded-xl text-sm transition-all duration-300 hover:border-primary hover:text-primary">
                      View Details
                    </a>
                  </td>
                </tr>
                <tr class="border-b-2 border-gray-100">
                  <td class="p-4 font-semibold text-primary">#EZ-2024-003</td>
                  <td class="p-4 text-gray-700">Oct 22, 2025</td>
                  <td class="p-4">
                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Cancelled</span>
                  </td>
                  <td class="p-4 font-semibold text-gray-900">$29.99</td>
                  <td class="p-4">
                    <a href="#" class="px-4 py-2 border-2 border-gray-200 text-gray-700 text-center font-semibold rounded-xl text-sm transition-all duration-300 hover:border-primary hover:text-primary">
                      View Details
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>

  </div>
</div>
@endsection
