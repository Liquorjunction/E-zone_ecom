@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-7xl">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Shop:') }} {{ $subcategory ? $subcategory->getTranslation('sub-category-name', app()->getLocale()) : Str::title($subcategory_slug) }}</x-slot:title>
                <x-slot:content>{{ statictranslations('All products from') }} {{ $subcategory ? $subcategory->getTranslation('sub-category-name', app()->getLocale()) : Str::title($subcategory_slug) }}.</x-slot:content>
            </x-breadcrumbs>

            @if($subcategory)
                <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-xl shadow-lg p-8 mt-8 mb-10">
                    @if(isset($subcategory['sub-category-icon']))
                        <img src="{{ asset('/storage/' . $subcategory->{'sub-category-icon'}) }}" alt="{{ $subcategory->getTranslation('sub-category-name', app()->getLocale()) }}" class="w-36 h-36 object-contain rounded-lg border border-gray-200 shadow-sm bg-white" style="object-fit: contain;">
                    @else
                        <div class="w-36 h-36 flex items-center justify-center bg-gray-100 rounded-lg text-4xl text-gray-400 font-bold border border-gray-200">
                            <span>{{ strtoupper(substr($subcategory->getTranslation('sub-category-name', app()->getLocale()), 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $subcategory->getTranslation('sub-category-name', app()->getLocale()) }}</h1>
                        <p class="text-lg text-gray-600 mb-2">{{ statictranslations('Explore all products from') }} <span class="font-semibold">{{ $subcategory->getTranslation('sub-category-name', app()->getLocale()) }}</span>.</p>
                        @if(!empty($subcategory->description))
                            <p class="text-gray-500">{{ $subcategory->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ statictranslations('Variants') }}</h2>
                    @if($variants && $variants->count())
                        <div class="grid grid-cols-2 gap-3 sm:hidden">
                            @foreach($variants as $variant)
                                @include('client.partials.product-card-mobile', ['product' => $variant])
                            @endforeach
                        </div>
                        <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($variants as $variant)
                                @include('client.partials.product-card', ['product' => $variant])
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $variants->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500 text-lg">
                            {{ statictranslations('No variants found for this subcategory.') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-16 text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ statictranslations('Subcategory Not Found') }}</h1>
                    <p class="text-gray-600 text-lg">{{ statictranslations('The subcategory') }} "{{ Str::title($subcategory_slug) }}" {{ statictranslations('could not be found.') }}</p>
                    <a href="{{ route('subcategories') }}" class="mt-6 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-primary-dark transition">{{ statictranslations('Back to All Subcategories') }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
