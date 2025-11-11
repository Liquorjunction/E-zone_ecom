@if ($paginator->hasPages())
    <div class="flex items-center space-x-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 text-gray-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="px-4 py-2 bg-primary text-white rounded-lg font-medium shadow-lg" aria-current="page">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @endif
    </div>
@endif
