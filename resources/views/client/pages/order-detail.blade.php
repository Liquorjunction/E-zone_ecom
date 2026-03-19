@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Order Details') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('Track your order and view details') }}</x-slot:content>
            </x-breadcrumbs>

            {{-- Order Header --}}
            @php
                $status = strtolower($order->status ?? 'pending');
                $statusClass = match ($status) {
                    'delivered', 'completed' => 'text-green-700 bg-green-100',
                    'shipped', 'out_for_delivery' => 'text-primary bg-primary/10',
                    'pending', 'order_confirmation' => 'text-yellow-700 bg-yellow-100',
                    'partially_dispatched', 'dispatched', 'ready_to_ship' => 'text-yellow-700 bg-yellow-100',
                    'cancelled' => 'text-red-700 bg-red-100',
                    default => 'text-gray-700 bg-gray-100',
                };
                $clientStatus = in_array($status, ['partially_dispatched', 'dispatched', 'ready_to_ship'], true)
                    ? 'order_confirmation'
                    : $status;
                if ($clientStatus === 'completed') {
                    $clientStatus = 'delivered';
                }
                // Use OrderStatusService for translated status label if available
                $statusLabel = isset($orderStatusService)
                    ? $orderStatusService->getTranslatedStatus($clientStatus)
                    : \Illuminate\Support\Str::title(str_replace('_', ' ', $clientStatus));
                $stages = ['pending', 'order_confirmation', 'shipped', 'out_for_delivery', 'delivered'];
                $stageLabels = [
                    'pending' => 'Pending',
                    'order_confirmation' => 'Order Confirmation',
                    'shipped' => 'Shipped',
                    'out_for_delivery' => 'Out For Delivery',
                    'delivered' => 'Delivered',
                ];
                $cancelableStatuses = ['pending'];
                $currentIndex = array_search($clientStatus, $stages, true);
                if ($currentIndex === false) {
                    $currentIndex = 0;
                }
                $progressPct = (($currentIndex + 1) / count($stages)) * 100;
            @endphp

            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ statictranslations('Order') }}
                                #{{ $order->order_number ?? $order->id }}</h1>
                            <span class="px-3 py-1.5 text-xs lg:text-sm font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ statictranslations('Placed on') }}
                            {{ optional($order->created_at)->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($status === 'delivered' || $status === 'completed')
                            <a href="{{ route('client.orders.invoice.download', ['id' => $order->id]) ?? '#' }}"
                                class="px-4 py-2.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                <span class="flex items-center gap-2">
                                    <i class="fa fa-download w-4 h-4"></i>
                                    {{ statictranslations('Download Invoice') }}
                                </span>
                            </a>
                            <a href="{{ route('client.orders.invoice', ['id' => $order->id]) ?? '#' }}"
                                class="px-4 py-2.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                <span class="flex items-center gap-2">
                                    <i class="fa fa-eye w-4 h-4"></i>
                                    {{ statictranslations('View Invoice') }}
                                </span>
                            </a>
                        @endif
                        @if (Auth::check() && $order->user_id === Auth::id() && in_array($status, $cancelableStatuses, true))
                            <button type="button"
                                class="px-4 py-2.5 border-2 border-red-200 text-red-700 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm open-cancel-modal-btn"
                                data-order-id="{{ $order->id }}"
                                data-order-number="{{ $order->order_number ?? '#' . $order->id }}">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ statictranslations('Cancel Order') }}
                                </span>
                            </button>
                        @endif
                        <!-- Cancel Order Modal (moved to body by JS) -->
                        <div id="cancel-order-modal"
                            class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-40 hidden">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                                <button type="button"
                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 close-cancel-modal-btn">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <form id="cancel-order-form" method="POST" action="{{ route('client.orders.cancel') }}">
                                    @csrf
                                    <input type="hidden" name="id" id="cancel-order-id" value="{{ $order->id }}">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ statictranslations('Cancel Order') }} <span id="cancel-order-number"
                                            class="text-primary">{{ $order->order_number ?? '#' . $order->id }}</span></h3>
                                    <p class="text-gray-700 mb-4">
                                        {{ statictranslations('Please provide a reason for cancellation. This action cannot be undone.') }}
                                    </p>
                                    <div class="mb-4">
                                        <label for="cancel-reason"
                                            class="block text-sm font-medium text-gray-700 mb-1">{{ statictranslations('Reason') }}
                                            <span class="text-red-500">*</span></label>
                                        <textarea name="reason" id="cancel-reason" rows="3" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg close-cancel-modal-btn">{{ statictranslations('Close') }}</button>
                                        <button type="submit"
                                            class="px-6 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all duration-200">{{ statictranslations('Confirm Cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-4 lg:space-y-6">

                    {{-- Order Tracking --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
                            {{ statictranslations('Order Tracking') }}
                        </h2>

                        @if ($status === 'cancelled')
                            @php
                                $cancelledAt = $order->cancelled_at ?? $order->updated_at;
                            @endphp
                            <div class="mt-2 rounded-xl border-2 border-red-100 bg-red-50 p-5 lg:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base lg:text-lg font-bold text-red-900">
                                            {{ statictranslations('Order Cancelled') }}</p>
                                        @if ($cancelledAt)
                                            <p class="text-sm text-red-700 mt-1">
                                                {{ statictranslations('Cancelled on') }}
                                                {{ optional($cancelledAt)->format('M j, Y \a\t g:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Desktop Timeline: horizontal stepper --}}
                            <div class="hidden md:block">
                                <div class="relative py-6">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t-2 border-gray-200"></div>
                                    </div>

                                    <div class="relative flex justify-between items-start">
                                        @foreach ($stages as $idx => $stage)
                                            @php
                                                $isActive = $idx <= $currentIndex;
                                                $label = isset($orderStatusService)
                                                    ? $orderStatusService->getTranslatedStatus($stage)
                                                    : $stageLabels[$stage] ??
                                                        \Illuminate\Support\Str::title(str_replace('_', ' ', $stage));
                                                $tsField = $stage . '_at';
                                                $ts = $order->{$tsField} ?? null;
                                                if (!$ts) {
                                                    $ts =
                                                        $stage === 'pending'
                                                            ? $order->created_at
                                                            : ($isActive
                                                                ? $order->updated_at
                                                                : null);
                                                }
                                            @endphp

                                            <div class="flex-1 min-w-0 text-center px-2">
                                                <div
                                                    class="mx-auto w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ $isActive ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
                                                    @if ($isActive)
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @else
                                                        <span class="text-sm font-semibold">{{ $idx + 1 }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs font-semibold text-gray-900">{{ $label }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @if ($ts)
                                                        {{ optional($ts)->format('M j, g:i A') }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Timeline: stacked steps --}}
                            <div class="md:hidden space-y-4">
                                @foreach ($stages as $idx => $stage)
                                    @php
                                        $isActive = $idx <= $currentIndex;
                                        $label = isset($orderStatusService)
                                            ? $orderStatusService->getTranslatedStatus($stage)
                                            : $stageLabels[$stage] ??
                                                \Illuminate\Support\Str::title(str_replace('_', ' ', $stage));
                                        $tsField = $stage . '_at';
                                        $ts = $order->{$tsField} ?? null;
                                        if (!$ts) {
                                            $ts =
                                                $stage === 'pending'
                                                    ? $order->created_at
                                                    : ($isActive
                                                        ? $order->updated_at
                                                        : null);
                                        }
                                    @endphp
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center w-12 shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-full flex items-center justify-center {{ $isActive ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500' }}">
                                                @if ($isActive)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    <span class="text-sm font-semibold">{{ $idx + 1 }}</span>
                                                @endif
                                            </div>
                                            @if (!$loop->last)
                                                <div class="w-px h-8 bg-gray-200 mt-2"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 pb-2">
                                            <div class="text-sm font-semibold text-gray-900">{{ $label }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if ($ts)
                                                    {{ optional($ts)->format('M j, g:i A') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Shipping Info --}}
                        @if ($status === 'delivered' || ($status === 'completed' && $order->delivered_by))
                            <div class="mt-6 p-4 bg-green-50 border-2 border-green-100 rounded-xl">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">
                                            {{ statictranslations('Package Delivered Successfully!') }}</p>
                                        <p class="text-xs text-green-800 mt-1">
                                            {{ statictranslations('Your order has been delivered to your address.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 mb-4 lg:mb-6 flex items-center">
                            <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
                            {{ statictranslations('Order Items') }} ({{ $order->items->count() }})
                        </h2>

                        <div class="space-y-4">
                            @foreach ($order->items as $item)
                                @php
                                    $variant = $item->variant;
                                    $product = $item->product;
                                    $img =
                                        optional(optional(optional($variant)?->images())?->first())->image_path ??
                                        (optional($product)->cover_image ??
                                            optional(optional(optional($product)?->images())?->first())->image_path);
                                    $imgUrl = $img
                                        ? (\Illuminate\Support\Str::startsWith($img, ['http', 'https', '/'])
                                            ? $img
                                            : asset('storage/' . ltrim($img, '/')))
                                        : 'https://via.placeholder.com/100x100';
                                    $productName = $product->name ?? 'Product';
                                    $variantName = $variant && $variant->variant_name ? $variant->variant_name : null;
                                    $title = $variantName ? $productName . ' - ' . $variantName : $productName;
                                @endphp

                                <div
                                    class="flex gap-4 p-4 border-2 border-gray-100 rounded-xl hover:border-gray-200 transition-colors">
                                    <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        <img src="{{ $imgUrl }}" alt="{{ $title }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm lg:text-base mb-1 line-clamp-2">
                                            {{ $variantName }}</h3>
                                        <p class="text-xs text-gray-500 mb-2">{{ statictranslations('Qty') }}:
                                            {{ $item->quantity }}</p>
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex-1">
                                                @php
                                                    $hasDiscount =
                                                        isset($item->unit_price_original) &&
                                                        $item->unit_price_original > $item->unit_price;
                                                @endphp
                                                @if ($hasDiscount)
                                                    <div class="flex flex-col">
                                                        <div>
                                                            <span
                                                                class="inline-block text-xs text-gray-400 line-through opacity-75">
                                                                {{ config('app.CURRENCY_SYMBOL') }}
                                                                {{ number_format($item->unit_price_original * $item->quantity, 2) }}
                                                            </span>
                                                        </div>
                                                        <span class="text-2xl font-bold text-red-600">
                                                            {{ config('app.CURRENCY_SYMBOL') }}
                                                            {{ number_format($item->unit_price * $item->quantity, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-2xl font-bold text-primary">
                                                        {{ config('app.CURRENCY_SYMBOL') }}
                                                        {{ number_format($item->unit_price * $item->quantity, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if (in_array(strtolower($order->status ?? ''), ['delivered', 'completed']))
                                                @if (!empty($userReviews[$item->id]))
                                                    @php
                                                        $userReview = $userReviews[$item->id];
                                                        $reviewStatus = strtolower(
                                                            $userReview->is_approved ?? 'pending',
                                                        );
                                                        $reviewImages = $userReview->images
                                                            ? $userReview->images
                                                                ->map(function ($image) {
                                                                    $path = $image->image_path ?? '';
                                                                    return \Illuminate\Support\Str::startsWith($path, [
                                                                        'http',
                                                                        'https',
                                                                        '/',
                                                                    ])
                                                                        ? $path
                                                                        : asset('storage/' . ltrim($path, '/'));
                                                                })
                                                                ->values()
                                                            : collect();
                                                    @endphp
                                                    @if ($reviewStatus === 'approved')
                                                        <button type="button"
                                                            onclick='openReviewModal({
                                                                mode: "view",
                                                                id: {{ $userReview->id }},
                                                                product_id: {{ $product->id ?? 0 }},
                                                                product_variant_id: {{ $item->variant_id ?? 'null' }},
                                                                order_id: {{ $order->id }},
                                                                order_item_id: {{ $item->id }},
                                                                rating: {{ $userReview->rating }},
                                                                title: @json($userReview->title ?? ''),
                                                                review_text: @json($userReview->review_text ?? ''),
                                                                reviewer_name: @json($userReview->reviewer_name ?? ''),
                                                                reviewer_email: @json($userReview->reviewer_email ?? ''),
                                                                fallback_reviewer_name: @json(Auth::user()?->name ?? ''),
                                                                fallback_reviewer_email: @json(Auth::user()?->email ?? ''),
                                                                product_name: @json($productName),
                                                                variant_name: @json($variantName ?? ''),
                                                                product_image: @json($imgUrl),
                                                                images: @json($reviewImages)
                                                            })'
                                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-green-500 hover:text-gray-700 bg-white hover:bg-white border hover:border-gray-300 border-green-600 rounded-lg transition-all duration-200">
                                                            {{ statictranslations('View Review') }}
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            onclick='openReviewModal({
                                                                id: {{ $userReview->id }},
                                                                product_id: {{ $product->id ?? 0 }},
                                                                product_variant_id: {{ $item->variant_id ?? 'null' }},
                                                                order_id: {{ $order->id }},
                                                                order_item_id: {{ $item->id }},
                                                                rating: {{ $userReview->rating }},
                                                                title: @json($userReview->title ?? ''),
                                                                review_text: @json($userReview->review_text ?? ''),
                                                                reviewer_name: @json($userReview->reviewer_name ?? ''),
                                                                reviewer_email: @json($userReview->reviewer_email ?? ''),
                                                                fallback_reviewer_name: @json(Auth::user()?->name ?? ''),
                                                                fallback_reviewer_email: @json(Auth::user()?->email ?? ''),
                                                                product_name: @json($productName),
                                                                variant_name: @json($variantName ?? ''),
                                                                product_image: @json($imgUrl),
                                                                images: @json($reviewImages)
                                                            })'
                                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-primary transition-all duration-200">
                                                            {{ statictranslations('Edit Review') }}
                                                        </button>
                                                    @endif
                                                @else
                                                    <button type="button"
                                                        onclick='openReviewModal({
                                                            product_id: {{ $product->id ?? 0 }},
                                                            product_variant_id: {{ $item->variant_id ?? 'null' }},
                                                            order_id: {{ $order->id }},
                                                            order_item_id: {{ $item->id }},
                                                            product_name: @json($productName),
                                                            variant_name: @json($variantName ?? ''),
                                                            product_image: @json($imgUrl),
                                                            reviewer_name: @json(Auth::user()?->name ?? ''),
                                                            reviewer_email: @json(Auth::user()?->email ?? '')
                                                        })'
                                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary hover:text-gray-700 bg-white hover:bg-white border hover:border-gray-300 border-primary rounded-lg transition-all duration-200">
                                                        {{ statictranslations('Write Review') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-4 lg:space-y-6">

                    {{-- Order Summary --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h3 class="text-lg font-bold flex items-center justify-between text-gray-900 mb-4">
                            {{ statictranslations('Order Summary') }}
                            <span class="text-sm font-semibold text-gray-600">{{ statictranslations('Item Total:') }}
                                {{ $order->items->sum('quantity') }}</span>
                        </h3>
                        @php
                            // Use stored order data (new fields added via migration)
                            $subtotal = $order->subtotal ?? 0;
                            $itemDiscount = $order->discount_amount ?? 0;
                            $couponDiscount = $order->coupon_discount_amount ?? 0;
                            $shipping = $order->shipping_cost ?? 0;
                            $tax = $order->tax_amount ?? 0;
                            $total = $order->total ?? 0;
                        @endphp
                        <div class="space-y-3 mb-4 pb-4 border-b-2 border-gray-100">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ statictranslations('Subtotal') }}</span>
                                <span class="font-semibold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }}
                                    {{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if ($itemDiscount > 0)
                                <div class="flex items-center justify-between text-sm bg-green-50 px-3 py-2 rounded-lg">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd"
                                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ statictranslations('Item Discounts') }}
                                    </span>
                                    <span class="font-semibold text-green-600">-{{ config('app.CURRENCY_SYMBOL') }}
                                        {{ number_format($itemDiscount, 2) }}</span>
                                </div>
                            @endif

                            @if ($couponDiscount > 0)
                                <div class="flex items-center justify-between text-sm bg-purple-50 px-3 py-2 rounded-lg">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-purple-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                        @if ($order->coupon_code)
                                            <span class="text-xs text-purple-600">{{ statictranslations('Code') }}: <span
                                                    class="font-semibold">{{ $order->coupon_code }}</span></span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-purple-600">-{{ config('app.CURRENCY_SYMBOL') }}
                                        {{ number_format($couponDiscount, 2) }}</span>
                                </div>
                            @endif
                            {{--
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ statictranslations('Shipping') }}</span>
                                <span class="font-semibold text-gray-900">
                                    @if ($shipping == 0)
                                        <span class="text-green-600">Free</span>
                                    @else
                                        <span>{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($shipping, 2) }}</span>
                                        @if ($order->shipping_method)
                                            <span
                                                class="text-xs text-gray-500">({{ ucfirst($order->shipping_method) }})</span>
                                        @endif
                                    @endif
                                </span>
                            </div> --}}
                        </div>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-base lg:text-lg font-bold text-gray-900">Total</span>
                            <span class="text-xl lg:text-2xl font-bold text-primary">{{ config('app.CURRENCY_SYMBOL') }}
                                {{ number_format($total, 2) }}</span>
                        </div>

                        <div class="space-y-3">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span
                                        class="text-xs font-semibold text-gray-700">{{ statictranslations('Payment Method') }}</span>
                                </div>
                                @if ($order->payment_method == 'cod')
                                    <p class="text-sm text-gray-900 font-medium">
                                        {{ statictranslations('Cash on Delivery') }}</p>
                                @elseif ($order->payment_method == 'card')
                                    <p class="text-sm text-gray-900 font-medium">
                                        {{ statictranslations('Online Payment') }}</p>
                                @else
                                    <p class="text-sm text-gray-900 font-medium">{{ $order->payment_method }}</p>
                                @endif
                            </div>

                            @if ($order->email)
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="text-xs font-semibold text-blue-700">{{ statictranslations('Email') }}</span>
                                    </div>
                                    <p class="text-sm text-blue-900 font-medium">{{ $order->email }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ statictranslations('Shipping Address') }}
                        </h3>

                        @php
                            $addr = $order->shipping_address ?? [];
                            if (!is_array($addr)) {
                                // try to decode JSON or cast
                                $decoded = @json_decode($addr, true);
                                $addr = is_array($decoded) ? $decoded : [];
                            }
                        @endphp

                        <div class="text-sm text-gray-700 space-y-1">
                            <p class="font-semibold text-gray-900">{{ $addr['name'] ?? ($order->user->name ?? '—') }}</p>
                            @if (!empty($addr['street']))
                                <p>{{ $addr['street'] }}</p>
                            @endif
                            @if (!empty($addr['apartment']))
                                <p>{{ $addr['apartment'] }}</p>
                            @endif
                            <p>{{ trim(implode(' ', array_filter([$addr['city'] ?? '', $addr['state'] ?? '', $addr['postal_code'] ?? '']))) }}
                            </p>
                            <p>{{ $addr['country'] ?? '' }}</p>
                            <p class="pt-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="font-medium">{{ $addr['phone'] ?? ($order->phone_no ?? '—') }}</span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/order-cancel-popup.js') }}"></script>
@endpush
