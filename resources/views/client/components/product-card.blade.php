<div class="group bg-white rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary/30">
    <div class="relative aspect-square overflow-hidden bg-gray-50">
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute top-3 right-3 {{ $badgeColor }} text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">
            {{ $badge }}
        </div>
        <button class="absolute top-3 left-3 w-9 h-9 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg hover:bg-primary hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>
    <div class="p-4">
        <span class="inline-block text-xs font-medium text-primary bg-primary/10 px-2 py-1 rounded">{{ $category }}</span>
        <h3 class="mt-2 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors min-h-[40px]">
            {{ $title }}
        </h3>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-lg font-bold text-gray-900">{{ $price }}</span>
            @if(isset($oldPrice))
                <span class="text-sm text-gray-400 line-through">{{ $oldPrice }}</span>
            @endif
        </div>
        <button class="mt-3 w-full bg-primary/10 text-primary hover:bg-primary hover:text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
            {{ gtrans('Add to Cart') }}
        </button>
    </div>
</div>
