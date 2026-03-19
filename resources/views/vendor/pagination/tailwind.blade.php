@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4">

        {{-- ── Results summary ── --}}
        <p class="text-sm text-gray-500 shrink-0">
            @if ($paginator->firstItem())
                {{ statictranslations('Showing') }}
                <span class="font-semibold text-gray-800">{{ $paginator->firstItem() }}</span>
                –
                <span class="font-semibold text-gray-800">{{ $paginator->lastItem() }}</span>
                {{ statictranslations('of') }}
                <span class="font-semibold text-gray-800">{{ $paginator->total() }}</span>
                {{ statictranslations('results') }}
            @else
                {{ statictranslations('Showing') }} {{ $paginator->count() }} {{ statictranslations('results') }}
            @endif
        </p>

        {{-- ── Page buttons ── --}}
        <div class="flex items-center gap-1">

            {{-- Prev button --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                             bg-gray-50 text-gray-300 cursor-not-allowed select-none"
                      aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          bg-white text-gray-500 hover:bg-primary hover:text-white hover:border-primary
                          transition-all duration-150 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)

                {{-- "…" separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Number links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Active page --}}
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold
                                         bg-primary text-white border border-primary shadow-md shadow-primary/30
                                         cursor-default select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-medium
                                      bg-white text-gray-600 border border-gray-200
                                      hover:bg-primary hover:text-white hover:border-primary
                                      transition-all duration-150 shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- Next button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   aria-label="{{ __('pagination.next') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          bg-white text-gray-500 hover:bg-primary hover:text-white hover:border-primary
                          transition-all duration-150 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                             bg-gray-50 text-gray-300 cursor-not-allowed select-none"
                      aria-disabled="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif

        </div>
    </nav>
@endif
