@extends('client.layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen pb-16">

        {{-- Page Header / Breadcrumbs --}}
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-8">
                <x-breadcrumbs>
                    <x-slot:title>{{ statictranslations('Privacy Policy') }}</x-slot:title>
                    <x-slot:content>{{ statictranslations('Your privacy and data security are important to us.') }}</x-slot:content>
                </x-breadcrumbs>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="flex flex-col lg:flex-row gap-10 relative">

                {{-- LEFT SIDEBAR: Table of Contents (Sticky) --}}
                <aside class="hidden lg:block lg:w-1/4">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                {{ statictranslations('Table of Contents') }}
                            </h3>
                            <nav class="flex flex-col space-y-1" aria-label="Privacy Policy Navigation">
                                @foreach ($privacyPolicy as $index => $content)
                                    <a href="#section-{{ $index }}"
                                       class="group flex items-start py-2 px-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-primary transition-all duration-200">
                                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-gray-300 mr-3 group-hover:bg-primary transition-colors"></span>
                                        {{ $content->title }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <main class="lg:w-3/4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 lg:p-12">

                        <div class="prose prose-lg max-w-none
                            prose-headings:font-bold prose-headings:text-gray-900
                            prose-h2:text-2xl prose-h2:border-b prose-h2:border-gray-100 prose-h2:pb-4 prose-h2:mb-6 prose-h2:mt-12 first:prose-h2:mt-0
                            prose-p:text-gray-600 prose-p:leading-8
                            prose-a:text-primary prose-a:no-underline hover:prose-a:underline
                            prose-ul:text-gray-600 prose-li:marker:text-gray-300">

                            {{-- Introduction / Last Updated Placeholder (Optional) --}}
                            <div class="mb-5 pb-6 border-b border-gray-100">
                                <p class="text-sm text-gray-500 font-medium">
                                    {{ statictranslations('Last Updated') }}: <span class="text-gray-800">{{ isset($lastUpdated) && $lastUpdated ? $lastUpdated->locale(app()->getLocale())->translatedFormat('F d, Y') : 'N/A' }}</span>
                                </p>
                                <p>
                                    {{ statictranslations('Please read our privacy policy carefully to get a clear understanding of how we collect, use, protect or otherwise handle your Personally Identifiable Information in accordance with our website.') }}
                                </p>
                            </div>

                            {{-- Dynamic Content Loop --}}
                            @foreach ($privacyPolicy as $index => $content)
                                <div id="section-{{ $index }}" class="scroll-mt-24 mb-6 prose-content">
                                    <h2>{{ $content->title }}</h2>

                                    <div class="text-justify">
                                        {!! $content->description !!}
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="border-gray-100 my-8">
                                @endif
                            @endforeach

                        </div>

                        {{-- Footer of the document --}}
                        <div class="mt-16 pt-8 border-t border-gray-100 text-center">
                            <p class="text-gray-500">
                                {{ statictranslations('If you have any questions about this policy, please') }}
                                <a href="{{ route('contact') }}" class="text-primary font-semibold hover:underline">{{ statictranslations('contact us') }}</a>.
                            </p>
                        </div>

                    </div>
                </main>

            </div>
        </div>
    </div>

    {{-- Optional: Smooth Scroll CSS for this page --}}
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
@endsection
