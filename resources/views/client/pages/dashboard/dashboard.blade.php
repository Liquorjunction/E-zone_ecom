@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('Dashboard'))

@section('dashboard-content')
    @include('client.pages.dashboard._stats')

    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100">
        <div class="flex items-center justify-between p-6 border-b-2 border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900">{{ statictranslations('Recent Orders') }}</h2>
            <a href="{{ route('dashboard.orders') }}"
                class="text-sm font-semibold text-primary">{{ statictranslations('View All') }}</a>
        </div>
        <div class="hidden lg:block">
            @if (!empty($orders) && $orders->count())
                @foreach ($orders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $productName = $firstItem->product->name ?? null;
                        $variantName = $firstItem->variant->variant_name ?? null;
                        $title =
                            $productName && $variantName
                                ? $productName . ' - ' . $variantName
                                : $productName ?? ($variantName ?? 'Product');
                        $itemsCount = $order->items->count();
                        $itemsLabel = $itemsCount > 1 ? $title . ' + ' . ($itemsCount - 1) . ' more' : $title;
                        $status = strtolower($order->status ?? 'pending');
                        $canCancel = in_array($status, ['pending', 'paid'], true);
                        $statusLabel = match ($status) {
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'order_confirmation', 'partially_dispatched', 'dispatched', 'ready_to_ship' => 'Order Confirmation',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'out_for_delivery' => 'Out for Delivery',
                            'delivered', 'completed' => 'Delivered',
                            'cancelled' => 'Cancelled',
                            'returned' => 'Returned',
                            'refunded' => 'Refunded',
                            'failed_delivery' => 'Failed Delivery',
                            default => ucwords(str_replace('_', ' ', $status)),
                        };
                        $statusClass = match ($status) {
                            'pending' => 'text-yellow-700 bg-yellow-100',
                            'paid' => 'text-emerald-700 bg-emerald-100',
                            'order_confirmation', 'partially_dispatched', 'dispatched', 'ready_to_ship' => 'text-yellow-700 bg-yellow-100',
                            'processing' => 'text-blue-700 bg-blue-100',
                            'shipped' => 'text-amber-700 bg-amber-100',
                            'out_for_delivery' => 'text-indigo-700 bg-indigo-100',
                            'delivered', 'completed' => 'text-green-700 bg-green-100',
                            'cancelled' => 'text-red-700 bg-red-100',
                            'returned' => 'text-purple-700 bg-purple-100',
                            'refunded' => 'text-rose-700 bg-rose-100',
                            default => 'text-gray-700 bg-gray-100',
                        };
                    @endphp
                    <div class="flex items-center border-b-2 border-gray-100 hover:bg-gray-50 transition-colors py-4 px-6">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 text-base mb-1">{{ $itemsLabel }}</div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                                <span
                                    class="font-semibold text-primary">{{ $order->order_number ?? '#' . $order->id }}</span>
                                <span>{{ optional($order->created_at)->format('M d, Y') }}</span>
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ statictranslations($statusLabel) }}</span>
                            </div>
                        </div>
                        <span class="font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</span>
                        <div class="flex items-center gap-2 ms-4">
                            <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary transition-colors">{{ statictranslations('View Details') }}</a>
                            @if (Auth::check() && $order->user_id === Auth::id() && $canCancel)
                                <button type="button"
                                    class="px-3 py-2 text-sm font-medium cursor-pointer text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-gray-50 hover:border-red-300 hover:text-red-600 transition-colors open-cancel-modal-btn"
                                    data-order-id="{{ $order->id }}"
                                    data-order-number="{{ $order->order_number ?? '#' . $order->id }}">
                                    {{ statictranslations('Cancel') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-gray-500 font-medium">{{ statictranslations('You have no recent orders.') }}</p>
                </div>
            @endif
        </div>
        <div class="block lg:hidden">
            @if (!empty($orders) && $orders->count())
                @foreach ($orders as $order)
                    <div class="p-4 border-b-2 border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-sm font-semibold text-primary">
                                    {{ $order->order_number ?? '#' . $order->id }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">{{ optional($order->created_at)->format('M d, Y') }}
                                </p>
                            </div>
                            @php
                                $status = strtolower($order->status ?? 'pending');
                                $canCancel = in_array($status, ['pending', 'paid'], true);
                                $statusLabel = match ($status) {
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'order_confirmation', 'partially_dispatched', 'dispatched', 'ready_to_ship' => 'Order Confirmation',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'out_for_delivery' => 'Out for Delivery',
                                    'delivered', 'completed' => 'Delivered',
                                    'cancelled' => 'Cancelled',
                                    'returned' => 'Returned',
                                    'refunded' => 'Refunded',
                                    'failed_delivery' => 'Failed Delivery',
                                    default => ucwords(str_replace('_', ' ', $status)),
                                };
                                $statusClass = match ($status) {
                                    'pending' => 'text-yellow-700 bg-yellow-100',
                                    'paid' => 'text-emerald-700 bg-emerald-100',
                                    'order_confirmation', 'partially_dispatched', 'dispatched', 'ready_to_ship' => 'text-yellow-700 bg-yellow-100',
                                    'processing' => 'text-blue-700 bg-blue-100',
                                    'shipped' => 'text-amber-700 bg-amber-100',
                                    'out_for_delivery' => 'text-indigo-700 bg-indigo-100',
                                    'delivered', 'completed' => 'text-green-700 bg-green-100',
                                    'cancelled' => 'text-red-700 bg-red-100',
                                    'returned' => 'text-purple-700 bg-purple-100',
                                    'refunded' => 'text-rose-700 bg-rose-100',
                                    default => 'text-gray-700 bg-gray-100',
                                };
                            @endphp
                            <span
                                class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ statictranslations($statusLabel) }}</span>
                        </div>
                        @php
                            $firstItem = $order->items->first();
                            $itemsCount = $order->items->count();
                            $firstName = null;
                            if ($firstItem) {
                                $firstName = $firstItem->variant->name ?? ($firstItem->product->name ?? 'Product');
                            }
                            $itemsLabel = $firstName
                                ? ($itemsCount > 1
                                    ? $firstName . ' + ' . ($itemsCount - 1) . ' more'
                                    : $firstName)
                                : $itemsCount . ' item' . ($itemsCount > 1 ? 's' : '');
                        @endphp
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-800 font-medium">{{ $itemsLabel }}</div>
                                <div class="text-lg font-bold text-gray-900 mt-1">
                                    {{ money_format_number($order->total ?? 0) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">{{ statictranslations('View Details') }}</a>
                                @if (Auth::check() && $order->user_id === Auth::id() && $canCancel)
                                    <button type="button"
                                        class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors open-cancel-modal-btn"
                                        data-order-id="{{ $order->id }}"
                                        data-order-number="{{ $order->order_number ?? '#' . $order->id }}">
                                        {{ statictranslations('Cancel') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-gray-500 font-medium">{{ statictranslations('You have no recent orders.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Order Modal (moved to body by JS) -->
    <div id="cancel-order-modal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
            <button type="button"
                class="absolute top-3 end-3 text-gray-400 hover:text-gray-600 close-cancel-modal-btn">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <form id="cancel-order-form" method="POST" action="{{ route('client.orders.cancel') }}">
                @csrf
                <input type="hidden" name="id" id="cancel-order-id" value="">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ statictranslations('Cancel Order') }} <span
                        id="cancel-order-number" class="text-primary"></span></h3>
                <p class="text-gray-700 mb-4">{{ statictranslations('Please provide a reason for cancellation. This action cannot be undone.') }}</p>
                <div class="mb-4">
                    <label for="cancel-reason" class="block text-sm font-medium text-gray-700 mb-1">{{ statictranslations('Reason') }} <span
                            class="text-red-500">*</span></label>
                    <textarea name="reason" id="cancel-reason" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg close-cancel-modal-btn transition-all duration-200 hover:bg-gray-200 cursor-pointer">{{ statictranslations('Close') }}</button>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all duration-200 cursor-pointer">{{ statictranslations('Confirm Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/order-cancel-popup.js') }}"></script>
@endpush
