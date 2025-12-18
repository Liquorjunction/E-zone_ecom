@php
    // --- Data Preparation (supports Product or ProductVariant) ---
    $locale = app()->getLocale() ?? 'en';
    $isVariant = isset($product) && class_basename($product) === 'ProductVariant';
    $master = $isVariant ? $product->product : $product;

    $nameRaw = $master?->getTranslation('name', $locale) ?? '';
    $variantName = $isVariant ? ($product->getTranslation('variant_name', $locale) ?? '') : '';
    $name = is_array($nameRaw) ? implode(' ', array_filter($nameRaw)) : (string) ($nameRaw ?? '');
    if ($variantName) {
        $v = is_array($variantName) ? implode(' ', $variantName) : $variantName;
        $name .= ' - ' . $v;
    }

    // Description handling
    $descRaw = $master?->getTranslation('description', $locale) ?? '';
    if (is_array($descRaw)) {
        $descRaw = implode(' ', array_map(function($v){ return is_array($v) ? implode(' ', $v) : $v; }, $descRaw));
    }
    $description = Str::limit(strip_tags((string) $descRaw), 120);

    // Image Logic (prefer variant images, then product images, then cover, then placeholder)
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
        $imagePath = 'https://via.placeholder.com/400x400?text=No+Image';
    }

    // Price & Math
    $price = (float) ($master->price ?? 0);
    $realPrice = (float) ($isVariant ? ($product->price ?? $master->real_price ?? $price) : ($master->real_price ?? $price));
    $discount = $price > $realPrice && $price > 0 ? round((($price - $realPrice) / $price) * 100) : 0;

    // Category
    $catRaw = $master->category?->name ?? 'General';
    $categoryName = is_array($catRaw) ? implode(' ', array_filter($catRaw)) : (string) $catRaw;
@endphp

<div class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-primary transition-all duration-300 overflow-hidden">
    <div class="flex flex-col md:flex-row h-full">

        <div class="w-full md:w-64 md:shrink-0 bg-gray-50 relative p-4 flex items-center justify-center overflow-hidden">
            <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}" class="block w-full h-48 md:h-full">
                <img src="{{ $imagePath }}"
                     alt="{{ $name }}"
                     class="w-full h-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105">
            </a>

            <div class="absolute top-3 left-3 flex flex-col gap-2">
                @if($discount > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        -{{ $discount }}%
                    </span>
                @endif
                @if($product->is_bestseller)
                    <span class="bg-yellow-400 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        HOT
                    </span>
                @endif
            </div>
        </div>

        <div class="flex-1 p-5 md:p-6 flex flex-col border-b md:border-b-0 md:border-r border-gray-100">

            <div class="flex items-center justify-between mb-2">
                     <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}"
                         class="text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-primary transition">
                    {{ $categoryName }}
                </a>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-xs font-medium text-gray-600">4.5</span>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                <a href="{{ route('products.detail', $master->slug) }}{{ $isVariant ? '?variant=' . ($product->slug ?? '') : '' }}">
                    {{ $name }}
                </a>
            </h3>

            <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                {{ $description }}
            </p>
        </div>

        <div class="w-full md:w-56 bg-gray-50/50 p-5 md:p-6 flex flex-col justify-center items-center md:items-end gap-4 border-t md:border-t-0">

            <div class="text-center md:text-right">
                @if($discount > 0)
                    <div class="text-sm text-gray-400 line-through mb-0.5">${{ number_format($price, 2) }}</div>
                @endif
                <div class="text-2xl font-bold text-primary">
                    ${{ number_format($realPrice, 2) }}
                </div>
                <div class="text-xs text-green-600 font-medium mt-1 flex items-center justify-end gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> In Stock
                </div>
            </div>

            <div class="w-full space-y-2">
                <button class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Add to Cart
                </button>

                <div class="flex gap-2">
                    <button class="flex-1 bg-white border border-gray-300 text-gray-700 hover:text-primary hover:border-primary py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-1" title="Add to Wishlist">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        Save
                    </button>
                    <button class="flex-1 bg-white border border-gray-300 text-gray-700 hover:text-primary hover:border-primary py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-1" title="Quick View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        View
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
