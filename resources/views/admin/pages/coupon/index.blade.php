@extends('admin.layouts.app')
@section('title', 'Manage Coupons')
@section('header-title', 'Coupons Management')
@section('header-description', 'Create and manage coupons for your customers')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.coupons.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
        <div>
            <h4 class="fw-semibold mb-1">Manage Coupons</h4>
            <p class="text-secondary small mb-0">Create and manage coupons for your customers</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                <i class="fas fa-filter me-1"></i> Filters
            </button>
            @can('coupon.create')
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Create Coupon
                </a>
            @endcan
        </div>
    </div>

    <div class="collapse mb-4" id="filter-panel">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form id="coupon-filter-bar">
                    <div class="d-flex flex-wrap align-items-start" style="gap:3rem;">
                        <div>
                            <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                            <div class="d-flex gap-3">
                                <div class="form-check mb-0">
                                    <input class="form-check-input filter-input" type="radio" name="status" value="all" id="status_all"
                                        {{ request('status') === 'all' || !request()->has('status') ? 'checked' : '' }}>
                                    <label class="form-check-label small text-dark" for="status_all">All</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input filter-input" type="radio" name="status" value="active" id="status_active"
                                        {{ request('status') === 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label small text-dark" for="status_active">Active</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input filter-input" type="radio" name="status" value="inactive" id="status_inactive"
                                        {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                    <label class="form-check-label small text-dark" for="status_inactive">Inactive</label>
                                </div>
                            </div>
                        </div>

                        <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                        <div>
                            <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" style="width:145px;">
                                <span class="text-muted small">—</span>
                                <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" style="width:145px;">
                            </div>
                        </div>

                        <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                        <div>
                            <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Usage Sort</div>
                            <select class="form-select form-select-sm" id="sort_by_usage_count" name="sort_by_usage_count" style="width:190px;">
                                <option value="">Default</option>
                                <option value="asc">Lowest to Highest</option>
                                <option value="desc">Highest to Lowest</option>
                            </select>
                        </div>

                        <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                        <div>
                            <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Coupon Type</div>
                            <select class="form-select form-select-sm" id="coupon_type" name="coupon_type"
                                data-list-url="{{ route('admin.coupons.list') }}" data-loaded="0" style="width:190px;">
                                <option value="">All Types</option>
                            </select>
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                @php
                    $hasAction = auth()->user()->can('coupon.update') || auth()->user()->can('coupon.delete');
                @endphp
                <table id="coupons-table" class="table table-striped table-hover w-100"
                    data-source="{{ route('admin.coupons.data') }}"
                    data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead class="bg-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Usages</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            @canany(['coupon.update', 'coupon.delete'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/coupons.js') }}"></script>
<script src="{{ asset('admin_assets/js/couponFilterBar.js') }}"></script>
@endpush
