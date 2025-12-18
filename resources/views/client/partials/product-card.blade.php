@php
  $locale = app()->getLocale() ?? 'en';
  $isVariant = isset($product) && class_basename($product) === 'ProductVariant';
  $master = $isVariant ? $product->product : $product;

  $nameRaw = $master?->getTranslation('name', $locale) ?? '';
  $variantName = $isVariant ? ($product->getTranslation('variant_name', $locale) ?? '') : '';
  $name = trim((is_array($nameRaw) ? implode(' ', array_filter($nameRaw)) : (string) $nameRaw) . ($variantName ? ' - ' . (is_array($variantName) ? implode(' ', $variantName) : $variantName) : ''));

  $catRaw = $master->category?->{'category-name'} ?? $master->category?->name ?? 'Uncategorized';
  $categoryName = is_array($catRaw) ? implode(' ', array_filter($catRaw)) : (string) $catRaw;

  // Image preference order:
  // 1) Variant-assigned image (product_variant_id)
  // 2) Product images that are for the product (product_variant_id IS NULL)
  // 3) Product cover_image
  // 4) Placeholder
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
@endphp

<div class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-gray-100">
  <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}" class="block relative aspect-square bg-gray-100 overflow-hidden">
  <img src="{{ $imagePath ?? 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">

    <!-- Badges -->
    <div class="absolute top-3 left-3 flex flex-col gap-2">
      <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
        -20%
      </span>
      <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
        NEW
      </span>
    </div>

    <!-- Wishlist Button -->
    <button class="absolute top-3 right-3 w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-red-50">
      <svg class="w-5 h-5 text-gray-600 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
    </button>

    <!-- Quick View -->
    <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/50 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
      <button class="w-full px-4 py-2 bg-white text-gray-900 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors">
        Quick View
      </button>
    </div>
  </a>

  <div class="p-5">
    <!-- Category Badge -->
    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">
      {{ $categoryName }}
    </span>

    <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}">
      <h3 class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
        {{ $name }}
      </h3>
    </a>

    <!-- Rating -->
    <div class="flex items-center mt-2">
      <div class="flex items-center text-yellow-400 text-sm">
        ★★★★★
      </div>
      <span class="ml-2 text-sm text-gray-500">(150)</span>
    </div>

    <!-- Features -->
    <ul class="mt-3 space-y-1 text-xs text-gray-600">
      <li class="flex items-center">
        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        Free Shipping
      </li>
      <li class="flex items-center">
        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        1 Year Warranty
      </li>
    </ul>

    <!-- Price and Add to Cart -->
    <div class="mt-4 flex items-center justify-between">
      <div>
        @php
          // Price: prefer variant price if available
          $real = $isVariant ? ($product->price ?? $master->real_price ?? $master->price ?? 0) : ($master->real_price ?? $master->price ?? 0);
          $orig = $isVariant ? ($master->price ?? null) : ($master->price ?? null);
        @endphp
        <p class="text-2xl font-bold text-primary">${{ number_format($real, 2) }}</p>
        @if($orig && $orig > $real)
          <p class="text-sm text-gray-400 line-through">${{ number_format($orig, 2) }}</p>
        @endif
      </div>
      <button class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 group-hover:bg-primary">
        <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </button>
    </div>

    <!-- Stock Status -->
    <div class="mt-3 text-xs text-green-600 font-medium flex items-center">
      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      In Stock
    </div>
  </div>
</div>
