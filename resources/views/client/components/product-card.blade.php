<div class="group bg-white rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary/30 block relative">
    <a href="{{ $url ?? 'javascript:void(0);' }}" class="block">
        <div class="aspect-square overflow-hidden bg-gray-50">
            <img src="{{ asset($image) }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute top-3 right-3 {{ $badgeColor }} text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">
                {{ $badge }}
            </div>
        </div>
    </a>
    {{-- Wishlist button moved outside anchor to prevent navigation on click --}}
        @if(isset($variant_id) || isset($product_id))
        @php
            $wid = $variant_id ?? $product_id ?? '';
            $isWishlisted = false;
            if (isset($wishlistVariantIds) && $wid) {
                // normalize to array safely (handle Collection or plain array)
                if (is_array($wishlistVariantIds)) {
                    $wishArr = $wishlistVariantIds;
                } elseif (is_object($wishlistVariantIds) && method_exists($wishlistVariantIds, 'toArray')) {
                    $wishArr = $wishlistVariantIds->toArray();
                } else {
                    $wishArr = (array) $wishlistVariantIds;
                }

                $isWishlisted = in_array($wid, $wishArr);
            }
        @endphp
        <button type="button" data-product-variant-id="{{ $wid }}" data-init-wishlisted="{{ $isWishlisted ? '1' : '0' }}" data-toast-ignore class="wishlist-btn no-toast absolute top-3 left-3 w-9 h-9 bg-white rounded-full flex items-center justify-center transition-opacity shadow-lg hover:bg-primary hover:text-white {{ $isWishlisted ? 'opacity-100 is-wishlisted' : 'opacity-0 group-hover:opacity-100' }}" @if(empty($wid)) disabled @endif>
            <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500' : 'text-gray-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    @endif
    <div class="p-4">
        <span class="inline-block text-xs font-medium text-primary bg-primary/10 px-2 py-1 rounded">{{ $category }}</span>
        <h3 class="mt-2 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors min-h-[40px]">
            <a href="{{ $url ?? 'javascript:void(0);' }}" class="inline-block w-full">{{ $title }}</a>
        </h3>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-lg font-bold text-gray-900">{{ $price }}</span>
            @if(isset($oldPrice))
                <span class="text-sm text-gray-400 line-through">{{ $oldPrice }}</span>
            @endif
        </div>

        {{-- If product/variant ids are provided, render a form that posts to cart.add, otherwise render a non-navigating button --}}
        @if(isset($product_id) || isset($variant_id))
            <form method="POST" action="{{ route('cart.add') }}" class="mt-3 ajax-add-to-cart"
                  data-product-id="{{ $product_id ?? '' }}" data-variant-id="{{ $variant_id ?? '' }}">
                @csrf
                @if(isset($product_id))
                    <input type="hidden" name="product_id" value="{{ $product_id }}">
                @endif
                @if(isset($variant_id))
                    <input type="hidden" name="variant_id" value="{{ $variant_id }}">
                @endif
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-primary/10 text-primary hover:bg-primary hover:text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                    {{ gtrans('Add to Cart') }}
                </button>
            </form>
        @else
            <button type="button" onclick="event.preventDefault(); event.stopPropagation();" class="mt-3 w-full bg-primary/10 text-primary hover:bg-primary hover:text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                {{ gtrans('Add to Cart') }}
            </button>
        @endif
    </div>
</div>
