@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Orders')

@section('dashboard-content')
  <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100">
    <div class="p-4 lg:p-6 border-b-2 border-gray-100">
      {{-- Mobile Filter Dropdown --}}
      <div class="block lg:hidden">
        <form id="orders-filter-form" method="GET" action="{{ route('dashboard.orders') }}" onsubmit="return false;">
          <select name="status" class="w-full px-4 py-2 text-sm font-semibold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary">
            <option value="" {{ empty($currentStatus) ? 'selected' : '' }}>All Orders ({{ $orders->total() }})</option>
            <option value="processing" {{ $currentStatus === 'processing' ? 'selected' : '' }}>Processing ({{ $orders->total() && $orders->where('status','processing')->count() }})</option>
            <option value="shipped" {{ $currentStatus === 'shipped' ? 'selected' : '' }}>Shipped ({{ $orders->total() && $orders->where('status','shipped')->count() }})</option>
            <option value="delivered" {{ $currentStatus === 'delivered' ? 'selected' : '' }}>Delivered ({{ $orders->total() && $orders->where('status','delivered')->count() }})</option>
            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled ({{ $orders->total() && $orders->where('status','cancelled')->count() }})</option>
          </select>
        </form>
      </div>

      {{-- Desktop Filter Tabs --}}
      <div class="hidden lg:flex items-center flex-wrap gap-2">
        <a href="{{ route('dashboard.orders') }}" class="px-4 py-2 text-sm font-semibold {{ empty($currentStatus) ? 'text-white bg-primary' : 'text-gray-600 hover:text-primary hover:bg-primary/5' }} rounded-lg transition-colors">
          All Orders ({{ $orders->total() }})
        </a>
        <a href="{{ route('dashboard.orders', ['status' => 'processing']) }}" class="px-4 py-2 text-sm font-semibold {{ $currentStatus === 'processing' ? 'text-white bg-primary' : 'text-gray-600 hover:text-primary hover:bg-primary/5' }} rounded-lg transition-colors">
          Processing ({{ $orders->total() && $orders->where('status','processing')->count() }})
        </a>
        <a href="{{ route('dashboard.orders', ['status' => 'shipped']) }}" class="px-4 py-2 text-sm font-semibold {{ $currentStatus === 'shipped' ? 'text-white bg-primary' : 'text-gray-600 hover:text-primary hover:bg-primary/5' }} rounded-lg transition-colors">
          Shipped ({{ $orders->total() && $orders->where('status','shipped')->count() }})
        </a>
        <a href="{{ route('dashboard.orders', ['status' => 'delivered']) }}" class="px-4 py-2 text-sm font-semibold {{ $currentStatus === 'delivered' ? 'text-white bg-primary' : 'text-gray-600 hover:text-primary hover:bg-primary/5' }} rounded-lg transition-colors">
          Delivered ({{ $orders->total() && $orders->where('status','delivered')->count() }})
        </a>
        <a href="{{ route('dashboard.orders', ['status' => 'cancelled']) }}" class="px-4 py-2 text-sm font-semibold {{ $currentStatus === 'cancelled' ? 'text-white bg-primary' : 'text-gray-600 hover:text-primary hover:bg-primary/5' }} rounded-lg transition-colors">
          Cancelled ({{ $orders->total() && $orders->where('status','cancelled')->count() }})
        </a>
      </div>
    </div>

    <div id="orders-list-container">
      @include('client.pages.dashboard.partials.orders-list', ['orders' => $orders, 'currentStatus' => $currentStatus])
    </div>
    <script src="/js/orders-filter.js"></script>

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
