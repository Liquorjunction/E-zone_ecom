{{-- Orders List Partial for AJAX --}}
@push('scripts')
    <script src="{{ asset('js/orders-filter.js') }}"></script>
@endpush
{{-- Orders Search/Sort/Filter Bar --}}
<div class="mb-6">
    <form id="orders-filter-form" class="flex flex-col md:flex-row md:items-end gap-3 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex-1 flex flex-col gap-1">
            <label for="search" class="text-xs font-semibold text-gray-600 mb-1">Search</label>
            <div class="relative">
                <input type="text" name="search" id="search" class="border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-primary focus:border-primary w-full" placeholder="Order number, product, etc..." value="{{ request('search', '') }}">
                <span class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 1010 17a7 7 0 007-7z"/></svg>
                </span>
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <label for="status" class="text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" id="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary w-40">
                <option value="">All Statuses</option>
                <option value="processing" @if(request('status')=='processing') selected @endif>Processing</option>
                <option value="shipped" @if(request('status')=='shipped') selected @endif>Shipped</option>
                <option value="delivered" @if(request('status')=='delivered') selected @endif>Delivered</option>
                <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label for="date_from" class="text-xs font-semibold text-gray-600 mb-1">Date From</label>
            <input type="date" name="date_from" id="date_from" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary w-36" value="{{ request('date_from', '') }}">
        </div>
        <div class="flex flex-col gap-1">
            <label for="date_to" class="text-xs font-semibold text-gray-600 mb-1">Date To</label>
            <input type="date" name="date_to" id="date_to" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary w-36" value="{{ request('date_to', '') }}">
        </div>
        <div class="flex flex-col gap-1">
            <label for="sort" class="text-xs font-semibold text-gray-600 mb-1">Sort By</label>
            <select name="sort" id="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary w-40">
                <option value="latest" @if(request('sort','latest')=='latest') selected @endif>Newest First</option>
                <option value="oldest" @if(request('sort')=='oldest') selected @endif>Oldest First</option>
                <option value="amount_high" @if(request('sort')=='amount_high') selected @endif>Amount High-Low</option>
                <option value="amount_low" @if(request('sort')=='amount_low') selected @endif>Amount Low-High</option>
            </select>
        </div>
        <div class="flex flex-col justify-end">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors mt-1">Apply</button>
        </div>
    </form>
</div>
<div id="orders-list-container">
<div class="hidden lg:block">
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
        @endphp
        <div class="flex items-center border-b-2 border-gray-100 hover:bg-gray-50 transition-colors py-4 px-2">
            <div class="flex-1 min-w-0">
                <div class="font-bold text-gray-900 text-base mb-1">{{ $itemsLabel }}</div>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                    <span class="font-semibold text-primary">{{ $order->order_number ?? '#' . $order->id }}</span>
                    <span>{{ optional($order->created_at)->format('M d, Y') }}</span>
                    @if ($status === 'processing')
                        <span
                            class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Processing</span>
                    @elseif($status === 'cancelled')
                        <span
                            class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Cancelled</span>
                    @else
                        <span
                            class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">{{ ucfirst($status) }}</span>
                    @endif
                </div>
            </div>
            <span class="font-semibold text-gray-900">{{ money_format_number($order->total ?? 0) }}</span>
            <div class="flex items-center gap-2 ml-4">
                <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    View Details
                </a>
                @if (Auth::check() &&
                        $order->user_id === Auth::id() &&
                        !in_array(strtolower($order->status ?? ''), ['cancelled', 'shipped', 'delivered']))
                    <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}"
                        onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="text-gray-500 font-medium">You have no orders yet.</p>
        </div>
    @endforelse
    </div>
{{-- Mobile Card View --}}
    <div class="block lg:hidden">
    @forelse($orders as $order)
        <div class="p-4 border-b-2 border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm font-semibold text-primary">{{ $order->order_number ?? '#' . $order->id }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ optional($order->created_at)->format('M d, Y') }}</p>
                </div>
                @php $status = strtolower($order->status ?? 'pending'); @endphp
                @if ($status === 'processing')
                    <span
                        class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Processing</span>
                @elseif($status === 'cancelled')
                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Cancelled</span>
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
                    <div class="text-lg font-bold text-gray-900 mt-1">{{ money_format_number($order->total ?? 0) }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">
                        View Details
                    </a>
                    @if (Auth::check() &&
                            $order->user_id === Auth::id() &&
                            !in_array(strtolower($order->status ?? ''), ['cancelled', 'shipped', 'delivered']))
                        <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            <button type="submit"
                                class="px-3 py-2 text-sm font-medium text-red-700 bg-white border-2 border-red-200 rounded-lg hover:bg-red-50 transition-colors">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="text-gray-500 font-medium">You have no orders yet.</p>
        </div>
    @endforelse
</div>
