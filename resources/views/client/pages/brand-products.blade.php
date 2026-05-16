@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-10">
        <div class="container mx-auto px-4 max-w-7xl">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Shop:') }} {{ $brand ? $brand->name : Str::title($brand_slug) }}</x-slot:title>
                <x-slot:content>{{ statictranslations('All products from') }} {{ $brand ? $brand->name : Str::title($brand_slug) }}.</x-slot:content>
            </x-breadcrumbs>

            @if($brand)
                <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-xl shadow-lg p-8 mt-8 mb-10">
                    @if($brand->image)
                        <img src="{{ asset('storage/' . $brand->image) }}" alt="{{ $brand->name }}" class="w-36 h-36 object-contain rounded-lg border border-gray-200 shadow-sm bg-white" style="object-fit: contain;">
                    @else
                        <div class="w-36 h-36 flex items-center justify-center bg-gray-100 rounded-lg text-4xl text-gray-400 font-bold border border-gray-200">
                            <span>{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $brand->name }}</h1>
                        <p class="text-lg text-gray-600 mb-2">{{ statictranslations('Explore all products from') }} <span class="font-semibold">{{ $brand->name }}</span>.</p>
                        @if(!empty($brand->description))
                            <p class="text-gray-500">{{ $brand->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ statictranslations('Products') }}</h2>
                    @if($products && $products->count())
                        <div class="grid grid-cols-2 gap-3 sm:hidden">
                            @foreach($products as $product)
                                @include('client.partials.product-card-mobile', ['product' => $product])
                            @endforeach
                        </div>
                        <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($products as $product)
                                @include('client.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500 text-lg">
                            {{ statictranslations('No products found for this brand.') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-16 text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ statictranslations('Brand Not Found') }}</h1>
                    <p class="text-gray-600 text-lg">{{ statictranslations('The brand') }} "{{ Str::title($brand_slug) }}" {{ statictranslations('could not be found.') }}</p>
                    <a href="{{ route('brands') }}" class="mt-6 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-primary-dark transition">{{ statictranslations('Back to All Brands') }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
