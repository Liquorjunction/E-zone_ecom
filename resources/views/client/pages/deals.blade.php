@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Deals') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('Discover the best deals on our platform.') }}</x-slot:content>
            </x-breadcrumbs>
        </div>

        <div class="container mx-auto px-4 mt-8">
            @if ($deals->count())
                <div class="grid grid-cols-2 gap-3 sm:hidden">
                    @foreach ($deals as $product)
                        @include('client.partials.product-card-mobile', ['product' => $product])
                    @endforeach
                </div>
                <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($deals as $product)
                        @include('client.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $deals->links() }}
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    {{ statictranslations('No deals found.') }}
                </div>
            @endif
        </div>
    </div>
@endsection
