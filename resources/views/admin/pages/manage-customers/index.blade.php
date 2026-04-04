@extends('admin.layouts.app')
@section('title', 'Manage Customers')
@section('header-title', 'Manage Customers')
@section('header-description', 'View and manage all registered customers')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.customers.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Manage Customers</h4>
                <p class="text-secondary small mb-0">View and manage all registered customers</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative"
                    data-bs-toggle="collapse" data-bs-target="#filter-panel"
                    aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                </button>
                @include('admin.partials.batch-actions')
            </div>
        </div>

        @include('admin.partials.filters')

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="customers-table" class="table table-striped table-bordered w-100"
                        data-route="{{ route('admin.customers.data') }}">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-customers"></th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joining Date</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
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
    <script src="{{ asset('admin_assets/js/datatableJs/manage-customers.js') }}"></script>
    <script src="{{ asset('admin_assets/js/manage-customer-filter-bar.js') }}"></script>
@endpush
