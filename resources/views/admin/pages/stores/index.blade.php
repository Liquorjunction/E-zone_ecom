@extends('admin.layouts.app')
@section('title', 'Stores List')
@section('header-title', 'Stores Management')
@section('header-description', 'Manage all stores associated with your inventory')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.stores.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Stores</h4>
                <p class="text-secondary small mb-0">Manage your stores</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Filter Toggle Button --}}
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('store.create')
                    <a href="{{ route('admin.stores.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Store
                    </a>
                @endcan
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse mb-4" id="filter-panel">
            <div class="card border-0 shadow-sm rounded-3">
                <div
                    class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-primary"></i>
                        <span class="fw-semibold text-dark small">Filter Stores</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body px-4 py-4">
                    <form id="brand-filter-bar" method="GET">

                        <div class="d-flex flex-wrap align-items-center gap-4">

                            {{-- Status --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="" id="status_all" {{ !request('status') ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="status_all">All</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="1" id="status_active"
                                            {{ request('status') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="status_active">Active</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="0" id="status_inactive"
                                            {{ request('status') === '0' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark"
                                            for="status_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Actions --}}
                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <button type="button" id="reset-filter-btn" class="btn btn-light btn-sm px-4 border">
                                <i class="fas fa-times me-1"></i> Reset
                            </button>
                            <span id="filter-result-count" class="ms-auto text-muted small d-none">
                                Showing filtered results
                            </span>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('store.update') || auth()->user()->can('store.delete');
                @endphp
                <table id="store-table" class="table table-striped table-bordered w-100"
                    data-route="{{ route('admin.stores.data') }}"
                    data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Logo</th>
                            {{-- <th>Store ID</th> --}}
                            <th>Store Name</th>
                            <th>Location</th>
                            <th>Status</th>
                            @if ($hasAction)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data populated via Yajra DataTables in Controller --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/store.js') }}"></script>
        <script src="{{ asset('admin_assets/js/storeFilterBar.js') }}"></script>
    @endpush
@endsection
