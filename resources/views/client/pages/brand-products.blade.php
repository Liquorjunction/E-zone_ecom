@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>Shop: {{ Str::title($brand_slug) }}</x-slot:title>
                <x-slot:content>All products from {{ Str::title($brand_slug) }}.</x-slot:content>
            </x-breadcrumbs>
        </div>
    </div>
@endsection
