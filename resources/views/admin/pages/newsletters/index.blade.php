@extends('admin.layouts.app')
@section('title', 'Manage Newsletters')

@section('header-title', 'Newsletter Management')
@section('header-description', 'Manage the newsletters shown across the site')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.newsletters.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Newsletter Subscribers</h4>
                <p class="text-secondary small mb-0">View and manage newsletter subscriptions</p>
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
        <div class="collapse mb-4" id="filter-panel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <form id="newsletter-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            {{-- Status --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input newsletter-status-filter" type="checkbox" id="filter_status_active"
                                            value="active" {{ request('status') === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filter_status_active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input newsletter-status-filter" type="checkbox" id="filter_status_inactive"
                                            value="inactive" {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filter_status_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Date Range --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Subscribed Date</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm" id="filter_date_from"
                                        value="{{ request('date_from', '') }}">
                                    <span class="text-muted">to</span>
                                    <input type="date" class="form-control form-control-sm" id="filter_date_to"
                                        value="{{ request('date_to', '') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
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

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    @php
                        $hasAction =
                            auth()->user()->can('newsletter.update') || auth()->user()->can('newsletter.delete');
                    @endphp
                    <table id="newsletters-table" class="table table-striped table-hover w-100" role="grid"
                        data-url="{{ route('admin.newsletter-subscribers.data') }}"
                        data-has-action="{{ $hasAction ? 1 : 0 }}">
                        <thead class="bg-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Subscribed At</th>
                                <th>Status</th>
                                @if ($hasAction)
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/newsletters.js') }}"></script>
    <script src="{{ asset('admin_assets/js/newsletter-filter-bar.js') }}"></script>
@endpush
