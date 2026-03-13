@extends('client.layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen pb-16">

        {{-- Page Header / Breadcrumbs --}}
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-8">
                <x-breadcrumbs>
                    <x-slot:title>{{ statictranslations('Frequently Asked Questions') }}</x-slot:title>
                    <x-slot:content>{{ statictranslations('Find answers to common questions about our products and services.') }}</x-slot:content>
                </x-breadcrumbs>
            </div>
        </div>

        {{-- Main Content Container with Slide Up Animation --}}
        <div class="container mx-auto px-4 py-12 animate-slide-up">
            <div class="flex flex-col lg:flex-row gap-10 relative">

                {{-- LEFT SIDEBAR: Category Navigation (Sticky) --}}
                <aside class="hidden lg:block lg:w-1/4">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                FAQ Categories
                            </h3>
                            <nav class="flex flex-col space-y-1" aria-label="FAQ Navigation">
                                @foreach ($faqcategories ?? [] as $category)
                                    @if(isset($category->faqs) && $category->faqs->isNotEmpty())
                                        <a href="#category-{{ $category->id }}"
                                           class="group flex items-start py-2 px-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary transition-all duration-200">
                                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-gray-300 mr-3 group-hover:bg-primary transition-colors"></span>
                                            {{ $category->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </nav>
                        </div>

                        {{-- Quick Contact Box --}}
                        <div class="mt-6 bg-blue-50 rounded-xl p-6 border border-blue-100">
                            <h4 class="text-blue-900 font-semibold mb-2">Still need help?</h4>
                            <p class="text-sm text-blue-700 mb-4">Can't find the answer you're looking for?</p>
                            <a href="{{ route('contact') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                Contact Support &rarr;
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <main class="lg:w-3/4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 lg:p-12">

                        @if (empty($faqcategories) || $faqcategories->isEmpty())
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No FAQs Found</h3>
                                <p class="text-gray-500 mt-2">Check back later for updates.</p>
                            </div>
                        @else
                            <div class="space-y-12">
                                {{-- Loop through Categories --}}
                                @foreach (($faqcategories ?? []) as $loopIndex => $category)
                                    @if(isset($category->faqs) && $category->faqs->isNotEmpty())
                                        <div id="category-{{ $category->id }}" class="scroll-mt-28">

                                        {{-- Category Title --}}
                                        <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                                            <span class="w-1.5 h-6 rounded-full bg-primary mr-3"></span>
                                            <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                                        </div>

                                        {{-- Questions Accordion --}}
                                        <div class="space-y-4">
                                            @foreach ($category->faqs ?? [] as $faq)
                                                <details class="group bg-gray-50 rounded-lg border border-transparent hover:border-gray-200 transition-all duration-200 open:bg-white open:border-gray-200 open:shadow-sm">
                                                    <summary class="flex justify-between items-center cursor-pointer p-4 list-none">
                                                        <span class="font-semibold text-gray-700 group-hover:text-primary group-open:text-primary transition-colors pr-4">
                                                            {{ $faq->question }}
                                                        </span>
                                                        <span class="text-gray-400 group-hover:text-primary group-open:text-primary group-open:rotate-180 transition-transform duration-200 shrink-0">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </span>
                                                    </summary>

                                                    {{-- Answer Content --}}
                                                    {{-- The animation for this div is now handled in app.css by details[open] summary ~ * --}}
                                                    <div class="px-4 pb-4 pt-0 text-gray-600 leading-relaxed">
                                                        <div class="pt-2 border-t border-gray-100 mt-2 prose-content">
                                                            {!! $faq->answer !!}
                                                        </div>
                                                    </div>
                                                </details>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Footer of the document --}}
                        <div class="mt-16 pt-8 border-t border-gray-100 text-center">
                            <p class="text-gray-500">
                                Still can't find what you're looking for?
                                <a href="{{ route('contact') }}" class="text-primary font-semibold hover:underline">Contact our support team</a>.
                            </p>
                        </div>

                    </div>
                </main>

            </div>
        </div>
    </div>
@endsection
