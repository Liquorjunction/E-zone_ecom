@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>Frequently Asked Questions</x-slot:title>
                <x-slot:content>Find answers to common questions about our products and services.</x-slot:content>
            </x-breadcrumbs>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">

                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        Ordering
                    </h2>
                    <div class="divide-y-2 divide-gray-100 mb-8">
                        <details class="group py-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none">
                                <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">How do I
                                    place an order?</span>
                                <span class="text-primary group-open:rotate-180 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <p class="text-gray-700 mt-3 leading-relaxed">Simply find the product you want, click "Add to
                                Cart," and then proceed to checkout. Follow the on-screen instructions to enter your
                                shipping and payment information.</p>
                        </details>
                        <details class="group py-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none">
                                <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Can I
                                    modify or cancel my order?</span>
                                <span class="text-primary group-open:rotate-180 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <p class="text-gray-700 mt-3 leading-relaxed">If your order has not yet been processed, you can
                                contact our support team to request a modification or cancellation. Please note that we
                                cannot guarantee changes once an order is in the processing stage.</p>
                        </details>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        Shipping & Returns
                    </h2>
                    <div class="divide-y-2 divide-gray-100">
                        <details class="group py-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none">
                                <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">What
                                    are your shipping options?</span>
                                <span class="text-primary group-open:rotate-180 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <p class="text-gray-700 mt-3 leading-relaxed">We offer Standard Shipping (5-7 business days),
                                Express Shipping (2-3 business days), and Overnight Shipping (1 business day). All orders
                                over $50 qualify for free standard shipping.</p>
                        </details>
                        <details class="group py-4">
                            <summary class="flex justify-between items-center cursor-pointer list-none">
                                <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">What is
                                    your return policy?</span>
                                <span class="text-primary group-open:rotate-180 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <p class="text-gray-700 mt-3 leading-relaxed">We offer a 30-day return policy for a full refund
                                on most items. Products must be returned in their original condition and packaging. Please
                                visit our Shipping & Returns page for full details.</p>
                        </details>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
