@extends('client.layouts.app')

@section('content')
    @inject('discountService', 'App\Services\DiscountService')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Deals') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('Discover the best deals on our platform.') }}</x-slot:content>
            </x-breadcrumbs>
        </div>

        <div class="container mx-auto px-4 mt-8">
            @php
                // You may need to inject the discountService if not already available
                $discountedProducts = $products->filter(function($product) use ($discountService) {
                    // If $product is a ProductVariant, check for discount
                    if (isset($discountService) && $product instanceof \App\Models\ProductVariant) {
                        $discount = $discountService->calculateProductDiscount($product);
                        return $discount && isset($discount['discount_amount']) && $discount['discount_amount'] > 0;
                    }
                    return false;
                });
            @endphp
            @if($discountedProducts->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($discountedProducts as $product)
                        @include('client.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    No deals found.
                </div>
            @endif
        </div>
    </div>
@endsection
