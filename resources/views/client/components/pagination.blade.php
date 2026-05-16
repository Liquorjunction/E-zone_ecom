@props(['paginator', 'containerClasses' => 'flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6'])

@if ($paginator->hasPages())
    <div class="{{ $containerClasses }}">
        <!-- Results Count -->
        <p class="text-sm text-gray-600">
            {{ statictranslations('Showing') }} <span class="font-semibold">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span> {{ statictranslations('of') }} <span class="font-semibold">{{ $paginator->total() }}</span> {{ statictranslations('results') }}
        </p>

        <!-- Pagination Controls -->
        <div class="flex items-center space-x-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @else
                <button data-page="{{ $paginator->currentPage() - 1 }}" class="pagination-link px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            {{-- Page Numbers --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            {{-- First page if not in range --}}
            @if ($start > 1)
                <button data-page="1" class="pagination-link px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">1</button>
                @if ($start > 2)
                    <span class="px-2 text-gray-400">...</span>
                @endif
            @endif

            {{-- Page range --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <button class="px-4 py-2 bg-primary text-white rounded-lg font-medium shadow-lg">{{ $page }}</button>
                @else
                    <button data-page="{{ $page }}" class="pagination-link px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">{{ $page }}</button>
                @endif
            @endfor

            {{-- Last page if not in range --}}
            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <span class="px-2 text-gray-400">...</span>
                @endif
                <button data-page="{{ $paginator->lastPage() }}" class="pagination-link px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">{{ $paginator->lastPage() }}</button>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button data-page="{{ $paginator->currentPage() + 1 }}" class="pagination-link px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button class="px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@endif
