@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Help & Support') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('Get help and support.') }}</x-slot:content>
            </x-breadcrumbs>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <a href="{{ route('contact') }}"
                    class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
                    <div
                        class="w-16 h-16 mb-4 rounded-2xl bg-primary/10 text-primary flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ statictranslations('Contact Us') }}
                    </h3>
                    <p class="text-gray-600 mb-4">{{ statictranslations('Get in touch with our support team directly via email, phone, or our contact form.') }}</p>
                    <span class="font-semibold text-primary">{{ statictranslations('Get Help') }} →</span>
                </a>

                <a href="{{ route('faq') }}"
                    class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
                    <div
                        class="w-16 h-16 mb-4 rounded-2xl bg-primary/10 text-primary flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4 0 1.847-1.133 3.37-2.835 3.844M12 8v.01M12 16v.01" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ statictranslations('FAQs') }}</h3>
                    <p class="text-gray-600 mb-4">{{ statictranslations('Find quick answers to common questions about orders, shipping, returns, and your account.') }}</p>
                    <span class="font-semibold text-primary">{{ statictranslations('View Answers') }} →</span>
                </a>

                <a href="{{ route('shipping-returns') }}"
                    class="group bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg hover:border-primary transform hover:-translate-y-1">
                    <div
                        class="w-16 h-16 mb-4 rounded-2xl bg-primary/10 text-primary flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ statictranslations('Shipping & Returns') }}</h3>
                    <p class="text-gray-600 mb-4">{{ statictranslations('Learn about our shipping options, delivery timelines, and our 30-day return policy.') }}</p>
                    <span class="font-semibold text-primary">{{ statictranslations('Read Policy') }} →</span>
                </a>

            </div>

            <div class="mt-8 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                    {{ statictranslations('All Information & Policies') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    <a href="{{ route('about') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ statictranslations('About Us') }}</span>
                    </a>

                    <a href="{{ route('showrooms') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ statictranslations('Our Showrooms') }}</span>
                    </a>

                    <a href="{{ route('terms') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ statictranslations('Terms And Conditions') }}</span>
                    </a>

                    <a href="{{ route('privacy-policy') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>{{ statictranslations('Privacy Policy') }}</span>
                    </a>

                    <a href="{{ route('dashboard.overview') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ statictranslations('My Account') }}</span>
                    </a>

                    <a href="{{ route('dashboard.orders') }}"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>{{ statictranslations('Track My Order') }}</span>
                    </a>

                </div>
            </div>

        </div>
    </div>
@endsection
