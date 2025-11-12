@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>Contact</x-slot:title>
                <x-slot:content>We're here to help. Get in touch with us!</x-slot:content>
            </x-breadcrumbs>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2">
                    <form class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                            Send Us a Message
                        </h2>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name
                                        *</label>
                                    <input type="text" id="name" name="name" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="John Doe">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email
                                        Address *</label>
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="you@example.com">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone
                                        Number</label>
                                    <input type="tel" id="phone" name="phone"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="+1 (555) 000-0000">
                                </div>
                                <div>
                                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject
                                        *</label>
                                    <input type="text" id="subject" name="subject" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="Question about an order">
                                </div>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message
                                    *</label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                    placeholder="Write your message here..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t-2 border-gray-100 flex justify-end">
                            <button type="submit"
                                class="px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 space-y-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                                Contact Info
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-primary mt-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Address</h3>
                                        <p class="text-gray-600">123 E-commerce St.<br>Jaipur, Rajasthan, 302001</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-primary mt-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Phone</h3>
                                        <p class="text-gray-600">+91 (123) 456-7890</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-primary mt-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Email</h3>
                                        <p class="text-gray-600">support@example.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t-2 border-gray-100">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                                Find Us Here
                            </h2>
                            <div class="aspect-video bg-gray-200 rounded-xl overflow-hidden">
                                <img src="https://via.placeholder.com/400x300?text=Google+Map+Placeholder" alt="Map"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
