@if(isset($edit_url))
    @can('role.update')
        <a href="{{ $edit_url }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Role & Permissions">
            Edit
        </a>
    @endcan
@endif
@if(isset($delete_url))
    @can('role.delete')
        <form action="{{ $delete_url }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Role">
                Delete
            </button>
        </form>
    @endcan
@endif
