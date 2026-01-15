@extends('client.layouts.app')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>Order Details</x-slot:title>
                <x-slot:content>Track your order and view details</x-slot:content>
            </x-breadcrumbs>

            {{-- Order Header --}}
            @php
                $status = strtolower($order->status ?? 'pending');
                $statusClass = match($status) {
                    'delivered' => 'text-green-700 bg-green-100',
                    'shipped' => 'text-primary bg-primary/10',
                    'processing' => 'text-yellow-700 bg-yellow-100',
                    'cancelled' => 'text-red-700 bg-red-100',
                    default => 'text-gray-700 bg-gray-100'
                };
                $stages = ['placed','processing','shipped','delivered'];
                $currentIndex = array_search($status, $stages, true);
                if ($currentIndex === false) $currentIndex = max(0, min(count($stages)-1, 0));
                $progressPct = (($currentIndex + 1) / count($stages)) * 100;
            @endphp

            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h1>
                            <span class="px-3 py-1.5 text-xs lg:text-sm font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($order->status ?? 'Pending') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Placed on {{ optional($order->created_at)->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('client.orders.invoice', $order->id) ?? '#' }}" class="px-4 py-2.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Invoice
                            </span>
                        </a>
                        <a href="#" class="px-4 py-2.5 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary/5 transition-colors text-sm">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Reorder
                            </span>
                        </a>
                        @if(Auth::check() && $order->user_id === Auth::id() && !in_array(strtolower($order->status ?? ''), ['cancelled','shipped','delivered']))
                            <form method="POST" action="{{ route('client.orders.cancel', $order->id) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');" class="inline-block">
                                @csrf
                                <button type="submit" class="px-4 py-2.5 border-2 border-red-200 text-red-700 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel Order
                                    </span>
                                </button>
                            </form>
                        @endif
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
                            Order Tracking
                        </h2>

                        {{-- Desktop Timeline: horizontal stepper --}}
                        <div class="hidden md:block">
                            <div class="relative py-6">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>

                                <div class="relative flex justify-between items-start">
                                    @foreach($stages as $idx => $stage)
                                        @php
                                            $isActive = $idx <= $currentIndex;
                                            $label = ucfirst($stage);
                                            // try stage specific timestamps if present on model
                                            $tsField = $stage . '_at';
                                            $ts = $order->{$tsField} ?? null;
                                            if (! $ts) {
                                                // fallback: show created_at for placed, updated_at for others when active
                                                $ts = $stage === 'placed' ? $order->created_at : ($isActive ? $order->updated_at : null);
                                            }
                                        @endphp

                                        <div class="flex-1 min-w-0 text-center px-2">
                                            <div class="mx-auto w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ $isActive ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
                                                @if($isActive)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    <span class="text-sm font-semibold">{{ $idx+1 }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-semibold text-gray-900">{{ $label }}</div>
                                            <div class="text-xs text-gray-500 mt-1">@if($ts) {{ optional($ts)->format('M j, g:i A') }} @endif</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Mobile Timeline: stacked steps --}}
                        <div class="md:hidden space-y-4">
                            @foreach($stages as $idx => $stage)
                                @php
                                    $isActive = $idx <= $currentIndex;
                                    $tsField = $stage . '_at';
                                    $ts = $order->{$tsField} ?? null;
                                    if (! $ts) {
                                        $ts = $stage === 'placed' ? $order->created_at : ($isActive ? $order->updated_at : null);
                                    }
                                @endphp
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center w-12 flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isActive ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500' }}">
                                            @if($isActive)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span class="text-sm font-semibold">{{ $idx+1 }}</span>
                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-px h-full bg-gray-200 mt-2"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-semibold text-gray-900">{{ ucfirst($stage) }}</div>
                                            <div class="text-xs text-gray-500">@if($ts) {{ optional($ts)->format('M j, g:i A') }} @endif</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">@if($isActive) Status: {{ ucfirst($order->status ?? '') }} @endif</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Shipping Info --}}
                        <div class="mt-6 p-4 bg-green-50 border-2 border-green-100 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-green-900">Package Delivered Successfully!</p>
                                    <p class="text-xs text-green-800 mt-1">Your order has been delivered to your address. Delivered by: John Smith</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-900 mb-4 lg:mb-6 flex items-center">
                                <span class="w-1 h-6 lg:h-7 rounded-full bg-primary mr-3"></span>
                                Order Items ({{ $order->items->count() }})
                            </h2>

                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    @php
                                        $variant = $item->variant;
                                        $product = $item->product;
                                        $img = optional(optional(optional($variant)?->images())?->first())->image_path ?? optional($product)->cover_image ?? optional(optional(optional($product)?->images())?->first())->image_path;
                                        $imgUrl = $img ? (\Illuminate\Support\Str::startsWith($img, ['http','https','/']) ? $img : asset('storage/' . ltrim($img, '/'))) : 'https://via.placeholder.com/100x100';
                                        $title = $variant && $variant->variant_name ? $variant->variant_name : ($product->name ?? 'Product');
                                        if (is_array($title)) {
                                            if (method_exists($product, 'getTranslation')) {
                                                $title = $product->getTranslation('name', app()->getLocale() ?? 'en') ?? $product->getTranslation('name', 'en') ?? implode(' ', array_values($title));
                                            } else {
                                                $title = implode(' ', array_values($title));
                                            }
                                        }

                                        $attributes = null;
                                        if ($variant && $variant->attributes) {
                                            $attrs = (array) $variant->attributes;
                                            $pairs = [];
                                            foreach ($attrs as $k => $v) {
                                                if (is_array($v)) {
                                                    $arr = (array) $v;
                                                    $val = (array_keys($arr) !== range(0, count($arr) - 1)) ? json_encode($arr) : implode(', ', $arr);
                                                } else {
                                                    $val = (string) $v;
                                                }
                                                $pairs[] = $k . ': ' . $val;
                                            }
                                            $attributes = implode(' | ', $pairs);
                                        }
                                        $productName = $product->name ?? null;
                                        if (is_array($productName)) {
                                            if (method_exists($product, 'getTranslation')) {
                                                $productName = $product->getTranslation('name', app()->getLocale() ?? 'en') ?? $product->getTranslation('name', 'en') ?? implode(' ', array_values($productName));
                                            } else {
                                                $productName = implode(' ', array_values($productName));
                                            }
                                        }
                                    @endphp

                                    <div class="flex gap-4 p-4 border-2 border-gray-100 rounded-xl hover:border-gray-200 transition-colors">
                                        <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                            <img src="{{ $imgUrl }}" alt="{{ $title }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 text-sm lg:text-base mb-1 line-clamp-2">{{ $title }}</h3>
                                            @if($attributes)
                                                <p class="text-xs text-gray-600 mb-2">{{ $attributes }}</p>
                                            @elseif($product && $productName)
                                                <p class="text-xs text-gray-600 mb-2">{{ $productName }}</p>
                                            @endif
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <div class="flex items-baseline gap-2">
                                                    <p class="text-base lg:text-lg font-bold text-primary">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($item->unit_price, 2) }}</p>
                                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                                </div>
                                                <button class="text-xs lg:text-sm font-semibold text-primary hover:text-primary/80">Write Review</button>
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
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h3>
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
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($itemDiscount > 0)
                                <div class="flex items-center justify-between text-sm bg-green-50 px-3 py-2 rounded-lg">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Item Discounts
                    </span>
                    <span class="font-semibold text-green-600">-{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($itemDiscount, 2) }}</span>
                </div>
                            @endif

                            @if($couponDiscount > 0)
                                <div class="flex items-center justify-between text-sm bg-purple-50 px-3 py-2 rounded-lg">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                        Coupon Discount
                                    </span>
                                    <span class="font-semibold text-purple-600">-{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($couponDiscount, 2) }}</span>
                                </div>
                                @if($order->coupon_code)
                                    <p class="text-xs text-purple-600 px-3">Code: <span class="font-semibold">{{ $order->coupon_code }}</span></p>
                                @endif
                            @endif

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold text-gray-900">
                                    @if($shipping == 0)
                                        <span class="text-green-600">Free</span>
                                    @else
                                        <span>{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($shipping, 2) }}</span>
                                        @if($order->shipping_method)
                                            <span class="text-xs text-gray-500">({{ ucfirst($order->shipping_method) }})</span>
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Tax ({{ $order->tax_percentage ?? 8 }}%)</span>
                                <span class="font-semibold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($tax, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-base lg:text-lg font-bold text-gray-900">Total</span>
                            <span class="text-xl lg:text-2xl font-bold text-primary">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($total, 2) }}</span>
                        </div>

                        <div class="space-y-3">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-700">Payment Method</span>
                                </div>
                                <p class="text-sm text-gray-900 font-medium">{{ $order->payment_method ?? ($order->payment ?? '—') }}</p>
                            </div>

                            @if($order->email)
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-blue-700">Email</span>
                                    </div>
                                    <p class="text-sm text-blue-900 font-medium">{{ $order->email }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100 p-4 lg:p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Shipping Address
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
                            @if(!empty($addr['street']))
                                <p>{{ $addr['street'] }}</p>
                            @endif
                            @if(!empty($addr['apartment']))
                                <p>{{ $addr['apartment'] }}</p>
                            @endif
                            <p>{{ trim(implode(' ', array_filter([$addr['city'] ?? '', $addr['state'] ?? '', $addr['postal_code'] ?? '']))) }}</p>
                            <p>{{ $addr['country'] ?? '' }}</p>
                            <p class="pt-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="font-medium">{{ $addr['phone'] ?? $order->phone_no ?? '—' }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Need Help --}}
                    <div class="bg-gradient-to-br from-primary/5 to-primary/10 rounded-xl lg:rounded-2xl border-2 border-primary/20 p-4 lg:p-6">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Need Help?</h4>
                                <p class="text-xs text-gray-600 mb-3">
                                    Have questions about your order? Our support team is here to help.
                                </p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <button class="w-full px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors text-sm">
                                Contact Support
                            </button>
                            <button class="w-full px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Track Package
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
