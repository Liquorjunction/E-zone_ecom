@extends('client.layouts.app')

@section('title', 'Page Not Found - E-Zone Electronics')
@section('description', 'The page you are looking for could not be found. Browse our sitemap or return to the homepage.')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 text-center">
                <div class="mb-8">
                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-.98-5.625-2.509M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z" />
                    </svg>
                </div>
                <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
                <p class="text-gray-600 mb-8">Sorry, the page you are looking for doesn't exist or has been moved.</p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-medium rounded-xl hover:bg-primary/90 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1zm5 0a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1z" />
                    </svg>
                    Go Home
                </a>
            </div>
        </div>
    </div>
@endsection
