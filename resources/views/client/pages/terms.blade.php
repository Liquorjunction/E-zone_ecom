@extends('client.layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen pb-16 relative overflow-hidden">

        {{-- Optional: Subtle Background Blob for visual depth --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-96 bg-linear-to-b from-white to-transparent opacity-60 pointer-events-none"></div>

        {{-- Page Header / Breadcrumbs --}}
        <div class="relative z-10 border-b border-gray-200 bg-white/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 py-6">
                <x-breadcrumbs>
                    <x-slot:title>Terms of Service</x-slot:title>
                    <x-slot:content>Please read our terms and conditions carefully.</x-slot:content>
                </x-breadcrumbs>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="container mx-auto px-4 py-12 relative z-10 animate-slide-up">
            <div class="max-w-8xl mx-auto">

                {{-- Document Card --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                    {{-- Card Top Accent --}}
                    <div class="h-1.5 w-full bg-primary"></div>

                    <div class="p-8 md:p-12 lg:p-16">
                        @if($terms)
                            {{-- Header Section --}}
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 pb-8 border-b border-gray-100">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Service</h1>
                                    <p class="text-gray-500">Agreement between User and {{ config('app.name') }}</p>
                                </div>

                                {{-- Date Badge --}}
                                <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-100 rounded-full">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2.5 animate-pulse"></div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mr-2">Last Updated:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $terms->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            {{-- Dynamic Content (Prose) --}}
                            <div class="prose-content">
                                {!! $terms->description ?? $terms->content !!}
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Terms Unavailable</h3>
                                <p class="text-gray-500 mt-2 max-w-sm mx-auto">We are currently updating our terms of service. Please check back later.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Card Footer --}}
                    <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <p class="text-sm text-gray-500">
                            Questions? <a href="{{ route('contact') }}" class="text-primary font-semibold hover:underline">Contact Support</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
