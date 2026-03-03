<div class="mb-6">
    <form id="orders-filter-form" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 lg:p-5">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 lg:max-w-md">
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-2">
                        Search Orders
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                            placeholder="Order number, product name..."
                            value="{{ request('search', '') }}"
                            autocomplete="off">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if(request('search'))
                            <button
                                type="button"
                                id="clear-search-btn"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                aria-label="Clear search">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 lg:gap-3">
                    <div class="flex-1 sm:w-44">
                        <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">
                            Status
                        </label>
                        <div class="relative">
                            <select
                                name="status"
                                id="status"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 bg-white cursor-pointer">
                                <option value="">All Statuses</option>
                                <option value="pending" @if (request('status') == 'pending') selected @endif>Pending</option>
                                <option value="order_confirmation" @if (request('status') == 'order_confirmation') selected @endif>Order Confirmation</option>
                                <option value="shipped" @if (request('status') == 'shipped') selected @endif>Shipped</option>
                                <option value="out_for_delivery" @if (request('status') == 'out_for_delivery') selected @endif>Out For Delivery</option>
                                <option value="delivered" @if (request('status') == 'delivered') selected @endif>Delivered</option>
                                <option value="cancelled" @if (request('status') == 'cancelled') selected @endif>Cancelled</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 sm:w-44">
                        <label for="sort" class="block text-xs font-semibold text-gray-700 mb-2">
                            Sort By
                        </label>
                        <div class="relative">
                            <select
                                name="sort"
                                id="sort"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 bg-white cursor-pointer">
                                <option value="latest" @if (request('sort', 'latest') == 'latest') selected @endif>Newest First</option>
                                <option value="oldest" @if (request('sort') == 'oldest') selected @endif>Oldest First</option>
                                <option value="amount_high" @if (request('sort') == 'amount_high') selected @endif>Highest Amount</option>
                                <option value="amount_low" @if (request('sort') == 'amount_low') selected @endif>Lowest Amount</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button
                    type="button"
                    id="toggle-advanced-filters"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors"
                    aria-expanded="{{ request('date_from') || request('date_to') ? 'true' : 'false' }}"
                    aria-controls="advanced-filters">
                    <svg class="w-4 h-4 transition-transform duration-200" id="filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transform: {{ request('date_from') || request('date_to') ? 'rotate(45deg)' : 'rotate(0deg)' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span id="filter-toggle-text">{{ request('date_from') || request('date_to') ? 'Hide Advanced Filters' : 'Advanced Filters' }}</span>
                    @php
                        $hasDateFilters = request('date_from') || request('date_to');
                        $dateFilterCount = (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0);
                    @endphp
                    @if($hasDateFilters)
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary rounded-full">
                            {{ $dateFilterCount }}
                        </span>
                    @endif
                </button>
            </div>
            <div
                id="advanced-filters"
                class="mt-4 pt-4 border-t border-gray-200 {{ $hasDateFilters ? '' : 'hidden' }}"
                style="{{ $hasDateFilters ? 'display: block;' : 'display: none;' }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="date_from" class="block text-xs font-semibold text-gray-700 mb-2">
                            Date From
                        </label>
                        <div class="relative">
                            <input
                                type="date"
                                name="date_from"
                                id="date_from"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                value="{{ request('date_from', '') }}">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-semibold text-gray-700 mb-2">
                            Date To
                        </label>
                        <div class="relative">
                            <input
                                type="date"
                                name="date_to"
                                id="date_to"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                value="{{ request('date_to', '') }}">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $hasFilters = request('search') || request('status') || request('date_from') || request('date_to') || (request('sort', 'latest') !== 'latest');
            $totalFilterCount =
                (request('search') ? 1 : 0) +
                (request('status') ? 1 : 0) +
                (request('date_from') ? 1 : 0) +
                (request('date_to') ? 1 : 0) +
                ((request('sort', 'latest') !== 'latest') ? 1 : 0);
        @endphp
        <div class="bg-gray-50 px-4 lg:px-5 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                @if($hasFilters)
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="font-medium">
                        {{ $totalFilterCount }} filter{{ $totalFilterCount !== 1 ? 's' : '' }} active
                    </span>
                @else
                    <span>No filters applied</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($hasFilters)
                    <a
                        href="{{ url()->current() }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All
                    </a>
                @endif
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 focus:ring-4 focus:ring-primary/20 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<div id="orders-list-container">
    <div class="hidden lg:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @forelse($orders as $order)
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
                $statusLabel = [
                    'pending' => 'Pending',
                    'order_confirmation' => 'Order Confirmation',
                    'partially_dispatched' => 'Order Confirmation',
                    'dispatched' => 'Order Confirmation',
                    'ready_to_ship' => 'Order Confirmation',
                    'shipped' => 'Shipped',
                    'out_for_delivery' => 'Out For Delivery',
                    'delivered' => 'Delivered',
                    'completed' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ][$status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $status));
                $statusStyles = [
                    'pending' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'order_confirmation' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'partially_dispatched' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'dispatched' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'ready_to_ship' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'shipped' => ['text-amber-700 bg-amber-100', 'bg-amber-600'],
                    'out_for_delivery' => ['text-indigo-700 bg-indigo-100', 'bg-indigo-600'],
                    'delivered' => ['text-green-700 bg-green-100', 'bg-green-600'],
                    'completed' => ['text-green-700 bg-green-100', 'bg-green-600'],
                    'cancelled' => ['text-red-700 bg-red-100', 'bg-red-600'],
                ][$status] ?? ['text-gray-700 bg-gray-100', 'bg-gray-600'];
            @endphp
            <div class="flex items-center border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-all duration-200 p-4">
                <div class="flex-1 min-w-0 pr-4">
                    <div class="font-semibold text-gray-900 text-base mb-2 truncate">{{ $itemsLabel }}</div>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="inline-flex items-center gap-1.5 font-semibold text-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ $order->order_number ?? '#' . $order->id }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-gray-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ optional($order->created_at)->format('M d, Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusStyles[0] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusStyles[1] }}"></span>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-bold text-gray-900 text-lg">{{ money_format_number($order->total ?? 0) }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-primary transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Details
                        </a>
                        @if (Auth::check() &&
                            $order->user_id === Auth::id() &&
                            in_array($status, ['pending'], true))
                            <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all duration-200 open-cancel-modal-btn"
                                data-order-id="{{ $order->id }}"
                                data-order-number="{{ $order->order_number ?? '#' . $order->id }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-gray-100 rounded-full">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                @if($hasFilters)
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters to see more results.</p>
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </a>
                @else
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-gray-600">Your order history will appear here once you make a purchase.</p>
                @endif
            </div>
        @endforelse
    </div>
    <div class="block lg:hidden space-y-3">
        @forelse($orders as $order)
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
                $status = strtolower($order->status ?? 'pending');
                $statusLabel = [
                    'pending' => 'Pending',
                    'order_confirmation' => 'Order Confirmation',
                    'partially_dispatched' => 'Order Confirmation',
                    'dispatched' => 'Order Confirmation',
                    'ready_to_ship' => 'Order Confirmation',
                    'shipped' => 'Shipped',
                    'out_for_delivery' => 'Out For Delivery',
                    'delivered' => 'Delivered',
                    'completed' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ][$status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $status));
                $statusStyles = [
                    'pending' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'order_confirmation' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'partially_dispatched' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'dispatched' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'ready_to_ship' => ['text-yellow-700 bg-yellow-100', 'bg-yellow-600'],
                    'shipped' => ['text-amber-700 bg-amber-100', 'bg-amber-600'],
                    'out_for_delivery' => ['text-indigo-700 bg-indigo-100', 'bg-indigo-600'],
                    'delivered' => ['text-green-700 bg-green-100', 'bg-green-600'],
                    'completed' => ['text-green-700 bg-green-100', 'bg-green-600'],
                    'cancelled' => ['text-red-700 bg-red-100', 'bg-red-600'],
                ][$status] ?? ['text-gray-700 bg-gray-100', 'bg-gray-600'];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-3 pb-3 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-bold text-primary">{{ $order->order_number ?? '#' . $order->id }}</p>
                        <p class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ optional($order->created_at)->format('M d, Y') }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusStyles[0] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusStyles[1] }}"></span>
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="mb-4">
                    <div class="text-sm text-gray-800 font-medium mb-2">{{ $itemsLabel }}</div>
                    <div class="text-xl font-bold text-gray-900">{{ money_format_number($order->total ?? 0) }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View
                    </a>
                        @if (Auth::check() &&
                            $order->user_id === Auth::id() &&
                            in_array($status, ['pending'], true))
                        <button type="button"
                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition-all duration-200 open-cancel-modal-btn"
                            data-order-id="{{ $order->id }}"
                            data-order-number="{{ $order->order_number ?? '#' . $order->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-gray-100 rounded-full">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                @if($hasFilters)
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters to see more results.</p>
                    <a href="{{ url()->current() }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </a>
                @else
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-gray-600">Your order history will appear here.</p>
                @endif
            </div>
        @endforelse
    </div>
    @if ($orders->hasPages())
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <nav class="flex flex-col sm:flex-row items-center justify-between gap-4" aria-label="Pagination">
                <div class="text-sm text-gray-600 text-center sm:text-left">
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

<!-- Cancel Order Modal (moved to body by JS) -->
<div id="cancel-order-modal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 close-cancel-modal-btn">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <form id="cancel-order-form" method="POST" action="{{ route('client.orders.cancel') }}">
            @csrf
            <input type="hidden" name="id" id="cancel-order-id" value="">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancel Order <span id="cancel-order-number" class="text-primary"></span></h3>
            <p class="text-gray-700 mb-4">Please provide a reason for cancellation. This action cannot be undone.</p>
            <div class="mb-4">
                <label for="cancel-reason" class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                <textarea name="reason" id="cancel-reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg close-cancel-modal-btn">Close</button>
                <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all duration-200">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/orders-filter.js') }}"></script>
    <script src="{{ asset('js/order-cancel-popup.js') }}"></script>
@endpush

