{{--
    Partial: checkout/partials/_billing-address-section.blade.php
    Extracted so AddressController can re-render just this section
    after a new billing address is saved, without a full page reload.
    Variables: $billingAddresses (Collection), $cities (Collection)
--}}
<div id="billingAddressSection" class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                {{ statictranslations('Billing Address') }}
            </h2>
            <p class="text-gray-500 text-sm mt-1">{{ statictranslations('Payment and billing information') }}</p>
        </div>
        <label class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-xl border-2 border-blue-200 cursor-pointer hover:bg-blue-100 transition-all">
            <input type="checkbox" id="billingSameAsShipping" name="billing_same_as_shipping"
                value="1" class="form-checkbox h-4 w-4 text-primary rounded" checked>
            <span class="ml-2 text-sm font-semibold text-gray-700">{{ statictranslations('Same as shipping') }}</span>
        </label>
    </div>

    <div id="billingFields" class="space-y-5 hidden">
        @if (Auth::check() && $billingAddresses->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-4">
                    <label for="billingAddressSelect" class="block text-sm font-semibold text-gray-700">
                        {{ statictranslations('Select Billing Address') }}
                        <span class="text-xs font-normal text-gray-500 ml-1">({{ $billingAddresses->count() }} {{ statictranslations('available') }})</span>
                    </label>
                    <button type="button" id="addBillingAddressBtn"
                        class="px-4 py-2.5 bg-linear-to-r from-primary to-primary/90 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ statictranslations('Add New') }}
                    </button>
                </div>
                <div class="relative">
                    <select id="billingAddressSelect" name="saved_billing_address_id"
                        class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium">
                        <option value="">-- {{ statictranslations('Select billing address') }} --</option>
                        @foreach ($billingAddresses as $addr)
                            <option value="{{ $addr->id }}"
                                data-name="{{ $addr->name }}"
                                data-street="{{ $addr->street }}"
                                data-city="{{ $addr->city }}"
                                data-postal-code="{{ $addr->postal_code }}"
                                data-country="{{ $addr->country }}"
                                data-phone="{{ $addr->phone }}"
                                {{ $addr->is_default ? 'selected' : '' }}>
                                {{ $addr->address_label ? '💳 ' . $addr->address_label . ' - ' : '' }}{{ $addr->name }}
                                ({{ collect([$addr->city, $addr->postal_code])->filter()->implode(', ') }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <div id="selectedBillingDisplay"
                    class="hidden mt-4 p-4 bg-linear-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-blue-800 mb-1">{{ statictranslations('Billing To:') }}</p>
                            <p id="displayBillingName" class="font-bold text-gray-900"></p>
                            <p id="displayBillingStreet" class="text-sm text-gray-700"></p>
                            <p id="displayBillingCity" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif (Auth::check())
            <div class="text-center py-10 bg-linear-to-br from-gray-50 to-purple-50 rounded-2xl border-2 border-dashed border-gray-300">
                <button type="button" id="addBillingAddressBtnEmpty"
                    class="px-6 py-3 bg-linear-to-r from-primary to-primary/90 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ statictranslations('Add Billing Address') }}
                </button>
            </div>
        @else
            {{--
                Guests must log in to add addresses via the address model.
                Billing will be copied from the shipping address (same-as-shipping is
                checked by default). If they uncheck it and have no saved billing
                address, validation will block submission.
            --}}
            <div class="text-center py-8 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border-2 border-dashed border-gray-300">
                <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-700 mb-1">{{ statictranslations('Billing address will match shipping') }}</p>
                <p class="text-xs text-gray-500">{{ statictranslations('Log in to manage saved addresses') }}</p>
            </div>
        @endif

        {{-- Hidden fields populated by JS (used for all auth states) --}}
        <input type="hidden" id="billing_firstName"    name="billing_firstName"    value="">
        <input type="hidden" id="billing_lastName"     name="billing_lastName"     value="">
        <input type="hidden" id="billing_address_line" name="billing_address_line" value="">
        <input type="hidden" id="billing_city"         name="billing_city"         value="">
        <input type="hidden" id="billing_zip"          name="billing_zip"          value="">
        <input type="hidden" id="billing_country"      name="billing_country"      value="">
        <input type="hidden" id="billing_phone"        name="billing_phone"        value="">
    </div>
</div>
