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
          @forelse($orders as $order)
          <tr class="border-b-2 border-gray-100">
            <td class="p-4 font-semibold text-primary">{{ $order->order_number ?? ('#'.$order->id) }}</td>
            <td class="p-4 text-gray-700">{{ optional($order->created_at)->format('M d, Y') }}</td>
            <td class="p-4">
              @php
                $status = strtolower($order->status ?? 'pending');
              @endphp
              @if($status === 'processing')
                <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Processing</span>
              @elseif($status === 'cancelled')
                <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Cancelled</span>
              @else
                <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ ucfirst($status) }}</span>
              @endif
            </td>
            <td class="p-4 font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</td>
            <td class="p-4">
              <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="px-4 py-2 border-2 border-gray-200 ...">View Details</a>
            </td>
          </tr>
          @empty
            <tr>
              <td class="p-4" colspan="5">You have no orders yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-6">
      <nav class="flex items-center justify-between" aria-label="Pagination">
        <div class="text-sm text-gray-600">
          Showing <span class="font-semibold text-gray-900">{{ $orders->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900">{{ $orders->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900">{{ $orders->total() ?? 0 }}</span> results
        </div>
        <div>
          {{ $orders->withQueryString()->links() }}
        </div>
      </nav>
    </div>
  </div>
@endsection
