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
                        $canCancel = in_array($status, ['pending'], true);
                    @endphp
                    <div class="flex items-center border-b-2 border-gray-100 hover:bg-gray-50 transition-colors py-4 px-6">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 text-base mb-1">{{ $itemsLabel }}</div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                                <span
                                    class="font-semibold text-primary">{{ $order->order_number ?? '#' . $order->id }}</span>
                                <span>{{ optional($order->created_at)->format('M d, Y') }}</span>
                                @if ($status === 'processing')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">{{ statictranslations('Processing') }}</span>
                                @elseif($status === 'cancelled')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">{{ statictranslations('Cancelled') }}</span>
                                @elseif($status === 'shipped')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">{{ statictranslations('Shipped') }}</span>
                                @elseif($status === 'delivered')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ statictranslations('Delivered') }}</span>
                                @elseif($status === 'returned')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">{{ statictranslations('Returned') }}</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ statictranslations(ucfirst($status)) }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</span>
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary transition-colors">{{ statictranslations('View Details') }}</a>
                            @if (Auth::check() && $order->user_id === Auth::id() && $canCancel)
                                <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}"
                                    onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">{{ statictranslations('Cancel') }}</button>
                                </form>
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
                                $canCancel = in_array($status, ['pending'], true);
                            @endphp
                            @if ($status === 'processing')
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">{{ statictranslations('Processing') }}</span>
                            @elseif($status === 'cancelled')
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">{{ statictranslations('Cancelled') }}</span>
                            @elseif($status === 'shipped')
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">{{ statictranslations('Shipped') }}</span>
                            @elseif($status === 'delivered')
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ statictranslations('Delivered') }}</span>
                            @elseif($status === 'returned')
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">{{ statictranslations('Returned') }}</span>
                            @else
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ ucfirst($status) }}</span>
                            @endif
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
                                    <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}"
                                        onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">{{ statictranslations('Cancel') }}</button>
                                    </form>
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
@endsection
