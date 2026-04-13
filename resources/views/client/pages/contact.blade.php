@extends('client.layouts.app')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Contact') }}</x-slot:title>
                <x-slot:content>{{ statictranslations("We're here to help. Get in touch with us!") }}</x-slot:content>
            </x-breadcrumbs>

            {{-- Success Banner (shown after submission) --}}
            @if (session('contact_success'))
                <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-green-800 text-lg">{{ statictranslations('Message Received!') }}</h3>
                        <p class="text-green-700 text-sm mt-1">
                            {{ statictranslations('Your ticket') }} <strong>#{{ session('ticket_number', 'ENQ-XXXXXX') }}</strong> {{ statictranslations('has been created.') }}
                            {{ statictranslations("We've sent a confirmation to your email. Our team will respond within") }} <strong>{{ statictranslations('24–48 hours') }}</strong>.
                        </p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ─── CONTACT FORM ─────────────────────────────────────────── --}}
                <div class="lg:col-span-2">
                    <form action="{{ route('contact.submit') }}" method="POST" enctype="multipart/form-data" id="contactForm"
                        class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
                        @csrf

                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                            {{ statictranslations('Send Us a Message') }}
                        </h2>

                        <div class="space-y-5">

                            {{-- Row 1: Name + Email --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ statictranslations('Full Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 @error('name') border-red-400 @enderror"
                                        placeholder="{{ statictranslations('John Doe') }}">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ statictranslations('Email Address') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 @error('email') border-red-400 @enderror"
                                        placeholder="{{ statictranslations('you@example.com') }}">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 2: Mobile + Subject --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="mobile" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ statictranslations('Mobile Number') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="mobile" name="mobile" required value="{{ old('mobile') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 @error('mobile') border-red-400 @enderror"
                                        placeholder="{{ statictranslations('+212 123 456 789') }}">
                                    @error('mobile')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ statictranslations('Subject') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select id="subject" name="subject" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 appearance-none bg-white cursor-pointer @error('subject') border-red-400 @enderror">
                                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>{{ statictranslations('— Select a subject —') }}</option>
                                            <option value="product_inquiry"
                                                {{ old('subject') === 'product_inquiry' ? 'selected' : '' }}>{{ statictranslations('Product Inquiry') }}</option>
                                            <option value="order_status"
                                                {{ old('subject') === 'order_status' ? 'selected' : '' }}>{{ statictranslations('Order Status') }}</option>
                                            <option value="return_replacement"
                                                {{ old('subject') === 'return_replacement' ? 'selected' : '' }}>{{ statictranslations('Return / Replacement') }}</option>
                                            <option value="warranty_claim"
                                                {{ old('subject') === 'warranty_claim' ? 'selected' : '' }}>{{ statictranslations('Warranty Claim') }}</option>
                                            <option value="installation_support"
                                                {{ old('subject') === 'installation_support' ? 'selected' : '' }}>{{ statictranslations('Installation Support') }}</option>
                                            <option value="technical_issue"
                                                {{ old('subject') === 'technical_issue' ? 'selected' : '' }}>{{ statictranslations('Technical Issue') }}</option>
                                            <option value="bulk_purchase"
                                                {{ old('subject') === 'bulk_purchase' ? 'selected' : '' }}>{{ statictranslations('Bulk Purchase Inquiry') }}</option>
                                            <option value="complaint"
                                                {{ old('subject') === 'complaint' ? 'selected' : '' }}>{{ statictranslations('Complaint') }}</option>
                                            <option value="other"
                                                {{ old('subject') === 'other' ? 'selected' : '' }}>{{ statictranslations('Other') }}</option>
                                        </select>
                                        <span
                                            class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('subject')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── Conditional: Order Number (order_status | return_replacement | warranty_claim | complaint) --}}
                            <div id="orderNumberWrapper" class="conditional-field hidden transition-all duration-300">
                                <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                                    <label for="order_number"
                                        class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ statictranslations('Order Number') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="order_number" name="order_number"
                                        value="{{ old('order_number') }}"
                                        class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-amber-100 focus:border-amber-400 transition-all duration-300 bg-white"
                                        placeholder="{{ statictranslations('e.g. ORD-20240001') }}">
                                    <p class="text-amber-600 text-xs mt-2">{{ statictranslations('You can find your order number in your confirmation email or order history.') }}</p>
                                </div>
                            </div>

                            {{-- ── Conditional: Product SKU (product_inquiry | installation_support | technical_issue | warranty_claim) --}}
                            <div id="productSkuWrapper" class="conditional-field hidden transition-all duration-300">
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                                    <label for="product_sku"
                                        class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5l4.586 4.586A2 2 0 0117 9v10a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                        </svg>
                                        {{ statictranslations('Product SKU / Model Number') }}
                                    </label>
                                    <input type="text" id="product_sku" name="product_sku"
                                        value="{{ old('product_sku') }}"
                                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all duration-300 bg-white"
                                        placeholder="{{ statictranslations('e.g. SKU-ABC-12345') }}">
                                    <p class="text-blue-600 text-xs mt-2">{{ statictranslations('Find the SKU on the product label, packaging, or your invoice.') }}</p>
                                </div>
                            </div>

                            {{-- Message --}}
                            <div>
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ statictranslations('Message') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 resize-none @error('message') border-red-400 @enderror"
                                    placeholder="{{ statictranslations('Describe your issue or question in detail…') }}">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ── File Upload ──────────────────────────────────────────── --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ statictranslations('Attachments') }}
                                    <span class="font-normal text-gray-400 ml-1">{{ statictranslations('(Invoice, product images, screenshots — max 5 files, 5 MB each)') }}</span>
                                </label>

                                <div id="dropZone"
                                    class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-300 group"
                                    onclick="document.getElementById('attachments').click()">

                                    <input type="file" id="attachments" name="attachments[]" multiple
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                                        onchange="handleFileSelect(this)">

                                    <div class="flex flex-col items-center gap-2 pointer-events-none">
                                        <div
                                            class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-primary/10 flex items-center justify-center transition-all duration-300">
                                            <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors duration-300"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <p
                                            class="text-sm text-gray-500 group-hover:text-primary transition-colors duration-300">
                                            <span class="font-semibold">{{ statictranslations('Click to upload') }}</span> {{ statictranslations('or drag & drop') }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ statictranslations('PNG, JPG, PDF, DOC, XLS up to 5 MB each') }}</p>
                                    </div>
                                </div>

                                {{-- File Preview List --}}
                                <ul id="fileList" class="mt-3 space-y-2"></ul>
                            </div>

                        </div>{{-- /space-y-5 --}}

                        <div
                            class="mt-8 pt-6 border-t-2 border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                            <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ statictranslations('A confirmation email with your ticket number will be sent automatically.') }}
                            </p>
                            <button type="submit"
                                class="px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ statictranslations('Send Message') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ─── RIGHT SIDEBAR ────────────────────────────────────────────── --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Contact Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center">
                            <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                            {{ statictranslations('Contact Info') }}
                        </h2>
                        <div class="space-y-4">
                            @php
                                $addressText = trim((string) ($siteContent->address ?? ''));
                                $phoneText = trim((string) ($siteContent->phone ?? ''));
                                $emailText = trim((string) ($siteContent->email ?? ''));

                                $hasAddress = $addressText !== '';
                                $hasPhone = $phoneText !== '';
                                $hasEmail = $emailText !== '';
                            @endphp
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ statictranslations('Address') }}</h3>
                                    <p class="text-gray-600 text-sm mt-0.5">
                                        {{ $hasAddress ? $addressText : statictranslations('N/A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ statictranslations('Phone') }}</h3>
                                    @if($hasPhone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phoneText) }}" class="text-primary text-sm hover:underline">{{ $phoneText }}</a>
                                    @else
                                        <p class="text-gray-600 text-sm">{{ statictranslations('N/A') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ statictranslations('Email') }}</h3>
                                    @if($hasEmail)
                                        <a href="mailto:{{ $emailText }}"
                                            class="text-primary text-sm hover:underline">{{ $emailText }}</a>
                                    @else
                                        <p class="text-gray-600 text-sm">{{ statictranslations('N/A') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ statictranslations('Response Time') }}</h3>
                                    <p class="text-gray-600 text-sm mt-0.5">{{ statictranslations('Within') }} <strong>{{ statictranslations('24–48 hours') }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Showroom Details --}}
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-5 flex items-center">
                            <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                            {{ statictranslations('Visit Our Showroom') }}
                        </h2>

                        @php
                            $weekdayOrder = [
                                'monday' => statictranslations('Monday'),
                                'tuesday' => statictranslations('Tuesday'),
                                'wednesday' => statictranslations('Wednesday'),
                                'thursday' => statictranslations('Thursday'),
                                'friday' => statictranslations('Friday'),
                                'saturday' => statictranslations('Saturday'),
                                'sunday' => statictranslations('Sunday'),
                            ];

                            $openingHoursData = [];
                            if (!empty($siteContent?->opening_hours)) {
                                $decodedOpeningHours = json_decode($siteContent->opening_hours, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedOpeningHours)) {
                                    $openingHoursData = $decodedOpeningHours;
                                }
                            }

                            $mapIframeRaw = trim((string) ($siteContent?->map_iframe ?? ''));
                            $mapUrl = null;
                            if ($mapIframeRaw !== '') {
                                if (stripos($mapIframeRaw, '<iframe') !== false && preg_match('/src=["\']([^"\']+)["\']/i', $mapIframeRaw, $iframeSrcMatch)) {
                                    $mapUrl = $iframeSrcMatch[1];
                                } elseif (filter_var($mapIframeRaw, FILTER_VALIDATE_URL)) {
                                    $mapUrl = $mapIframeRaw;
                                }
                            }

                            $openInMapsUrl = $mapUrl;
                            if (str_contains($mapUrl, 'output=embed')) {
                                $openInMapsUrl = str_replace('output=embed', '', $mapUrl);
                            }
                        @endphp

                        <div class="space-y-4">
                            {{-- Working Hours --}}
                            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ statictranslations('Working Hours') }}
                                </h3>
                                <div class="space-y-1.5 text-sm">
                                    @if(!empty($openingHoursData))
                                        @foreach($weekdayOrder as $dayKey => $dayLabel)
                                            @php
                                                $dayInfo = $openingHoursData[$dayKey] ?? [];
                                                $isClosed = filter_var($dayInfo['is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN);
                                                $openTime = $dayInfo['open'] ?? null;
                                                $closeTime = $dayInfo['close'] ?? null;

                                                $timeLabel = statictranslations('Closed');
                                                if (!$isClosed && !empty($openTime) && !empty($closeTime)) {
                                                    $formattedOpen = date('g:i A', strtotime($openTime));
                                                    $formattedClose = date('g:i A', strtotime($closeTime));
                                                    $timeLabel = $formattedOpen . ' – ' . $formattedClose;
                                                }
                                            @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">{{ $dayLabel }}</span>
                                                <span class="font-medium {{ $timeLabel === statictranslations('Closed') ? 'text-red-500' : 'text-gray-900' }}">{{ $timeLabel }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($weekdayOrder as $dayLabel)
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">{{ $dayLabel }}</span>
                                                <span class="font-medium text-gray-500">{{ statictranslations('N/A') }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Map --}}
                            <div class="pt-2">
                                <h3 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    {{ statictranslations('Find Us Here') }}
                                </h3>
                                <div
                                    class="aspect-video bg-gray-200 rounded-xl overflow-hidden relative group cursor-pointer">
                                    @if(!empty($mapUrl))
                                        <iframe
                                            src="{{ $mapUrl }}"
                                            class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade" title="{{ statictranslations('Showroom Location') }}">
                                        </iframe>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-sm text-gray-600 bg-gray-100">
                                            {{ statictranslations('Map not available (N/A)') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /right sidebar --}}

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/contact.js') }}"></script>
@endpush

