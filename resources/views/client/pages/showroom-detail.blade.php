@extends('client.layouts.app')

@section('content')
    @php
        $locale = app()->getLocale();
        $name = $showroom->getTranslation('name', $locale) ?? (is_array($showroom->name) ? null : $showroom->name);
        $address = $showroom->getTranslation('address', $locale) ?? (is_array($showroom->address) ? null : $showroom->address);
        $city = $showroom->getTranslation('city', $locale) ?? (is_array($showroom->city) ? null : $showroom->city);
        $state = $showroom->getTranslation('state', $locale) ?? (is_array($showroom->state) ? null : $showroom->state);
        $country = $showroom->getTranslation('country', $locale) ?? (is_array($showroom->country) ? null : $showroom->country);
        $description = $showroom->getTranslation('description', $locale) ?? (is_array($showroom->description) ? null : $showroom->description);
        $location = trim(implode(', ', array_filter([$address, $city, $state, $country, $showroom->pincode])));
        $mapQuery = urlencode($location);
        $coverImage = !empty($showroom->cover_image) ? asset('storage/' . $showroom->cover_image) : null;
    @endphp

    <style>
        .ez-showroom-page {
            font-family: "Space Grotesk", "DM Sans", "Futura", sans-serif;
        }

        .ez-fade-up {
            opacity: 0;
            transform: translateY(18px);
            animation: ezFadeUp 0.7s ease-out forwards;
        }

        .ez-delay-1 {
            animation-delay: 0.08s;
        }

        .ez-delay-2 {
            animation-delay: 0.16s;
        }

        .ez-delay-3 {
            animation-delay: 0.24s;
        }

        .ez-delay-4 {
            animation-delay: 0.32s;
        }

        @keyframes ezFadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="bg-gray-50 min-h-screen pb-16 relative overflow-hidden">

        {{-- Optional: Subtle Background Blob for visual depth --}}
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-96 bg-linear-to-b from-white to-transparent opacity-60 pointer-events-none">
        </div>

        {{-- Page Header / Breadcrumbs --}}
        <div class="relative z-10 border-b border-gray-200 bg-white/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 py-6">
                <x-breadcrumbs>
                    <x-slot:title>{{ $name }}</x-slot:title>
                    <x-slot:content>{{ statictranslations('Find directions, opening hours, and contact details.') }}</x-slot:content>
                </x-breadcrumbs>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="container mx-auto px-4 py-12 relative z-10 animate-slide-up">
            <div class="max-w-8xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-7 space-y-6">
                        <div
                            class="relative overflow-hidden rounded-3xl border border-gray-100 bg-white/95 shadow-xl ez-fade-up">
                            <div
                                class="relative w-full h-72 sm:h-96 lg:h-[450px] bg-linear-to-r from-slate-100 via-gray-50 to-slate-100">
                                @if ($coverImage)
                                    <img src="{{ $coverImage }}" alt="{{ $name }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-gray-400 font-semibold text-lg">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fa-solid fa-store text-3xl opacity-50"></i>
                                            <span>{{ statictranslations('Showroom Preview') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if (!empty($description))
                                <div class="p-6 sm:p-8 bg-white">
                                    <div class="text-gray-700 leading-relaxed prose-content">
                                        {!! $description !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if ($images->count() > 0)
                                <div class="rounded-3xl border border-gray-100 bg-white/95 shadow-lg p-6 ez-fade-up ez-delay-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ statictranslations('Gallery') }}</h3>

                                    <!-- Main Image Area -->
                                    <div
                                        class="aspect-square rounded-2xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center mb-4 relative group">
                                        <img id="main-product-image" src="{{ asset('storage/' . $images->first()->image) }}"
                                            alt="{{ $name }}" class="w-full h-full object-cover" style="cursor: zoom-in;">
                                        <div
                                            class="absolute bottom-4 right-4 text-xs font-semibold px-3 py-1 rounded-lg backdrop-blur-sm bg-black/55 text-white opacity-0 group-hover:opacity-100 transition">
                                            {{ statictranslations('Click to zoom') }}
                                        </div>
                                    </div>

                                    <!-- Thumbnail Grid -->
                                    <div class="flex gap-3 overflow-x-auto pb-2">
                                        @foreach ($images as $image)
                                            <button type="button"
                                                class="thumb-button flex-shrink-0 w-16 h-16 rounded-lg border-2 border-gray-200 overflow-hidden transition-all duration-200 hover:border-primary/50"
                                                data-image="{{ asset('storage/' . $image->image) }}"
                                                aria-label="{{ statictranslations('View image') }}">
                                                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $name }}"
                                                    class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="rounded-3xl border border-gray-100 bg-white/95 shadow-lg p-6 ez-fade-up ez-delay-2">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ statictranslations('Opening Hours') }}
                                </h3>
                                @if (!empty($showroom->opening_hours) && is_array($showroom->opening_hours))
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

                                        $orderedHours = [];
                                        foreach ($weekdayOrder as $key => $label) {
                                            if (isset($showroom->opening_hours[$key])) {
                                                $orderedHours[$key] = [
                                                    'label' => $label,
                                                    'hours' => $showroom->opening_hours[$key]
                                                ];
                                            }
                                        }
                                        foreach ($showroom->opening_hours as $key => $hours) {
                                            if (!array_key_exists($key, $weekdayOrder)) {
                                                $orderedHours[$key] = [
                                                    'label' => statictranslations(ucfirst($key)),
                                                    'hours' => $hours
                                                ];
                                            }
                                        }
                                    @endphp
                                    <ul class="space-y-2 text-gray-700">
                                        @foreach ($orderedHours as $dayKey => $data)
                                            <li class="flex justify-between border-b border-gray-100 pb-2">
                                                <span class="font-semibold">{{ $data['label'] }}</span>
                                                @php
                                                    $hours = $data['hours'];
                                                    $isClosed = false;
                                                    if (is_array($hours)) {
                                                        if (isset($hours['is_closed']) && $hours['is_closed']) {
                                                            $displayHours = statictranslations('Closed');
                                                            $isClosed = true;
                                                        } else {
                                                            $open = $hours['open'] ?? '';
                                                            $close = $hours['close'] ?? '';
                                                            if ($open && $close) {
                                                                // Format times slightly better if possible, or just concat
                                                                $displayHours = $open . ' - ' . $close;
                                                            } else {
                                                                $displayHours = statictranslations('Closed');
                                                                $isClosed = true;
                                                            }
                                                        }
                                                    } else {
                                                        $val = (string) $hours;
                                                        if ($val === '1' || $val === 'true' || $hours === true) {
                                                            $displayHours = statictranslations('Open');
                                                        } elseif ($val === '0' || $val === 'false' || $hours === false || empty($val)) {
                                                            $displayHours = statictranslations('Closed');
                                                            $isClosed = true;
                                                        } else {
                                                            $displayHours = $hours;
                                                        }
                                                    }
                                                @endphp
                                                <span class="{{ $isClosed ? 'text-red-600 font-semibold' : '' }}">{{ $displayHours }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-600">{{ statictranslations('Opening hours are not available.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 space-y-6">
                        <div class="rounded-[28px] border border-gray-100 bg-white/95 shadow-xl p-6 ez-fade-up ez-delay-2">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ statictranslations('Quick Contacts') }}</h3>
                            <div class="space-y-4 text-gray-700">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ statictranslations('Phone') }}</p>
                                        <p class="text-sm">{{ $showroom->phone ?? statictranslations('Not available') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ statictranslations('Email') }}</p>
                                        <p class="text-sm">{{ $showroom->email ?? statictranslations('Not available') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ statictranslations('Address') }}</p>
                                        <p class="text-sm">{{ $location ?? statictranslations('Not available') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @if (!empty($showroom->phone))
                                    <a href="tel:{{ $showroom->phone }}"
                                        class="px-4 py-3 rounded-2xl bg-primary text-white text-center font-semibold hover:bg-primary/90 transition">
                                        {{ statictranslations('Call Us') }}
                                    </a>
                                @endif
                                @if (!empty($showroom->email))
                                    <a href="mailto:{{ $showroom->email }}"
                                        class="px-4 py-3 rounded-2xl border border-gray-200 text-gray-700 text-center font-semibold hover:border-primary hover:text-primary transition">
                                        {{ statictranslations('Email Us') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ statictranslations('Map') }}</h3>
                        @if (!empty($showroom->map_iframe))
                            <div class="rounded-2xl overflow-hidden border border-gray-100">
                                {!! $showroom->map_iframe !!}
                            </div>
                        @else
                            <div class="bg-gray-100 rounded-2xl h-64 flex items-center justify-center text-gray-500">
                                {{ statictranslations('Map placeholder') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/product-image-gallery.js') }}"></script>
