@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            @php
                $brand = \App\Helpers\ClientHelper::getBrandBySlug($brand_slug);
            @endphp

            <x-breadcrumbs>
                <x-slot:title>Shop: {{ $brand ? $brand->name : Str::title($brand_slug) }}</x-slot:title>
                <x-slot:content>All products from {{ $brand ? $brand->name : Str::title($brand_slug) }}.</x-slot:content>
            </x-breadcrumbs>

            @if($brand)
                <div class="mt-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $brand->name }}</h1>
                    @if($brand->image)
                        <img src="{{ asset('storage/' . $brand->image) }}" alt="{{ $brand->name }}" class="w-32 h-32 object-cover rounded-lg mb-4">
                    @endif
                    <p class="text-gray-600">Explore all products from {{ $brand->name }}.</p>
                </div>
            @else
                <div class="mt-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Brand Not Found</h1>
                    <p class="text-gray-600">The brand "{{ Str::title($brand_slug) }}" could not be found.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
