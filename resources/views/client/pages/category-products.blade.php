@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-7xl">

            <x-breadcrumbs :category="$category">
                <x-slot:title>{{ statictranslations('Shop:') }} {{$category->category_name}}</x-slot:title>
                <x-slot:content>{{ statictranslations('All products from') }} {{$category->category_name}}.</x-slot:content>
            </x-breadcrumbs>

            @if($category)
                <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-xl shadow-lg p-8 mt-8 mb-10">
                    @if(isset($category['category-icon']))
                        <img src="{{ asset('/storage/' . $category->{'category-icon'}) }}" alt="{{ $category->getTranslation('category-name', app()->getLocale()) }}" class="w-36 h-36 object-contain rounded-lg border border-gray-200 shadow-sm bg-white" style="object-fit: contain;">
                    @else
                        <div class="w-36 h-36 flex items-center justify-center bg-gray-100 rounded-lg text-4xl text-gray-400 font-bold border border-gray-200">
                            <span>{{ strtoupper(substr($category->getTranslation('category-name', app()->getLocale()), 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $category->getTranslation('category-name', app()->getLocale()) }}</h1>
                        <p class="text-lg text-gray-600 mb-2">{{ statictranslations('Explore all products from') }} <span class="font-semibold">{{ $category->getTranslation('category-name', app()->getLocale()) }}</span>.</p>
                        @if(!empty($category->description))
                            <p class="text-gray-500">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ statictranslations('Products') }}</h2>
                    @if($products && $products->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach($products as $product)
                                @include('client.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500 text-lg">
                            {{ statictranslations('No products found for this category.') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-16 text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ statictranslations('Category Not Found') }}</h1>
                    <p class="text-gray-600 text-lg">{{ statictranslations('The category') }} "{{ Str::title($category_slug) }}" {{ statictranslations('could not be found.') }}</p>
                    <a href="{{ route('categories') }}" class="mt-6 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-primary-dark transition">{{ statictranslations('Back to All Categories') }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
