@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('Saved Addresses'))

@section('dashboard-content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ statictranslations('Your Addresses') }}</h2>
                <p class="text-sm text-gray-600">{{ statictranslations('Manage delivery and billing addresses') }}</p>
            </div>
            <button type="button" onclick="openAddModal()" class="hidden sm:flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>{{ statictranslations('Add New Address') }}</span>
            </button>
        </div>

        <button type="button" onclick="openAddModal()" class="sm:hidden inline-flex w-full items-center justify-center gap-2 px-4 py-3 mb-6 bg-primary text-white font-semibold rounded-xl shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            <span>{{ statictranslations('Add New Address') }}</span>
        </button>

        <div id="addresses-grid" class="space-y-8">
            @php
                // If controller supplied pre-sorted collections use them, otherwise fallback
                $shipping = $shipping ?? (collect($addresses ?? [])->where('type', 'shipping')->values());
                $billing = $billing ?? (collect($addresses ?? [])->where('type', 'billing')->values());
            @endphp

            <section>
                <h3 class="text-xl font-semibold mb-4">{{ statictranslations('Shipping Addresses') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($shipping as $address)
                        @include('client.pages.dashboard._address_card', ['address' => $address])
                    @empty
                        <div class="col-span-full text-center py-8">
                            <button type="button" onclick="openAddModal()" class="group flex flex-col items-center justify-center min-h-50 w-full border-3 border-dashed border-gray-300 rounded-2xl text-gray-400 hover:border-primary hover:bg-primary/5 hover:text-primary transition-all duration-300">
                                <div class="bg-gray-100 group-hover:bg-primary/10 p-4 rounded-full mb-3 transition-colors duration-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                </div>
                                <span class="font-semibold text-base">{{ statictranslations('Add Shipping Address') }}</span>
                                <span class="text-sm text-gray-400 mt-1">{{ statictranslations('Click to add') }}</span>
                            </button>
                        </div>
                    @endforelse
                </div>
            </section>

            <section>
                <h3 class="text-xl font-semibold mb-4">{{ statictranslations('Billing Addresses') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($billing as $address)
                        @include('client.pages.dashboard._address_card', ['address' => $address])
                    @empty
                        <div class="col-span-full text-center py-8">
                            <button type="button" onclick="openAddModal()" class="group flex flex-col items-center justify-center min-h-50 w-full border-3 border-dashed border-gray-300 rounded-2xl text-gray-400 hover:border-primary hover:bg-primary/5 hover:text-primary transition-all duration-300">
                                <div class="bg-gray-100 group-hover:bg-primary/10 p-4 rounded-full mb-3 transition-colors duration-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                </div>
                                <span class="font-semibold text-base">{{ statictranslations('Add Billing Address') }}</span>
                                <span class="text-sm text-gray-400 mt-1">{{ statictranslations('Click to add') }}</span>
                            </button>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div id="addr-unique-modal" style="z-index: 99999;" class="hidden fixed inset-0 items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="addr-unique-content">
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200 bg-linear-to-r from-primary/5 to-primary/10">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 p-2.5 rounded-xl">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 id="addr-modal-title" class="text-2xl font-bold text-gray-900">{{ statictranslations('Add Address') }}</h3>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="address-form" method="POST" action="{{ url('/client/addresses') }}" class="max-h-[calc(90vh-180px)] overflow-y-auto" novalidate>
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="address-id" value="">

                <div class="px-8 py-6 space-y-6">
                    <div id="address-form-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Address Type') }}</label>
                            <div class="relative">
                                <select id="field-type" name="type" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white" required>
                                    <option value="billing">{{ statictranslations('Billing') }}</option>
                                    <option value="shipping">{{ statictranslations('Shipping') }}</option>
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Custom Label') }} <span class="text-gray-400 font-normal">({{ statictranslations('Optional') }})</span></label>
                            <input id="field-address_label" name="address_label" placeholder="{{ statictranslations("e.g., Mom's House") }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Full Name') }}</label>
                            <input id="field-name" name="name" placeholder="{{ statictranslations('John Doe') }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" required />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Phone') }} <span class="text-gray-400 font-normal">({{ statictranslations('Optional') }})</span></label>
                            <input id="field-phone" name="phone" type="tel" placeholder="{{ statictranslations('+1 555 000 0000') }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Street Address') }}</label>
                        <input id="field-street" name="street" placeholder="{{ statictranslations('123 Main Street, Apt 4B') }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('City') }}</label>
                            <div class="relative">
                                <select id="field-city" name="city_id" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white" required>
                                    <option value="">{{ statictranslations('Select city') }}</option>
                                    @foreach(($cities ?? collect()) as $city)
                                        <option value="{{ $city->id }}" data-country="{{ $city->getTranslation('country_name', app()->getLocale()) }}">
                                            {{ $city->getTranslation('city_name', app()->getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Postal Code') }}</label>
                            <input id="field-postal_code" name="postal_code" placeholder="{{ statictranslations('10001') }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" maxlength="10" inputmode="numeric" pattern="\d*" />
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Country') }}</label>
                            <input id="field-country" name="country" placeholder="{{ statictranslations('United States') }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" required />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-8 py-6 bg-gray-50 border-t border-gray-200 rounded-b-3xl">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all">
                        {{ statictranslations('Cancel') }}
                    </button>
                    <button type="submit" id="form-submit" class="px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ statictranslations('Save Address') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="mobile-address-form" style="z-index: 99999;" class="sm:hidden fixed inset-0 bg-white transform translate-y-full transition-transform duration-300">
        <div class="sticky top-0 z-10 bg-linear-to-r from-primary/5 to-primary/10 border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-4">
                <button type="button" onclick="closeMobileForm()" class="p-2 hover:bg-white/50 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 id="mobile-form-title" class="text-lg font-bold text-gray-900">{{ statictranslations('Add Address') }}</h3>
                <div class="w-10"></div>
            </div>
        </div>

        <div class="overflow-y-auto h-[calc(100vh-73px)] pb-24">
            <form id="mobile-address-form-element" method="POST" action="{{ url('/client/addresses') }}" class="p-4 space-y-5" novalidate>
                @csrf
                <input type="hidden" name="_method" id="mobile-form-method" value="POST">
                <input type="hidden" name="id" id="mobile-address-id" value="">

                <div id="mobile-address-form-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Address Type') }}</label>
                    <div class="relative">
                        <select id="mobile-field-type" name="type" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white text-base" required>
                            <option value="billing">{{ statictranslations('Billing') }}</option>
                            <option value="shipping">{{ statictranslations('Shipping') }}</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Custom Label') }} <span class="text-gray-400 font-normal">({{ statictranslations('Optional') }})</span></label>
                    <input id="mobile-field-address_label" name="address_label" placeholder="{{ statictranslations("e.g., Mom's House") }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Full Name') }}</label>
                    <input id="mobile-field-name" name="name" placeholder="{{ statictranslations('John Doe') }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" required />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Phone') }} <span class="text-gray-400 font-normal">({{ statictranslations('Optional') }})</span></label>
                    <input id="mobile-field-phone" name="phone" type="tel" placeholder="{{ statictranslations('+1 555 000 0000') }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Street Address') }}</label>
                    <input id="mobile-field-street" name="street" placeholder="{{ statictranslations('123 Main Street, Apt 4B') }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" required />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('City') }}</label>
                    <div class="relative">
                        <select id="mobile-field-city" name="city_id" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white text-base" required>
                            <option value="">{{ statictranslations('Select city') }}</option>
                            @foreach(($cities ?? collect()) as $city)
                                <option value="{{ $city->id }}" data-country="{{ $city->getTranslation('country_name', app()->getLocale()) }}">
                                    {{ $city->getTranslation('city_name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Postal Code') }}</label>
                    <input id="mobile-field-postal_code" name="postal_code" placeholder="{{ statictranslations('10001') }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" maxlength="10" inputmode="numeric" pattern="\d*" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Country') }}</label>
                    <input id="mobile-field-country" name="country" placeholder="{{ statictranslations('United States') }}" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" required />
                </div>
            </form>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 p-4 shadow-2xl">
            <button type="button" onclick="submitMobileForm()" id="mobile-form-submit-btn" class="w-full py-4 rounded-xl bg-primary text-white font-bold text-base hover:shadow-lg transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ statictranslations('Save Address') }}
            </button>
        </div>
    </div>

    <button type="button" onclick="openAddModal()" style="z-index: 9998;" class="sm:hidden fixed bottom-6 right-6 bg-primary text-white p-5 rounded-full shadow-2xl hover:shadow-xl hover:scale-110 transition-all duration-300 flex items-center justify-center group">
        <svg class="w-7 h-7 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
    </button>

    @push('scripts')
        <script src="{{ asset('js/addresses.js') }}" defer></script>
    @endpush
@endsection
