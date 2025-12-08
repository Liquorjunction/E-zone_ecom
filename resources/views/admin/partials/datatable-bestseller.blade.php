<div class="form-check form-switch form-check-inline pe-0">
    <input
        class="form-check-input bestseller-toggle"
        type="checkbox"
        data-url="{{ $toggle_url }}"
        {{ $is_bestseller ? 'checked' : '' }}
        id="bestseller-switch-{{ $id }}"
        {{ !$toggle_url ? 'disabled' : '' }}
    >
    <label class="pl-2 form-check-label" for="bestseller-switch-{{ $id }}">
        @if($is_bestseller)
            <span class="text-warning">⭐ Bestseller</span>
        @else
            <span class="text-muted">Regular</span>
        @endif
    </label>
</div>
