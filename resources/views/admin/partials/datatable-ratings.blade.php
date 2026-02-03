@php
    $maxStars = 5;
    $rating = isset($rating) ? floatval($rating) : 0;
@endphp
<div class="datatable-rating">
    @for ($i = 1; $i <= $maxStars; $i++)
        @if ($i <= floor($rating))
            <span class="text-warning">&#9733;</span>
        @elseif ($i - $rating > 0 && $i - $rating < 1)
            <span class="text-warning">&#189;</span>
        @else
            <span class="text-secondary">&#9734;</span>
        @endif
    @endfor
    <span class="ms-2">{{ rtrim(rtrim(number_format($rating, 2, '.', ''), '0'), '.') }}/{{ $maxStars }}</span>
</div>
