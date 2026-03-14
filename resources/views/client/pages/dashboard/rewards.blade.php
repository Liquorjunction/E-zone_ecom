@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Rewards')

@section('dashboard-content')
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">{{ statictranslations('Available Points') }}</p>
        <p class="text-5xl font-extrabold text-primary">1,250</p>
        <p class="text-gray-600 mt-1">{{ statictranslations('Valued at') }} <span class="font-semibold">$12.50</span></p>
      </div>
      <div class="w-20 h-20 rounded-full bg-primary/10 text-primary flex items-center justify-center">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-3">{{ statictranslations('How to Earn Points') }}</h3>
      <ul class="space-y-2 text-gray-700">
        <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>{{ statictranslations('Earn 1 point for every $1 spent') }}</li>
        <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>{{ statictranslations('Earn 50 points for writing a review') }}</li>
        <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>{{ statictranslations('Earn 100 points on your birthday') }}</li>
      </ul>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 mt-6">
    <div class="flex items-center justify-between p-6 border-b-2 border-gray-100">
      <h2 class="text-2xl font-bold text-gray-900 flex items-center">
        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
        {{ statictranslations('Rewards History') }}
      </h2>
      <a href="#" class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity">{{ statictranslations('Learn More') }}</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-max">
        <thead>
          <tr class="text-left text-sm font-semibold text-gray-600 border-b-2 border-gray-100">
            <th class="p-4">{{ statictranslations('Date') }}</th>
            <th class="p-4">{{ statictranslations('Activity') }}</th>
            <th class="p-4">{{ statictranslations('Points') }}</th>
            <th class="p-4">{{ statictranslations('Status') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 text-gray-700">Oct 28, 2025</td>
            <td class="p-4 font-medium text-gray-900">Order #EZ-2024-001</td>
            <td class="p-4 font-semibold text-green-600">+862</td>
            <td class="p-4">
              <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ statictranslations('Available') }}</span>
            </td>
          </tr>
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 text-gray-700">Oct 26, 2025</td>
            <td class="p-4 font-medium text-gray-900">{{ statictranslations('Redeemed for $5 Voucher') }}</td>
            <td class="p-4 font-semibold text-red-600">-500</td>
            <td class="p-4">
              <span class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">{{ statictranslations('Used') }}</span>
            </td>
          </tr>
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 text-gray-700">Oct 25, 2025</td>
            <td class="p-4 font-medium text-gray-900">Wrote a product review</td>
            <td class="p-4 font-semibold text-green-600">+50</td>
            <td class="p-4">
              <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Available</span>
            </td>
          </tr>
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 text-gray-700">Oct 22, 2025</td>
            <td class="p-4 font-medium text-gray-900">Sign Up Bonus</td>
            <td class="p-4 font-semibold text-green-600">+100</td>
            <td class="p-4">
              <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Available</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
@endsection
