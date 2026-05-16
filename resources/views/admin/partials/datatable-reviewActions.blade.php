{{-- datatable-reviewActions.blade.php --}}
@php
    // $id, $is_approved, $approve_route, $reject_route are expected
@endphp
<div class="d-flex align-items-center gap-2">
    @if($is_approved === 'pending' && $approve_route)
        <!-- Approve Button -->
        <button type="button"
            class="approve-review-btn btn btn-sm btn-success"
            data-review-id="{{ $id }}"
            data-route="{{ $approve_route }}"
            title="Approve Review"
            style="padding: 0.25rem 0.5rem;">
            <i class="bi bi-check"></i>
        </button>
        <!-- Reject Button -->
        <button type="button"
            class="decline-review-btn btn btn-sm btn-danger"
            data-review-id="{{ $id }}"
            data-route="{{ $reject_route }}"
            title="Reject Review"
            style="padding: 0.25rem 0.5rem;">
            <i class="bi bi-x"></i>
        </button>
    @elseif($is_approved === 'approved')
        <span class="badge bg-success">
            <i class="bi bi-check-circle"></i> Approved
        </span>
    @elseif($is_approved === 'rejected')
        <span class="badge bg-danger">
            <i class="bi bi-x-circle"></i> Rejected
        </span>
    @else
        <span class="badge bg-warning text-dark">
            <i class="bi bi-clock"></i> Pending
        </span>
    @endif
</div>
