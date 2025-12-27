@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'Dashboard')

@section('dashboard-content')
  @include('client.pages.dashboard._stats')

  <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100">
    <div class="flex items-center justify-between p-6 border-b-2 border-gray-100">
      <h2 class="text-2xl font-bold text-gray-900 ...">Recent Orders</h2>
      <a href="{{ route('dashboard.orders') }}" class="text-sm font-semibold text-primary ...">View All</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-max">
        <thead>
          <tr class="text-left text-sm font-semibold text-gray-600 ...">
            <th class="p-4">Order ID</th>
            <th class="p-4">Items</th>
            <th class="p-4">Total</th>
            <th class="p-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($orders) && $orders->count())
            @foreach($orders as $order)
              <tr class="border-b-2 border-gray-100">
                <td class="p-4 font-semibold text-primary">{{ $order->order_number ?? ('#'.$order->id) }}</td>
                <td class="p-4">
                  @php
                    // product summary: first product name + " + x more" if multiple
                    $firstItem = $order->items->first();
                    $itemsCount = $order->items->count();
                    $firstName = null;
                    if ($firstItem) {
                      $firstName = $firstItem->variant->name ?? $firstItem->product->name ?? 'Product';
                    }
                    $itemsLabel = $firstName ? ($itemsCount > 1 ? $firstName.' + '.($itemsCount - 1).' more' : $firstName) : ($itemsCount.' item'.($itemsCount>1?'s':''));
                    $status = strtolower($order->status ?? 'pending');
                    $statusClass = 'bg-gray-100 text-gray-700';
                    if($status === 'delivered') { $statusClass = 'bg-green-100 text-green-700'; }
                    elseif(in_array($status, ['processing','shipped'])) { $statusClass = 'bg-yellow-100 text-yellow-700'; }
                    elseif(in_array($status, ['cancelled','refunded'])) { $statusClass = 'bg-red-100 text-red-700'; }
                  @endphp
                  <div class="text-sm text-gray-800">{{ $itemsLabel }}</div>
                  <div class="mt-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($order->status ?? 'Pending') }}</span>
                  </div>
                </td>
                <td class="p-4 font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</td>
                <td class="p-4">
                  <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="px-4 py-2 border-2 border-gray-200 ...">View Details</a>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td class="p-4" colspan="5">You have no recent orders.</td>
            </tr>
          @endif
          </tbody>
      </table>
    </div>
  </div>
@endsection
