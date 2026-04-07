@extends('admin.layouts.app')
@section('title', 'Dispatch Management')
@section('header-title', 'Dispatch Management')
@section('header-description', 'Track shipments and update delivery statuses')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.dispatch.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Dispatch Management</h4>
                <p class="text-secondary small mb-0">Track shipments and update delivery statuses</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-box-seam"></i> Go to Orders
                </a>
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse mb-4" id="filter-panel">
            <div class="card border-0 shadow-sm rounded-3">
                <div
                    class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-primary"></i>
                        <span class="fw-semibold text-dark small">Filter Dispatches</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body px-4 py-4">
                    <form id="dispatch-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-start" style="gap:6rem;">
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <select id="filter_status" class="form-select form-select-sm"
                                    data-statuses-url="{{ route('admin.dispatch.status-list') }}"
                                    data-selected="{{ request('status', '') }}" style="width:200px;">
                                    <option value="">Select statuses...</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                            <button type="button" id="apply-filters-btn" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <button type="button" id="reset-filters-btn" class="btn btn-light btn-sm px-4 border">
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

        {{-- Dispatches DataTable --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dispatchTable" style="width: 100%;"
                        data-url="{{ route('admin.dispatch.data') }}">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase text-muted small fw-semibold">Dispatch ID</th>
                                <th class="text-uppercase text-muted small fw-semibold">Order</th>
                                <th class="text-uppercase text-muted small fw-semibold">Item & Qty</th>
                                <th class="text-uppercase text-muted small fw-semibold">Source Location</th>
                                <th class="text-uppercase text-muted small fw-semibold">Status</th>
                                <th class="text-uppercase text-muted small fw-semibold">Dispatch Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Populated by Yajra DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/dispatch.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dispatchFilterBar.js') }}"></script>
@endpush
