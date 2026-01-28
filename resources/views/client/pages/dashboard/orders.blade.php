@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Orders')

@section('dashboard-content')
  <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100">
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
