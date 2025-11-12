@unless ($breadcrumbs->isEmpty())
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            @foreach ($breadcrumbs as $breadcrumb)

                {{-- If it's a link and not the last item --}}
                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" class="hover:text-primary transition-colors">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    {{-- If it's the last item (current page) --}}
                    <li aria-current="page">
                        <span class="text-primary font-medium">
                            {{ $breadcrumb->title }}
                        </span>
                    </li>
                @endif

                {{-- Add the chevron separator --}}
                @unless($loop->last)
                    <li class="text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                @endunless

            @endforeach
        </ol>
    </nav>
@endunless
