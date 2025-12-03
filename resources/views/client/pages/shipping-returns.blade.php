@extends('client.layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen pb-16 relative overflow-hidden">

        {{-- Optional: Subtle Background Blob for visual depth --}}
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-96 bg-linear-to-b from-white to-transparent opacity-60 pointer-events-none">
        </div>

        {{-- Page Header / Breadcrumbs --}}
        <div class="relative z-10 border-b border-gray-200 bg-white/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 py-6">
                <x-breadcrumbs>
                    <x-slot:title>Shipping & Returns</x-slot:title>
                    <x-slot:content>Learn about our shipping and return policies.</x-slot:content>
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
                        @if($shippingandreturns)
                            {{-- Header Section --}}
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 pb-8 border-b border-gray-100">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Shipping & Returns</h1>
                                    <p class="text-gray-500">Agreement between User and {{ config('app.name') }}</p>
                                </div>

                                {{-- Date Badge --}}
                                <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-100 rounded-full">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2.5 animate-pulse"></div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mr-2">Last Updated:</span>
                                    <span class="text-sm font-bold text-gray-900"></span>
                                </div>
                            </div>

                             {{-- Dynamic Content (Prose) --}}
                            <div class="prose prose-lg max-w-none
                                {{-- Headings --}}
                                prose-headings:font-bold prose-headings:text-gray-900 prose-headings:scroll-mt-24
                                prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-6 prose-h2:pb-4 prose-h2:border-b prose-h2:border-gray-100
                                prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-4 prose-h3:text-gray-800

                                {{-- Text & Links --}}
                                prose-p:text-gray-600 prose-p:leading-8 prose-p:mb-6
                                prose-a:text-primary prose-a:font-medium prose-a:no-underline hover:prose-a:underline hover:prose-a:text-primary-dark
                                prose-strong:text-gray-900 prose-strong:font-bold

                                {{-- Lists --}}
                                prose-ul:my-6 prose-ul:list-disc prose-ul:pl-6 prose-li:marker:text-gray-300 prose-li:pl-2
                                prose-ol:my-6 prose-ol:list-decimal prose-ol:pl-6

                                {{-- Blockquotes --}}
                                prose-blockquote:border-l-4 prose-blockquote:border-primary/30 prose-blockquote:bg-gray-50 prose-blockquote:py-2 prose-blockquote:px-6 prose-blockquote:rounded-r-lg prose-blockquote:italic prose-blockquote:text-gray-700">

                                {!! $shippingandreturns->description ?? $shippingandreturns->content !!}
                            </div>
                        @else
                            <div class="prose prose-lg max-w-none">
                                <h2 class="text-3xl font-bold text-gray-900 mb-2">Shipping & Returns</h2>
                                <p class="text-gray-500">Agreement between User and {{ config('app.name') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
