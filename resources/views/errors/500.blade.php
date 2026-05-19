@extends('client.layouts.app')

@section('title', 'Internal Server Error - Ezone Electronics')
@section('description', 'We are experiencing technical difficulties. Please try again later or contact our support team.')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 text-center">
                <div class="mb-8">
                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h1 class="text-6xl font-bold text-gray-900 mb-4">500</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Internal Server Error</h2>
                <p class="text-gray-600 mb-8">Something went wrong on our end. Please try again later.</p>
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
