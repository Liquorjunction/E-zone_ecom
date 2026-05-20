@extends('client.layouts.app')

@section('title', 'Access Forbidden - Ezone Electronics')
@section('description', 'You do not have permission to access this page. Please contact support if you believe this is an error.')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 text-center">
                <div class="mb-8">
                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-6xl font-bold text-gray-900 mb-4">403</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Access Forbidden</h2>
                <p class="text-gray-600 mb-8">You don't have permission to access this page.</p>
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
