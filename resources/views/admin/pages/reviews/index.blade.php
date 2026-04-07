@extends('admin.layouts.app')
@section('title', 'Manage Reviews')
@section('header-title', 'Reviews Management')
@section('header-description', 'View and manage all product reviews submitted by customers')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.reviews.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Manage Reviews</h4>
                <p class="text-secondary small mb-0">View and manage all product reviews submitted by customers</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        @include('admin.partials.review-filters')

        {{-- Reviews Table --}}
        <div class="card border-0 shadow-sm rounded-3">
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
@endsection
@push('scripts')
    <script>
        const reviewDataUrl = '{{ route('admin.reviews.data') }}';
        const reviewStatusListUrl = '{{ route('admin.reviews.status-list') }}';
        const reviewRatingListUrl = '{{ route('admin.reviews.rating-list') }}';
        const csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('admin_assets/js/datatableJs/reviews.js') }}"></script>
    <script src="{{ asset('admin_assets/js/review-filters.js') }}"></script>
@endpush
