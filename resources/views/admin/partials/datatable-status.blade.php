<div class="form-check form-switch form-check-inline pe-0">
    <input
        class="form-check-input status-toggle"
        type="checkbox"
        data-url="{{ $toggle_url }}"
        {{ $status == 'active' ? 'checked' : '' }}
        {{-- Add a unique ID to ensure label clicking works correctly --}}
        id="status-switch-{{ uniqid() }}"
    >
    <label class="pl-2 form-check-label" for="status-switch-{{ uniqid() }}">
        {{ $status == 'active' ? 'Active' : 'Inactive' }}
    </label>
</div>
