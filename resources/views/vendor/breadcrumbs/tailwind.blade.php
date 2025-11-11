@unless ($breadcrumbs->isEmpty())
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <a href="{{ $breadcrumb->url }}" class="hover:text-primary transition-colors">{{ $breadcrumb->title }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @else
                <span class="text-primary font-medium">{{ $breadcrumb->title }}</span>
            @endif
        @endforeach
    </nav>
@endunless
