@extends('admin.layouts.app')
@section('title', 'Review Details')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>Review #{{ $review->id }}
                        </h4>
                        <small class="text-muted">{{ $review->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Reviewer</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th>Name</th>
                                    <td>
                                        @if($review->user)
                                            {{ $review->user->name }}
                                        @else
                                            {{ $review->reviewer_name ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><a href="mailto:{{ $review->reviewer_email }}">{{ $review->reviewer_email }}</a></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Product</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th>Name</th>
                                    <td>
                                        @if($review->product)
                                            {{ $review->product->getTranslation('name', 'en') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Variant</th>
                                    <td>
                                        @if($review->productVariant)
                                            {{ $review->productVariant->getTranslation('variant_name', 'en') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order</th>
                                    <td>
                                        @if($review->order)
                                            <a href="{{ route('admin.orders.show', $review->order->id) }}" class="text-decoration-none">
                                                {{ $review->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Status & Rating</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($review->is_approved == 'approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                        @elseif($review->is_approved == 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                        @else
                                            <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rating</th>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star-fill {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                        @endfor
                                        <span class="ms-2 fw-500">{{ $review->rating }} / 5</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Activity</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th>Created At</th>
                                    <td><i class="bi bi-calendar me-1 text-info"></i>{{ $review->created_at->format('d M Y, H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><i class="bi bi-pencil me-1 text-warning"></i>{{ $review->updated_at->format('d M Y, H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Review Content</h6>
                            <div class="border rounded p-3 bg-light">
                                <div class="lh-lg">{{ $review->review_text }}</div>
                            </div>
                        </div>
                    </div>
                    @if($review->images && $review->images->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Review Images</h6>
                            <div class="row g-3">
                                @foreach($review->images as $image)
                                    <div class="col-md-3 col-6">
                                        <div class="position-relative overflow-hidden rounded" style="aspect-ratio: 1/1;">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                 alt="Review Image"
                                                 class="img-fluid w-100 h-100 object-fit-cover"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#imageModal{{ $image->id }}"
                                                 style="cursor: pointer;">
                                        </div>
                                    </div>
                                    <!-- Image Modal -->
                                    <div class="modal fade" id="imageModal{{ $image->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-secondary">
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                                         alt="Review Image"
                                                         class="img-fluid w-100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Reviews
                        </a>
                        @if(auth('admin')->user()->can('reviews.delete'))
                            <button class="btn btn-outline-danger ms-auto" onclick="deleteReview({{ $review->id }})">
                                <i class="bi bi-trash me-2"></i>Delete Review
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteReview(reviewId) {
        if (typeof Swal === 'undefined') {
            if (confirm('Are you sure you want to delete this review?')) {
                deleteReviewConfirm(reviewId);
            }
            return;
        }

        Swal.fire({
            title: 'Delete Review?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteReviewConfirm(reviewId);
            }
        });
    }

    function deleteReviewConfirm(reviewId) {
        $.ajax({
            url: '{{ route("admin.reviews.destroy") }}',
            type: 'POST',
            data: {
                id: reviewId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("admin.reviews.index") }}';
                    });
                } else {
                    alert(response.message);
                    window.location.href = '{{ route("admin.reviews.index") }}';
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to delete review.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }
</script>
@endpush
@endsection
