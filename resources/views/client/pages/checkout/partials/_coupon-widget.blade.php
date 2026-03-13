{{--
    Partial: checkout/partials/_coupon-widget.blade.php
    Renders the "Have a Coupon Code?" card.

    Variables:
      - $appliedCoupon : array|null  – session coupon data (id, code, discount_amount, …)
      - $idSuffix      : string      – '' for step 1, '-step2' for step 2
--}}

@php $suffix = $idSuffix ?? ''; @endphp

<div class="mb-6 bg-linear-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6">
    <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
        </svg>
        {{ statictranslations('Have a Coupon Code?') }}
    </h3>

    @if ($appliedCoupon)
        {{-- Coupon already applied (from session) --}}
        <div class="bg-white rounded-xl p-4 border-2 border-purple-200" id="applied-coupon-display{{ $suffix }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $appliedCoupon['code'] }}</p>
                        <p class="text-xs text-gray-600">{{ statictranslations('Coupon Applied') }}</p>
                    </div>
                </div>
                <button type="button" id="remove-coupon-btn{{ $suffix }}"
                    class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded-lg hover:bg-red-50 text-sm font-semibold">
                    {{ statictranslations('Remove') }}
                </button>
            </div>
        </div>
    @else
        {{-- Coupon input form --}}
        <div class="flex gap-3" id="coupon-input-form{{ $suffix }}">
            <input type="text" id="coupon-code-input{{ $suffix }}"
                placeholder="{{ statictranslations('Enter coupon code') }}"
                class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm uppercase">
            <button type="button" id="apply-coupon-btn{{ $suffix }}"
                class="px-6 py-3 bg-linear-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                {{ statictranslations('Apply') }}
            </button>
        </div>
    @endif

    <div id="coupon-message{{ $suffix }}" class="mt-3 text-sm hidden"></div>
</div>
