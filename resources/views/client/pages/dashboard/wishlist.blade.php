@extends('client.pages.dashboard-layout')

@section('dashboard-title', 'My Wishlist')

@section('dashboard-content')
  @if(isset($wishlist) && $wishlist->count())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
        @endphp

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden group border-2 border-gray-100">
          <a href="{{ $productSlug ? route('products.detail', $productSlug) . ($variantSlug ? '?variant=' . $variantSlug : '') : '#' }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
            <img src="{{ $imageUrl }}" alt="{{ $name }}" class="w-full h-full object-cover">
            <button type="button" data-product-variant-id="{{ $variant?->id }}" data-init-wishlisted="1" class="wishlist-btn absolute top-4 right-4 w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center text-red-500">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
            </button>
          </a>
          <div class="p-4">
            <a href="{{ $productSlug ? route('products.detail', $productSlug) . ($variantSlug ? '?variant=' . $variantSlug : '') : '#' }}"><h3 class="font-semibold text-gray-900">{{ $name }}</h3></a>
            <div class="flex items-center mb-2">
              <div class="flex items-center text-yellow-400 text-sm">★★★★☆</div>
              <span class="ml-2 text-sm text-gray-500">({{ rand(10, 100) }})</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold text-primary">${{ number_format($price, 2) }}</span>
              <form method="post" action="#">
                @csrf
                <input type="hidden" name="product_variant_id" value="{{ $variant?->id }}">
                <button type="button" class="px-4 py-2 bg-primary/10 text-primary font-semibold rounded-lg">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="flex items-center justify-center py-20 bg-white rounded-2xl border-2 border-gray-100">
      <div class="text-center max-w-md">
        <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
          <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Your Wishlist is Empty</h2>
        <p class="text-gray-600 mb-8">Looks like you haven't added anything to your wishlist yet. Browse products and click the heart to save them.</p>
        <a href="{{-- route('products') --}}" class="inline-flex items-center px-8 py-4 bg-primary text-white ...">
          Start Shopping
        </a>
      </div>
    </div>
  @endif
@endsection
