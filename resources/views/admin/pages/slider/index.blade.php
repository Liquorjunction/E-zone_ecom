@extends('admin.layouts.app')

@section('header-title', 'Hero Slider Management')
@section('header-description', 'Manage hero sliders for the website')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.hero-slider.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Hero Sliders</h4>
                <p class="text-secondary small mb-0">Manage your website's hero sliders</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @include('admin.partials.batch-actions')
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('slider.create')
                <a href="{{ route('admin.hero-slider.create') }}" class="btn btn-primary btn-sm">Add New Slider</a>
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
                        <span class="fw-semibold text-dark small">Filter Sliders</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body p-4">
                    <form id="slider-filter-bar" method="GET">
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

        {{-- Sliders Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('slider.update') || auth()->user()->can('slider.delete');
                @endphp
                <table id="slider-list-table" class="table table-striped" role="grid" data-bs-toggle="data-table"
                    data-url="{{ route('admin.hero-slider.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr class="light">
                            <th><input type="checkbox" id="select-all-sliders"></th>
                            <th>#</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            @canany(['slider.update', 'slider.delete'])
                                <th style="min-width: 100px">Action</th>
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
    <script src="{{ asset('admin_assets/js/datatableJs/sliderManagement.js') }}"></script>
    <script src="{{ asset('admin_assets/js/sliderFilterBar.js') }}"></script>
@endpush
