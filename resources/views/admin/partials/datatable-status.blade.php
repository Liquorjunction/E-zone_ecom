@can('toggle admin status')
    <form action="{{ $toggle_url }}" method="POST" style="display: inline;">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
            data-bs-placement="top" data-original-title="{{ $status == 'active' ? 'Deactivate' : 'Activate' }}"
            aria-label="{{ $status == 'active' ? 'Deactivate' : 'Activate' }}"
            data-bs-original-title="{{ $status == 'active' ? 'Deactivate' : 'Activate' }}">
            <span class="btn-inner">
                @if ($status == 'active')
                    <span class="badge rounded-pill bg-success">Active</span>
                @else
                    <span class="badge rounded-pill bg-danger">Inactive</span>
                @endif
            </span>
        </button>
    </form>
@endcan
