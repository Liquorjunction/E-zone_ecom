@extends('admin.layouts.app')
@section('title', 'About Us Management')
@section('header-title', 'About Us Management')
@section('header-description', 'Manage About Us entries for the website')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.about_us.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">About Us</h4>
                <p class="text-secondary small mb-0">Manage About Us entries for the website</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('about_us.create')
                    <a href="{{ route('admin.about_us.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New About Us Entry
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
                        <span class="fw-semibold text-dark small">Filter About Us Entries</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body p-4">
                    <form id="about-us-filter-bar" method="GET">
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

                            <div class="vr opacity-25 d-none d-md-block" style="height:auto;"></div>

                            {{-- Date Range --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm filter-input" name="date_from"
                                        value="{{ request('date_from') }}" style="width:145px;">
                                    <span class="text-muted small">—</span>
                                    <input type="date" class="form-control form-control-sm filter-input" name="date_to"
                                        value="{{ request('date_to') }}" style="width:145px;">
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex align-items-center gap-3 mt-4">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <button type="button" id="reset-filters-btn" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eraser me-1"></i> Reset
                            </button>
                            <span id="filter-result-count" class="ms-auto text-muted small d-none">
                                Showing filtered results
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- About Us Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('about_us.update') || auth()->user()->can('about_us.delete');
                @endphp
                <table id="about-us-table" class="table table-striped table-bordered w-100"
                    data-route="{{ route('admin.about_us.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description (Preview)</th>
                            <th>Created At</th>
                            {{-- <th>Status</th> --}}
                            @canany(['about_us.update', 'about_us.delete'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/aboutUs.js') }}"></script>
    <script src="{{ asset('admin_assets/js/aboutUsFilterBar.js') }}"></script>
@endpush
