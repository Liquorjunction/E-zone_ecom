@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            {!! Breadcrumbs::render('products.detail', $product) !!}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 overflow-hidden">
                        <div class="aspect-square relative">
                            <img src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}" class="w-full h-full object-cover">
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($product->created_at > now()->subDays(30))
                                    <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                        NEW
                                    </span>
                                @endif
                            </div>
                            <button
                                class="absolute top-4 right-4 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-red-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-600 hover:text-red-500 transition-colors" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        @if($product->images && $product->images->count() > 0)
                            @foreach($product->images->take(4) as $image)
                                <button
                                    class="bg-white rounded-xl shadow-sm border-2 border-gray-100 overflow-hidden hover:border-primary transition-colors focus:border-primary focus:ring-2 focus:ring-primary/50">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }} - {{ $loop->iteration }}" class="w-full h-24 object-cover">
                                </button>
                            @endforeach
                        @else
                            <button
                                class="bg-white rounded-xl shadow-sm border-2 border-gray-100 overflow-hidden hover:border-primary transition-colors focus:border-primary focus:ring-2 focus:ring-primary/50">
                                <img src="{{ $product->cover_image ? asset('storage/' . $product->cover_image) : asset('images/placeholder-product.jpg') }}"
                                    alt="{{ $product->name }}" class="w-full h-24 object-cover">
                            </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-4">
                            {{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}
                        </h1>
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex items-center text-yellow-400 text-lg">
                                ★★★★★
                            </div>
                            <span class="text-gray-600">(0 reviews)</span>
                            <span class="text-green-600 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                In Stock
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold text-primary">${{ number_format($product->price, 2) }}</span>
                    </div>

                    {{-- Categories and Tags Chips --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Category:</span>
                        @if($product->category)
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ $product->category->{'category-name'} }}
                            </a>
                        @endif
                        @if($product->subCategory)
                            <a href="{{ route('products.index', ['subcategory' => $product->subCategory->slug]) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ $product->subCategory->{'sub-category-name'} }}
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Brand:</span>
                        @if($product->brand)
                            <a href="{{ route('brand-products', $product->brand->slug) }}" class="text-sm text-gray-600 hover:text-primary transition-colors">
                                {{ $product->brand->name }}
                            </a>
                        @endif
                    </div>

                    {{-- Information About Product and more infos --}}
                    <div class="space-y-2">
                        <h2 class="text-lg font-bold text-gray-700 mb-2">Description:</h2>
                        <div class="text-gray-600 prose max-w-none">
                            {!! is_array($product->description) ? ($product->description[app()->getLocale()] ?? $product->description['en'] ?? '') : $product->description !!}
                        </div>
                    </div>

                    {{-- Product Features --}}
                    @php
                        // Normalize specifications and dimensions: they may be cast as array, or stored as JSON string.
                        $specifications = $product->specifications;
                        if (is_string($specifications)) {
                            $decoded = json_decode($specifications, true);
                            $specifications = is_array($decoded) ? $decoded : null;
                        }
                        $dimensions = $product->dimensions;
                        if (is_string($dimensions)) {
                            $decoded = json_decode($dimensions, true);
                            $dimensions = is_array($decoded) ? $decoded : null;
                        }
                    @endphp

                    @if(!empty($specifications))
                        <div class="space-y-2">
                            <h2 class="text-lg font-bold text-gray-700 mb-2">Specifications:</h2>
                            <div class="text-gray-600">
                                @foreach($specifications as $key => $value)
                                    <div class="flex justify-between py-1">
                                        <span class="font-medium">{{ ucfirst($key) }}:</span>
                                        <span>{{ is_array($value) ? ($value[app()->getLocale()] ?? $value['en'] ?? '') : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Color --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <div class="flex items-center space-x-3">
                            <label class="relative">
                                <input type="radio" name="color" value="black" class="peer sr-only" checked>
                                <span
                                    class="w-8 h-8 rounded-full bg-gray-900 block cursor-pointer ring-2 ring-transparent peer-checked:ring-primary ring-offset-2 transition-all"></span>
                            </label>
                            <label class="relative">
                                <input type="radio" name="color" value="white" class="peer sr-only">
                                <span
                                    class="w-8 h-8 rounded-full bg-white border border-gray-200 block cursor-pointer ring-2 ring-transparent peer-checked:ring-primary ring-offset-2 transition-all"></span>
                            </label>
                            <label class="relative">
                                <input type="radio" name="color" value="blue" class="peer sr-only">
                                <span
                                    class="w-8 h-8 rounded-full bg-blue-600 block cursor-pointer ring-2 ring-transparent peer-checked:ring-primary ring-offset-2 transition-all"></span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center border border-gray-300 rounded-lg w-fit">
                            <button class="w-12 h-12 text-gray-600 hover:text-primary transition-colors">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="text" value="1"
                                class="w-12 text-center text-lg font-medium text-gray-900 border-x border-gray-300 focus:outline-none"
                                readonly>
                            <button class="w-12 h-12 text-gray-600 hover:text-primary transition-colors">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <button
                            class="flex-1 w-full bg-primary text-white text-lg font-semibold py-3.5 px-6 rounded-lg shadow-lg hover:bg-primary-dark transition-all duration-300 transform hover:-translate-y-0.5">
                            Add to Cart
                        </button>
                        <button
                            class="flex-1 w-full bg-gray-800 text-white text-lg font-semibold py-3.5 px-6 rounded-lg shadow-lg hover:bg-black transition-all duration-300 transform hover:-translate-y-0.5">
                            Buy Now
                        </button>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 mb-12">
                <div class="border-b border-gray-100">
                    <nav class="flex">
                        <button
                            class="tab-button active px-8 py-4 text-gray-700 font-semibold border-b-2 border-primary transition-colors"
                            data-tab="description">
                            Description
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="specifications">
                            Specifications
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="reviews">
                            Reviews (247)
                        </button>
                        <button
                            class="tab-button px-8 py-4 text-gray-500 hover:text-gray-700 font-semibold transition-colors"
                            data-tab="shipping">
                            Shipping & Returns
                        </button>
                    </nav>
                </div>

                <div class="p-8">
                    <div id="description-tab" class="tab-content active">
                        <div class="prose max-w-none">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ is_array($product->name) ? ($product->name[app()->getLocale()] ?? $product->name['en'] ?? '') : $product->name }}</h3>
                            <div class="text-gray-700 mb-6 leading-relaxed">
                                {!! is_array($product->description) ? ($product->description[app()->getLocale()] ?? $product->description['en'] ?? '') : $product->description !!}
                            </div>

                            @if($product->specifications)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Specifications:</h4>
                                        <div class="space-y-2 text-gray-700">
                                                @foreach($product->specifications as $key => $value)
                                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                                        <span class="font-medium">{{ ucfirst($key) }}:</span>
                                                        <span>{{ is_array($value) ? ($value[app()->getLocale()] ?? $value['en'] ?? '') : $value }}</span>
                                                    </div>
                                                @endforeach
                                        </div>
                                    </div>
                                    @if(!empty($dimensions))
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 mb-3">Dimensions:</h4>
                                            <div class="space-y-2 text-gray-700">
                                                @foreach($dimensions as $key => $value)
                                                    <div class="flex justify-between py-1 border-b border-gray-100">
                                                        <span class="font-medium">{{ ucfirst($key) }}:</span>
                                                        <span>{{ is_array($value) ? ($value[app()->getLocale()] ?? $value['en'] ?? '') : $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($product->warranty)
                                <div class="mt-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Warranty:</h4>
                                    <div class="text-gray-700">
                                        {!! is_array($product->warranty) ? ($product->warranty[app()->getLocale()] ?? $product->warranty['en'] ?? '') : $product->warranty !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="specifications-tab" class="tab-content hidden">
                        @if(!empty($specifications) || !empty($dimensions))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                @if(!empty($specifications))
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Specifications</h4>
                                        <table class="w-full text-sm">
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($specifications as $key => $value)
                                                    <tr class="py-2">
                                                        <td class="py-3 text-gray-600 font-medium">{{ ucfirst($key) }}</td>
                                                        <td class="py-3 text-gray-900">{{ is_array($value) ? ($value[app()->getLocale()] ?? $value['en'] ?? '') : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                @if(!empty($dimensions))
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Dimensions</h4>
                                        <table class="w-full text-sm">
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($dimensions as $key => $value)
                                                    <tr class="py-2">
                                                        <td class="py-3 text-gray-600 font-medium">{{ ucfirst($key) }}</td>
                                                        <td class="py-3 text-gray-900">{{ is_array($value) ? ($value[app()->getLocale()] ?? $value['en'] ?? '') : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                No specifications available for this product.
                            </div>
                        @endif
                    </div>

                    <div id="reviews-tab" class="tab-content hidden">
                        <div class="space-y-8">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-semibold text-gray-900">Customer Reviews</h4>
                                    <button
                                        class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-dark transition-colors">
                                        Write a Review
                                    </button>
                                </div>
                                <div class="flex items-center space-x-8">
                                    <div class="text-center">
                                        <div class="text-4xl font-bold text-gray-900">4.8</div>
                                        <div class="flex items-center justify-center text-yellow-400 text-lg mb-1">
                                            ★★★★★
                                        </div>
                                        <div class="text-sm text-gray-600">247 reviews</div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">5★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 75%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">186</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">4★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 20%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">49</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">3★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 3%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">8</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">2★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">3</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-sm w-8">1★</span>
                                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ substr(['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">
                                                        {{ ['John D.', 'Sarah M.', 'Mike R.', 'Emma L.', 'Alex K.'][$i - 1] }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ ['2 weeks ago', '1 month ago', '3 weeks ago', '1 week ago', '2 days ago'][$i - 1] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center text-yellow-400">
                                                ★★★★★
                                            </div>
                                        </div>
                                        <h5 class="font-semibold text-gray-900 mb-2">
                                            {{ ['Excellent Sound Quality', 'Perfect for Workouts', 'Great Battery Life', 'Comfortable for Long Sessions', 'Worth Every Penny'][$i - 1] }}
                                        </h5>
                                        <p class="text-gray-700 leading-relaxed">
                                            {{ [
                                                'These headphones exceeded my expectations. The sound quality is incredible and the noise cancellation works perfectly for my daily commute.',
                                                'I use these for gym sessions and they stay put perfectly. The battery lasts all day and the sound is crystal clear.',
                                                '30 hours of battery life is no joke! I can go days without charging. The build quality feels premium.',
                                                'Super comfortable even after wearing for 8+ hours. The leather cushions are soft and the fit is perfect.',
                                                'Best headphones I\'ve ever owned. The audio quality, comfort, and features make them worth every cent.',
                                            ][$i - 1] }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                Helpful ({{ rand(5, 25) }})
                                            </span>
                                            <button class="text-primary hover:underline">Report</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="text-center">
                                <button
                                    class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                    Load More Reviews
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="shipping-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Shipping Information
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Free Shipping</div>
                                            <div class="text-sm text-gray-600">Orders over $50 qualify for free standard
                                                shipping</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Express Delivery</div>
                                            <div class="text-sm text-gray-600">1-2 business days - $9.99</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Same Day Delivery</div>
                                            <div class="text-sm text-gray-600">Available in select areas - $14.99</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                                    </svg>
                                    Returns & Exchanges
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">30-Day Returns</div>
                                            <div class="text-sm text-gray-600">Return any item within 30 days for a full
                                                refund</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Free Exchanges</div>
                                            <div class="text-sm text-gray-600">Exchange for a different size or color at no
                                                cost</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Easy Process</div>
                                            <div class="text-sm text-gray-600">Print return label or drop off at any
                                                location</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts && $relatedProducts->count() > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">You Might Also Like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-gray-100">
                                <a href="{{ route('products.detail', $relatedProduct->slug) }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
                                    <img src="{{ $relatedProduct->cover_image ? asset('storage/' . $relatedProduct->cover_image) : asset('images/placeholder-product.jpg') }}"
                                        alt="{{ is_array($relatedProduct->name) ? ($relatedProduct->name[app()->getLocale()] ?? $relatedProduct->name['en'] ?? '') : $relatedProduct->name }}"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                                    @if($relatedProduct->created_at > now()->subDays(30))
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                NEW
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-4">
                                    <a href="{{ route('products.detail', $relatedProduct->slug) }}">
                                        <h3 class="font-semibold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary mb-2">
                                            {{ is_array($relatedProduct->name) ? ($relatedProduct->name[app()->getLocale()] ?? $relatedProduct->name['en'] ?? '') : $relatedProduct->name }}
                                        </h3>
                                    </a>

                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center text-yellow-400 text-sm">
                                            ★★★★☆
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">(0)</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-lg font-bold text-primary">${{ number_format($relatedProduct->price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.dataset.tab;

                    // Deactivate all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-primary', 'text-gray-700');
                        btn.classList.add('text-gray-500');
                    });

                    // Activate clicked button
                    button.classList.add('active', 'border-primary', 'text-gray-700');
                    button.classList.remove('text-gray-500');

                    // Hide all content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    });

                    // Show target content
                    const targetContent = document.getElementById(targetTab + '-tab');
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                        targetContent.classList.add('active');
                    }
                });
            });
        });
    </script>
@endpush
