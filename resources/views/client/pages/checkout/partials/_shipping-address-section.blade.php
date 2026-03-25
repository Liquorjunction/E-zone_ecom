{{--
    Partial: checkout/partials/_shipping-address-section.blade.php
    Extracted so AddressController can re-render just this section
    after a new shipping address is saved, without a full page reload.
    Variables: $addresses (Collection), $cities (Collection)
--}}
<div id="shippingAddressSection" class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                {{ statictranslations('Shipping Address') }}
            </h2>
            <p class="text-gray-500 text-sm mt-1">{{ statictranslations('Where should we deliver your order?') }}</p>
        </div>
        @if (Auth::check())
            <button type="button" id="addShippingAddressBtn"
                class="px-4 py-2.5 bg-linear-to-r from-primary to-primary/90 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ statictranslations('Add New') }}
            </button>
        @endif
    </div>

    @if (Auth::check() && $addresses->count() > 0)
        <div class="mb-5">
            <label for="shippingAddressSelect" class="block text-sm font-semibold text-gray-700 mb-2">
                {{ statictranslations('Saved Addresses') }}
                <span class="text-xs font-normal text-gray-500 ml-1">({{ $addresses->count() }} {{ statictranslations('available') }})</span>
            </label>
            <div class="relative">
                <select id="shippingAddressSelect" name="saved_address_id"
                    class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium">
                    <option value="">-- {{ statictranslations('Select a saved address') }} --</option>
                    @foreach ($addresses as $addr)
                        <option value="{{ $addr->id }}"
                            data-name="{{ $addr->name }}"
                            data-street="{{ $addr->street }}"
                            data-city="{{ $addr->city }}"
                            data-postal-code="{{ $addr->postal_code }}"
                            data-country="{{ $addr->country }}"
                            data-phone="{{ $addr->phone }}"
                            {{ $addr->is_default ? 'selected' : '' }}>
                            {{ $addr->address_label ? '🏠 ' . $addr->address_label . ' - ' : '' }}{{ $addr->name }}
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

            <div id="selectedShippingDisplay"
                class="hidden mt-4 p-4 bg-linear-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-800 mb-1">{{ statictranslations('Shipping To:') }}</p>
                        <p id="displayShippingName" class="font-bold text-gray-900"></p>
                        <p id="displayShippingStreet" class="text-sm text-gray-700"></p>
                        <p id="displayShippingCity" class="text-sm text-gray-700"></p>
                    </div>
                </div>
            </div>
        </div>
    @elseif (Auth::check())
        <div class="text-center py-10 bg-linear-to-br from-gray-50 to-purple-50 rounded-2xl border-2 border-dashed border-gray-300 mb-5">
            <button type="button" id="addShippingAddressBtnEmpty"
                class="px-6 py-3 bg-linear-to-r from-primary to-primary/90 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ statictranslations('Add Shipping Address') }}
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 {{ Auth::check() && $addresses->count() > 0 ? 'hidden' : '' }}"
        id="manualAddressFields">
        <div>
            <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('First Name') }} *</label>
            <input type="text" id="firstName" name="firstName" required
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
        </div>
        <div>
            <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Last Name') }} *</label>
            <input type="text" id="lastName" name="lastName" required
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
        </div>
        <div class="md:col-span-2">
            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Street Address') }} *</label>
            <input type="text" id="address" name="address" required
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
        </div>
        <div>
            <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('City') }} *</label>
            <input type="text" id="city" name="city" required
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
        </div>
        <div>
            <label for="zip" class="block text-sm font-semibold text-gray-700 mb-2">
                {{ statictranslations('Postal Code') }}
                <span class="text-gray-400 font-normal">{{ statictranslations('(Optional)') }}</span>
            </label>
            <input type="text" id="zip" name="zip"
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
        </div>
        <div>
            <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Country') }} *</label>
            <input type="text" id="country" name="country" value="Morocco" required
                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
        </div>
    </div>

    @if (Auth::check())
        <label class="inline-flex items-center mt-5">
            <input type="checkbox" name="save_address" value="1" class="form-checkbox h-4 w-4 text-primary rounded mr-3">
            <span class="text-sm font-medium text-gray-700">{{ statictranslations('Save this address for future orders') }}</span>
        </label>
    @endif
</div>
