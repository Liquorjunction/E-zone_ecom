@can('user management.update')
    <form action="{{ $toggle_url }}" method="POST" style="display: inline;">
        @csrf
        @method('PATCH')
        <div class="form-check form-switch form-check-inline pe-0">
            <input class="form-check-input" type="checkbox" id="switch-status" {{ $status == 'active' ? 'checked' : '' }} onchange="this.form.submit()">
            <label class="pl-2 form-check-label" for="switch-status">{{ $status == 'active' ? 'Active' : 'Inactive' }}</label>
        </div>
    </form>
@endcan
