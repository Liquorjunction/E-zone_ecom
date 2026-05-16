@extends('admin.layouts.app')
@section('title', 'Manage Discounts')
@section('header-title', 'Discounts Management')
@section('header-description', 'Create and manage discounts for your products')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.discounts.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Discounts</h4>
                <p class="text-secondary small mb-0">Create and manage discounts for your products</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('discount.create')
                    <a href="{{ route('admin.discounts.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New Discount
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
                        <span class="fw-semibold text-dark small">Filter Discounts</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body p-4">
                    <form id="discount-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            {{-- Status --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="all" id="status_all"
                                            {{ request('status') === 'all' || !request()->has('status') ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="status_all">All</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="active" id="status_active"
                                            {{ request('status') === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="status_active">Active</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="status"
                                            value="inactive" id="status_inactive"
                                            {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark"
                                            for="status_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Expired / Upcoming / Active --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Validity</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="validity"
                                            value="all" id="validity_all"
                                            {{ request('validity') === 'all' || !request()->has('validity') ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="validity_all">All</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="validity"
                                            value="active" id="validity_active"
                                            {{ request('validity') === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark" for="validity_active">Active</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="validity"
                                            value="upcoming" id="validity_upcoming"
                                            {{ request('validity') === 'upcoming' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark"
                                            for="validity_upcoming">Upcoming</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input filter-input" type="radio" name="validity"
                                            value="expired" id="validity_expired"
                                            {{ request('validity') === 'expired' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark"
                                            for="validity_expired">Expired</label>
                                    </div>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Discount Type --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Discount Type</div>
                                <select class="form-select form-select-sm filter-input" id="discount_type" name="discount_type"
                                    data-list-url="{{ route('admin.discounts.list') }}" data-loaded="0" style="width:190px;">
                                    <option value="">All</option>
                                </select>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Date Range --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm filter-input" id="date_from"
                                        name="date_from" value="{{ request('date_from') }}" style="width:145px;">
                                    <span class="text-muted small">to</span>
                                    <input type="date" class="form-control form-control-sm filter-input" id="date_to"
                                        name="date_to" value="{{ request('date_to') }}" style="width:145px;">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-sm btn-primary" id="apply-filters-btn">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filters-btn">
                                <i class="fas fa-eraser me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Discounts Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('discount.update') || auth()->user()->can('discount.delete');
                @endphp
                <table id="discounts-table" class="table table-bordered table-striped" style="width:100%;"
                    data-source="{{ route('admin.discounts.datatable') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th></th> <!-- For the expand icon -->
                            <th>ID</th>
                            <th>Name (EN)</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            @canany(['discount.update', 'discount.delete'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/discounts.js') }}"></script>
    <script src="{{ asset('admin_assets/js/discountFilterBar.js') }}"></script>
@endpush
