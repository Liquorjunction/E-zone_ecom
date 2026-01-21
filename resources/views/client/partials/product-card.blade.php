@php
  $locale = app()->getLocale() ?? 'en';
  $isVariant = isset($product) && class_basename($product) === 'ProductVariant';
  $master = $isVariant ? $product->product : $product;

  $nameRaw = $master?->getTranslation('name', $locale) ?? '';
  $variantName = $isVariant ? ($product->getTranslation('variant_name', $locale) ?? '') : '';
  $name = trim((is_array($nameRaw) ? implode(' ', array_filter($nameRaw)) : (string) $nameRaw) . ($variantName ? ' - ' . (is_array($variantName) ? implode(' ', $variantName) : $variantName) : ''));

  $catRaw = $master->category?->{'category-name'} ?? $master->category?->name ?? 'Uncategorized';
  $categoryName = is_array($catRaw) ? implode(' ', array_filter($catRaw)) : (string) $catRaw;

  // Image preference order
  $variantImage = null;
  if ($isVariant) {
    $variantImage = optional($product->images()->ordered()->first())->image_path;
  }

  $productImage = optional($master->images()->forProduct($master->id)->ordered()->first())->image_path;

  if ($variantImage) {
    $imagePath = asset('storage/' . $variantImage);
  } elseif ($productImage) {
    $imagePath = asset('storage/' . $productImage);
  } elseif ($master->cover_image) {
    $imagePath = asset('storage/' . $master->cover_image);
  } else {
    $imagePath = null;
  }

  // Get discount info
  $real = $isVariant ? ($product->price ?? $master->real_price ?? $master->price ?? 0) : ($master->real_price ?? $master->price ?? 0);
  $itemDiscountInfo = $isVariant ? $product->getDiscountInfo() : $master->getDiscountInfo();
  $productDiscountInfo = $master->getDiscountInfo();

  // Wishlist data
  $variantId = $isVariant ? ($product->id ?? null) : ($master->productVariants()->ordered()->pluck('id')->first() ?? null);
  $isWishlisted = isset($wishlistVariantIds) && $variantId ? in_array($variantId, $wishlistVariantIds) : false;

  // Bestseller check
  $isBestseller = false;
  if ($isVariant) {
    $isBestseller = (bool) $product->is_bestseller;
  } else {
    $firstVariant = $master->productVariants?->first();
    $isBestseller = $firstVariant && (bool) $firstVariant->is_bestseller;
  }
@endphp

<div class="group bg-white rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl border-2 border-gray-100 hover:border-primary/20">
  <!-- Image Container -->
  <div class="relative aspect-square bg-gray-100 overflow-hidden">
    <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}" class="block w-full h-full">
      <img
        src="{{ $imagePath ?? 'https://via.placeholder.com/400x400?text=No+Image' }}"
        alt="{{ $name }}"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        loading="lazy"
      >
    </a>

    <!-- Gradient Overlay on Hover -->
    <div class="absolute inset-0 bg-linear-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

    <!-- Badges -->
    @if($productDiscountInfo['has_discount'] || $master->created_at > now()->subDays(30) || $isBestseller)
      <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
        @if($productDiscountInfo['has_discount'])
          <span class="inline-flex items-center gap-1 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
            </svg>
            @if($productDiscountInfo['discount_percentage'] > 0)
              {{ number_format($productDiscountInfo['discount_percentage'], 0) }}% OFF
            @else
              ${{ number_format($productDiscountInfo['discount_amount'], 2) }} OFF
            @endif
          </span>
        @endif

        @if($master->created_at > now()->subDays(30))
          <span class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            NEW
          </span>
        @endif

        @if($isBestseller)
          <span class="inline-flex items-center gap-1 bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            BESTSELLER
          </span>
        @endif
      </div>
    @endif

    <!-- Wishlist Button -->
    <button
      type="button"
      data-product-variant-id="{{ $variantId }}"
      data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
      data-toast-ignore
      class="wishlist-btn no-toast absolute top-3 right-3 w-10 h-10 rounded-full bg-white/95 backdrop-blur-sm shadow-lg flex items-center justify-center transition-all duration-300 hover:bg-white hover:scale-110 z-10 {{ $isWishlisted ? 'opacity-100 is-wishlisted' : 'opacity-0 group-hover:opacity-100' }}"
      @if(!$variantId) disabled @endif
    >
      <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500 fill-current' : 'text-gray-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
      </svg>
    </button>

    <!-- Quick View - Professional Style -->
    <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
      <button class="w-full px-4 py-2.5 bg-white/95 backdrop-blur-sm text-gray-900 rounded-lg font-semibold text-sm hover:bg-white transition-all duration-200 shadow-lg flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Quick View
      </button>
    </div>
  </div>

  <!-- Product Info -->
  <div class="p-5">
    <!-- Category Badge -->
    <div class="flex items-center justify-between mb-3">
      <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20">
        {{ $categoryName }}
      </span>

      <!-- Stock Indicator -->
      @php
        $stock = $isVariant
          ? ($product->getTotalStockAttribute() ?? 0)
          : ($master->productVariants()->sum('inventory.quantity') ?? 0);
      @endphp
      <div class="flex items-center gap-1.5 text-xs font-medium {{ $stock > 0 ? 'text-green-600' : 'text-red-600' }}">
        <span class="flex h-2 w-2 relative">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $stock > 0 ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 {{ $stock > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
        </span>
        {{ $stock > 0 ? 'In Stock' : 'Out of Stock' }}
        @if($stock > 0)
          <span class="ml-1 text-gray-400">({{ $stock }})</span>
        @endif
      </div>
    </div>

    <!-- Product Title -->
    <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}">
      <h3 class="text-sm font-bold text-gray-900 line-clamp-2 mb-3 min-h-14 group-hover:text-primary transition-colors leading-snug">
        {{ $name }}
      </h3>
    </a>

    <!-- Rating -->
    <div class="flex items-center gap-2 mb-4">
      <div class="flex items-center">
        @for($i = 0; $i < 5; $i++)
          <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        @endfor
      </div>
      <span class="text-sm text-gray-600 font-medium">4.8 <span class="text-gray-400">(150)</span></span>
    </div>

    <!-- Features -->
    <div class="flex gap-2 mb-4 pb-4 border-b border-gray-100">
      <div class="flex items-center gap-1.5 text-xs text-gray-600">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">Free Shipping</span>
      </div>
      <div class="flex items-center gap-1.5 text-xs text-gray-600">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">1 Year Warranty</span>
      </div>
    </div>

    <!-- Price and Add to Cart -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex-1">
        @if($itemDiscountInfo['has_discount'])
          <div class="flex flex-col">
            <div>
              <span class="inline-block text-xs text-gray-400 line-through opacity-75">
                {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['original_price'], 2) }}
              </span>
            </div>
            <span class="text-2xl font-bold text-red-600">
              {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
            </span>
          </div>
        @else
          <span class="text-2xl font-bold text-primary">
            {{ config('app.CURRENCY_SYMBOL') }}{{ number_format($itemDiscountInfo['final_price'], 2) }}
          </span>
        @endif
      </div>

      <button class="shrink-0 w-full sm:w-12 h-12 rounded-xl bg-primary shadow-lg shadow-primary/30 flex items-center justify-center transition-all duration-300 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/40 hover:scale-105 active:scale-95 order-2 sm:order-none mt-3 sm:mt-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
      </button>
    </div>
  </div>
</div>
