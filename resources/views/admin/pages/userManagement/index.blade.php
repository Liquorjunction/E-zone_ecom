@extends('admin.layouts.app')
@section('header-title', 'User Management')
@section('header-description', 'Manage user accounts and permissions')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.user-management.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">User List</h4>
                <p class="text-secondary small mb-0">Manage user accounts and permissions</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('user management.create')
                    <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary btn-sm">
                        Add New User
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
                        <span class="fw-semibold text-dark small">Filter Users</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body p-4">
                    <form id="user-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-center gap-4">

                            {{-- Status --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input user-status-filter" type="checkbox"
                                            id="filter_status_active" value="active"
                                            {{ request('status') === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filter_status_active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input user-status-filter" type="checkbox"
                                            id="filter_status_inactive" value="inactive"
                                            {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filter_status_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Role --}}
                            <div style="min-width: 280px;">
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Role</div>
                                <select class="select2-ajax browser-default form-control" data-allow-clear="true"
                                    data-error="#role-error-div" data-option-url="{{ route('admin.lists.roles') }}"
                                    data-placeholder="Select role" id="role" name="role">
                                    @if (old('role'))
                                        <option selected value="{{ old('role') }}">{{ old('role') }}</option>
                                    @endif
                                </select>
                                <div id="role-error-div"></div>
                                @error('role')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Date Range --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Joining Date</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm filter-input" id="date_from"
                                        name="date_from" value="{{ request('date_from') }}" style="width:145px;">
                                    <span class="text-muted small">—</span>
                                    <input type="date" class="form-control form-control-sm filter-input" id="date_to"
                                        name="date_to" value="{{ request('date_to') }}" style="width:145px;">
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                            <button type="button" id="apply-filters-btn" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <button type="button" id="reset-filters-btn" class="btn btn-outline-secondary btn-sm">
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

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="user-list-table" class="table table-striped table-hover w-100" role="grid"
                        data-bs-toggle="data-table" data-url="{{ route('admin.user-management.data') }}">
                        <thead class="bg-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th style="min-width: 100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/userManagement.js') }}"></script>
    <script src="{{ asset('admin_assets/js/userManagementFilterBar.js') }}"></script>
@endpush
