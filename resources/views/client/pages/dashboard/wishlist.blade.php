@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('My Wishlist'))

@section('dashboard-content')
  @if(isset($wishlist) && $wishlist->count())
    {{-- Wishlist Header with Count --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 lg:mb-6 p-4 lg:p-6 bg-white rounded-xl lg:rounded-2xl shadow-sm border-2 border-gray-100">
      <div>
        <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ statictranslations('Your Wishlist') }}</h2>
        <p class="text-sm text-gray-600 mt-1"><span id="wishlist-count">{{ $wishlist->count() }}</span> {{ statictranslations('item') }}{{ $wishlist->count() > 1 ? 's' : '' }} {{ statictranslations('saved') }}</p>
      </div>
    </div>

    {{-- Wishlist Grid --}}
    <div id="wishlist-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4">
      @foreach($wishlist as $item)
        @php
          $variant = $item->productVariant ?? null;
          $master = $variant?->product ?? null;
          $locale = app()->getLocale() ?? 'en';
          $name = $master?->getTranslation('name', $locale) ?? ($variant?->getTranslation('variant_name', $locale) ?? ($variant->name ?? 'Product'));
          $variantSlug = $variant?->slug ?? null;
          $productSlug = $master?->slug ?? null;
          $image = optional($variant?->images()->ordered()->first())->image_path ?? optional($master?->images()->forProduct($master?->id)->ordered()->first())->image_path ?? $master?->cover_image ?? null;
          $imageUrl = $image ? asset('storage/' . $image) : 'https://via.placeholder.com/300x300?text=No+Image';
          $price = $variant?->price ?? $master?->real_price ?? $master?->price ?? 0;
          $originalPrice = ($master?->price ?? 0) > $price ? $master?->price : null;
        @endphp

        <div data-wishlist-item="{{ $variant?->id }}" class="bg-white rounded-xl lg:rounded-2xl shadow-sm overflow-hidden group border-2 border-gray-100 hover:shadow-lg hover:border-primary/20 transition-all duration-300">
          {{-- Product Image with Wishlist Button --}}
          <div class="relative aspect-square bg-gray-50 overflow-hidden">
            <a href="{{ $productSlug ? route('products.detail', $productSlug) . ($variantSlug ? '?variant=' . $variantSlug : '') : '#' }}">
              <img
                src="{{ $imageUrl }}"
                alt="{{ $name }}"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              >
            </a>

            {{-- Wishlist Remove Button --}}
            <button
              type="button"
              data-product-variant-id="{{ $variant?->id }}"
              data-init-wishlisted="1"
              data-toast-ignore
              aria-pressed="t   rue"
              class="wishlist-btn no-toast is-wishlisted opacity-100 absolute top-2 right-2 w-8 h-8 lg:w-10 lg:h-10 rounded-full bg-white/90 backdrop-blur-sm shadow-lg flex items-center justify-center text-red-500 hover:bg-white hover:scale-110 transition-all z-10"
              title="{{ statictranslations('Remove from wishlist') }}"
            >
              <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
              </svg>
            </button>

            {{-- Discount Badge (if applicable) --}}
            @if($originalPrice && $originalPrice > $price)
              @php
                $discount = round((($originalPrice - $price) / $originalPrice) * 100);
              @endphp
              <div class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                -{{ $discount }}%
              </div>
            @endif

            {{-- Quick View Overlay --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
              <a
                href="{{ $productSlug ? route('products.detail', $productSlug) . ($variantSlug ? '?variant=' . $variantSlug : '') : '#' }}"
                class="px-4 py-2 bg-white text-gray-900 font-semibold rounded-lg text-sm opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300"
              >
                {{ statictranslations('Quick View') }}
              </a>
            </div>
          </div>

          {{-- Product Info --}}
          <div class="p-3 lg:p-4">
            <a href="{{ $productSlug ? route('products.detail', $productSlug) . ($variantSlug ? '?variant=' . $variantSlug : '') : '#' }}">
              <h3 class="font-semibold text-gray-900 text-xs lg:text-sm line-clamp-2 mb-2 min-h-10 lg:min-h-[2.8rem] group-hover:text-primary transition-colors">
                {{ $name }}
              </h3>
            </a>

            {{-- Rating --}}
            <div class="flex items-center mb-2 lg:mb-3">
              <div class="flex items-center text-yellow-400 text-xs lg:text-sm">
                ★★★★☆
              </div>
              <span class="ml-1.5 text-xs text-gray-500">({{ rand(10, 100) }})</span>
            </div>

            {{-- Price --}}
            <div class="flex items-baseline gap-2 mb-3">
              <span class="text-base lg:text-lg font-bold text-primary">
                {{ money_format_number($price) }}
              </span>
              @if($originalPrice && $originalPrice > $price)
                <span class="text-xs lg:text-sm text-gray-400 line-through">
                  {{ money_format_number($originalPrice) }}
                </span>
              @endif
            </div>

            {{-- Add to Cart Button --}}
            <form method="post" action="#" class="w-full ajax-add-to-cart">
              @csrf
              <input type="hidden" name="product_variant_id" value="{{ $variant?->id }}">
              <button
                type="submit"
                class="w-full px-3 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-all text-xs lg:text-sm flex items-center justify-center gap-2 group/btn"
              >
                <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ statictranslations('Add to Cart') }}
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if(method_exists($wishlist, 'hasPages') && $wishlist->hasPages())
      <div class="mt-6 flex justify-center">
        {{ $wishlist->links() }}
      </div>
    @endif

    {{-- Hidden empty-template for client-side replacement when last item removed --}}
    <template id="wishlist-empty-template">
      <div class="flex items-center justify-center py-16 lg:py-24 bg-white rounded-xl lg:rounded-2xl border-2 border-gray-100">
        <div class="text-center max-w-md px-6">
          <div class="relative w-32 h-32 lg:w-40 lg:h-40 mx-auto mb-6 lg:mb-8">
            <div class="absolute inset-0 rounded-full bg-linear-to-br from-red-50 to-pink-50 animate-pulse"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <svg class="w-16 h-16 lg:w-20 lg:h-20 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
          </div>

          <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3 lg:mb-4">
            {{ statictranslations('Your Wishlist is Empty') }}
          </h2>
          <p class="text-sm lg:text-base text-gray-600 mb-6 lg:mb-8 leading-relaxed">
            {{ statictranslations('Save your favorite items here so you can easily find them later. Start exploring and add products you love!') }}
          </p>

          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a
              href="{{ route('products.index') }}"
              class="inline-flex items-center justify-center px-6 lg:px-8 py-3 lg:py-4 bg-primary text-white font-semibold rounded-xl shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all text-sm lg:text-base group"
            >
              <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              {{ statictranslations('Discover Products') }}
            </a>
          </div>
        </div>
      </div>
    </template>

  @else
    {{-- Empty Wishlist State --}}
    <div class="flex items-center justify-center py-16 lg:py-24 bg-white rounded-xl lg:rounded-2xl border-2 border-gray-100">
      <div class="text-center max-w-md px-6">
        {{-- Animated Heart Icon --}}
        <div class="relative w-32 h-32 lg:w-40 lg:h-40 mx-auto mb-6 lg:mb-8">
          <div class="absolute inset-0 rounded-full bg-linear-to-br from-red-50 to-pink-50 animate-pulse"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-16 h-16 lg:w-20 lg:h-20 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </div>
        </div>

        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3 lg:mb-4">
          {{ statictranslations('Your Wishlist is Empty') }}
        </h2>
        <p class="text-sm lg:text-base text-gray-600 mb-6 lg:mb-8 leading-relaxed">
          {{ statictranslations('Save your favorite items here so you can easily find them later. Start exploring and add products you love!') }}
        </p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <a
            href="{{ route('products.index') }}"
            class="inline-flex items-center justify-center px-6 lg:px-8 py-3 lg:py-4 bg-primary text-white font-semibold rounded-xl shadow-lg hover:bg-primary/90 hover:shadow-xl transition-all text-sm lg:text-base group"
          >
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            {{ statictranslations('Discover Products') }}
          </a>
        </div>
      </div>
    </div>
  @endif
@endsection
