{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div
            class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Customers</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters">
            </button>
        </div>

        <div class="card-body p-4">
            <form id="customer-filter-bar" method="GET">

                {{-- All filters in one aligned row --}}
                <div class="d-flex flex-wrap align-items-center" style="gap:6rem !important;">

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
                                <label class="form-check-label small text-dark" for="status_inactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:auto;"></div>

                    {{-- Date Range --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Joining Date</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" class="form-control form-control-sm filter-input" name="start_date"
                                value="{{ request('start_date') }}">
                            <span class="text-muted">to</span>
                            <input type="date" class="form-control form-control-sm filter-input" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:auto;"></div>

                    {{-- Email Verified --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Email Verified</div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input filter-input" type="checkbox" name="email_verified"
                                value="1" id="email_verified" {{ request('email_verified') ? 'checked' : '' }}>
                            <label class="form-check-label small text-dark" for="email_verified">Only Verified</label>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="mt-4 d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filters">
                        <i class="fas fa-eraser me-1"></i> Reset Filters
                    </button>
                    <span id="filter-result-count" class="ms-auto text-muted small d-none">
                        Showing filtered results
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
