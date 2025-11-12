@extends('client.layouts.app')

@section('content')
    @php
        // FOR DESIGNING: Set this to `[]` to see the empty state.
        $products = [
            [
                'name' => 'Premium Wireless Headphones',
                'image' => 'https://via.placeholder.com/300x300?text=Product+1',
                'price' => '299.99',
                'rating' => 4.5,
                'reviews' => 247,
                'specs' => [
                    'Brand' => 'AudioMax',
                    'Color' => 'Midnight Black',
                    'ANC' => 'Yes',
                    'Battery' => '30 Hours',
                    'Bluetooth' => '5.0',
                    'Weight' => '290g',
                ],
            ],
            [
                'name' => 'Pro Gamer Headset',
                'image' => 'https://via.placeholder.com/300x300?text=Product+2',
                'price' => '199.99',
                'rating' => 4.2,
                'reviews' => 188,
                'specs' => [
                    'Brand' => 'GamerPro',
                    'Color' => 'Carbon Fiber',
                    'ANC' => 'No',
                    'Battery' => 'N/A (Wired)',
                    'Bluetooth' => 'N/A',
                    'Weight' => '350g',
                ],
            ],
        ];

        $features = ['Brand', 'Color', 'ANC', 'Battery', 'Bluetooth', 'Weight'];
    @endphp

    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>Compare Products</x-slot:title>
                <x-slot:content>Compare products side by side.</x-slot:content>
            </x-breadcrumbs>

            @if (!empty($products))
                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 overflow-x-auto">
                    <div class="min-w-max lg:min-w-full">
                        <table class="w-full text-sm text-left">

                            <thead>
                                <tr class="border-b-2 border-gray-100">

                                    <th class="sticky left-0 bg-white p-6 min-w-[200px] z-10">
                                        <h3 class="text-xl font-bold text-gray-900">Features</h3>
                                        <p class="text-gray-500 text-sm">Products ({{ count($products) }} of 4)</p>
                                    </th>

                                    @foreach ($products as $product)
                                        <th class="p-4 w-1/4 min-w-[300px] text-center border-l-2 border-gray-100">
                                            <div class="flex justify-end mb-2">
                                                <button class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <a href="#"
                                                class="block relative aspect-square bg-gray-100 rounded-xl overflow-hidden group mb-4">
                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                                    class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                                            </a>
                                            <a href="#"
                                                class="font-semibold text-gray-900 transition-colors hover:text-primary mb-2 line-clamp-2">
                                                {{ $product['name'] }}
                                            </a>
                                            <div class="flex items-center justify-center text-yellow-400 text-sm my-2">
                                                ★★★★☆ <span
                                                    class="ml-2 text-sm text-gray-500">({{ $product['reviews'] }})</span>
                                            </div>
                                            <p class="text-xl font-bold text-primary mb-3">${{ $product['price'] }}</p>
                                            <button
                                                class="w-full px-4 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                                Add to Cart
                                            </button>
                                        </th>
                                    @endforeach

                                    @for ($i = count($products); $i < 4; $i++)
                                        <th class="p-4 w-1/4 min-w-[300px] text-center border-l-2 border-gray-100">
                                            <div class="flex items-center justify-center h-full">
                                                <a href="{{-- route('products') --}}"
                                                    class="flex flex-col items-center justify-center w-full h-full aspect-square border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:border-primary hover:text-primary transition-colors">
                                                    <svg class="w-16 h-16" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <span class="font-semibold mt-2">Add Product</span>
                                                </a>
                                            </div>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($features as $feature)
                                    <tr class="even:bg-gray-50">
                                        <td
                                            class="sticky left-0 bg-white even:bg-gray-50 p-4 font-semibold text-gray-700 border-b border-gray-100 z-10">
                                            {{ $feature }}
                                        </td>

                                        @foreach ($products as $product)
                                            <td class="p-4 text-gray-900 border-b border-l-2 border-gray-100 text-center">
                                                {{ $product['specs'][$feature] ?? '—' }}
                                            </td>
                                        @endforeach

                                        @for ($i = count($products); $i < 4; $i++)
                                            <td class="p-4 text-gray-400 border-b border-l-2 border-gray-100 text-center">
                                                —
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center py-20">
                    <div class="text-center max-w-md">
                        <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Nothing to Compare</h2>
                        <p class="text-gray-600 mb-8">You haven't added any products to compare. Start browsing and add
                            products to see a side-by-side comparison.</p>
                        <a href="{{-- route('products') --}}"
                            class="inline-flex items-center px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
