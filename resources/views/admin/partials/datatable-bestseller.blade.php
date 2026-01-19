<div class="form-check form-switch form-check-inline pe-0">
    <input
        class="form-check-input bestseller-toggle"
        type="checkbox"
        data-url="{{ $toggle_url }}"
        data-variants-all-bestseller="{{ $variants_all_bestseller ?? 0 }}"
        data-variants-all-not-bestseller="{{ $variants_all_not_bestseller ?? 0 }}"
        {{ $is_bestseller ? 'checked' : '' }}
        id="bestseller-switch-{{ $id }}"
        {{ !$toggle_url ? 'disabled' : '' }}
    >
    <label class="pl-2 form-check-label" for="bestseller-switch-{{ $id }}">
        @if($is_bestseller)
            <span class="text-warning"></span>
        @else
            <span class="text-muted"></span>
        @endif
    </label>
</div>
