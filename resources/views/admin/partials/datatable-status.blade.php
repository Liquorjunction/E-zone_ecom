<div class="form-check form-switch form-check-inline pe-0">
    @php($__uid = uniqid('status-switch-'))
    <input
        class="form-check-input status-toggle"
        type="checkbox"
        data-url="{{ $toggle_url }}"
        @if(!empty($status)) checked @endif
        @if(!empty($is_only_active)) disabled title="At least one slider must remain active" @endif
        id="{{ $__uid }}"
    >
    <label class="pl-2 form-check-label" for="{{ $__uid }}">

    </label>
    @if(!empty($is_only_active))
        <input type="hidden" name="__only_active_guard" value="1" />
    @endif

</div>
