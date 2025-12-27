@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Orders')

@section('dashboard-content')
  <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100">
    <div class="p-4 lg:p-6 border-b-2 border-gray-100">
      {{-- Mobile Filter Dropdown --}}
      <div class="block lg:hidden">
        <select class="w-full px-4 py-2 text-sm font-semibold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary">
          <option value="">All Orders (12)</option>
          <option value="processing">Processing (1)</option>
          <option value="shipped">Shipped (2)</option>
          <option value="delivered">Delivered (8)</option>
          <option value="cancelled">Cancelled (1)</option>
        </select>
      </div>

      {{-- Desktop Filter Tabs --}}
      <div class="hidden lg:flex items-center flex-wrap gap-2">
        <a href="#" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
          All Orders (12)
        </a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
          Processing (1)
        </a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
          Shipped (2)
        </a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
          Delivered (8)
        </a>
        <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
          Cancelled (1)
        </a>
      </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="block lg:hidden">
      @forelse($orders as $order)
        <div class="p-4 border-b-2 border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="text-sm font-semibold text-primary">{{ $order->order_number ?? ('#'.$order->id) }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ optional($order->created_at)->format('M d, Y') }}</p>
            </div>
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
          </div>
          @php
            $firstItem = $order->items->first();
            $itemsCount = $order->items->count();
            $firstName = null;
            if ($firstItem) {
              $firstName = $firstItem->variant->name ?? $firstItem->product->name ?? 'Product';
            }
            $itemsLabel = $firstName ? ($itemsCount > 1 ? $firstName.' + '.($itemsCount - 1).' more' : $firstName) : ($itemsCount.' item'.($itemsCount>1?'s':''));
          @endphp
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-800 font-medium">{{ $itemsLabel }}</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ money_format_number($order->total ?? 0) }}</div>
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">
                View Details
              </a>
              @if(Auth::check() && $order->user_id === Auth::id() && !in_array(strtolower($order->status ?? ''), ['cancelled','shipped','delivered']))
                <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                  @csrf
                  <button type="submit" class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    Cancel
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="p-8 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <p class="text-gray-500 font-medium">You have no orders yet.</p>
          <a href="#" class="inline-block mt-3 text-sm text-primary font-semibold hover:text-primary/80">
            Start Shopping
          </a>
        </div>
      @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full min-w-max">
        <thead>
          <tr class="text-left text-sm font-semibold text-gray-600 border-b-2 border-gray-100">
            <th class="p-4">Order ID</th>
            <th class="p-4">Date</th>
            <th class="p-4">Status</th>
            <th class="p-4">Items</th>
            <th class="p-4">Total</th>
            <th class="p-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
          <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
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
            @php
              $firstItem = $order->items->first();
              $itemsCount = $order->items->count();
              $firstName = null;
              if ($firstItem) {
                $firstName = $firstItem->variant->name ?? $firstItem->product->name ?? 'Product';
              }
              $itemsLabel = $firstName ? ($itemsCount > 1 ? $firstName.' + '.($itemsCount - 1).' more' : $firstName) : ($itemsCount.' item'.($itemsCount>1?'s':''));
            @endphp
            <td class="p-4 text-gray-700">{{ $itemsLabel }}</td>
            <td class="p-4 font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</td>
            <td class="p-4">
              <div class="flex items-center gap-2">
                <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">
                  View Details
                </a>
                @if(Auth::check() && $order->user_id === Auth::id() && !in_array(strtolower($order->status ?? ''), ['cancelled','shipped','delivered']))
                  <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                      Cancel
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
            <tr>
              <td class="p-8 text-center" colspan="5">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-gray-500 font-medium">You have no orders yet.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
      <div class="p-4 lg:p-6 border-t-2 border-gray-100">
        <nav class="flex flex-col sm:flex-row items-center justify-between gap-4" aria-label="Pagination">
          <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
            Showing <span class="font-semibold text-gray-900">{{ $orders->firstItem() ?? 0 }}</span> to
            <span class="font-semibold text-gray-900">{{ $orders->lastItem() ?? 0 }}</span> of
            <span class="font-semibold text-gray-900">{{ $orders->total() ?? 0 }}</span> results
          </div>
          <div class="w-full sm:w-auto">
            {{ $orders->withQueryString()->links() }}
          </div>
        </nav>
      </div>
    @endif
  </div>
@endsection
