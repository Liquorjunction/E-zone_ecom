@extends('admin.layouts.app')
@section('title', 'Manage Reviews')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="header-title">
                                <h4 class="card-title mb-1">Manage Reviews</h4>
                                <small class="text-muted">View and manage all product reviews</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" id="toggle-filters-btn">
                                    <i class="bi bi-funnel me-2"></i><span id="filter-btn-text">Show Filters</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        @include('admin.partials.review-filters')
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="reviews-table" class="table table-striped table-hover w-100"
                                        data-route="{{ route('admin.reviews.data') }}">
                                        <thead class="bg-light text-uppercase small">
                                            <tr>
                                                <th>ID</th>
                                                <th>Product</th>
                                                <th>Review</th>
                                                <th>User</th>
                                                <th>Order</th>
                                                <th>Rating</th>
                                                <th>Created At</th>
                                                <th>Approval Actions</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Data Loaded via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const reviewDataUrl = '{{ route('admin.reviews.data') }}';
        const csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('admin_assets/js/datatableJs/reviews.js') }}"></script>
    <script src="{{ asset('admin_assets/js/review-filters.js') }}"></script>
@endpush
