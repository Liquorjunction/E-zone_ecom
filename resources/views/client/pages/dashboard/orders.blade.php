@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Orders')

@section('dashboard-content')
  <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100">
    <div class="p-6 border-b-2 border-gray-100">
      <div class="flex items-center space-x-4">
        <a href="#" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg">All Orders (12)</a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary">Processing (1)</a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary">Shipped (2)</a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary">Delivered (8)</a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary">Cancelled (1)</a>
      </div>
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
          @for($i = 1; $i <= 8; $i++)
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 font-semibold text-primary">#EZ-2024-00{{ $i }}</td>
            <td class="p-4 text-gray-700">Oct {{ 28 - $i }}, 2025</td>
            <td class="p-4">
              @if($i == 2)
                <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Processing</span>
              @elseif($i == 3)
                <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Cancelled</span>
              @else
                <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Delivered</span>
              @endif
            </td>
            <td class="p-4 font-semibold text-gray-900">${{ rand(29, 900) }}.99</td>
            <td class="p-4">
              <a href="#" class="px-4 py-2 border-2 border-gray-200 ...">View Details</a>
            </td>
          </tr>
          @endfor
        </tbody>
      </table>
    </div>
    <div class="p-6">
      <nav class="flex items-center justify-between" aria-label="Pagination">
        <div class="text-sm text-gray-600">
          Showing <span class="font-semibold text-gray-900">1</span> to <span class="font-semibold text-gray-900">8</span> of <span class="font-semibold text-gray-900">12</span> results
        </div>
        <div class="flex items-center space-x-1">
          <a href="#" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
          </a>
          <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-primary bg-primary text-white font-semibold">1</a>
          <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50">2</a>
          <a href="#" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </a>
        </div>
      </nav>
    </div>
  </div>
@endsection
