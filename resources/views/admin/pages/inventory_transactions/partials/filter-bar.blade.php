{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Inventory Transactions</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters">
            </button>
        </div>
        <div class="card-body px-4 py-4">
            <form id="filter-form" method="GET">

                <div class="d-flex flex-wrap align-items-start" style="gap:6rem;">

                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Transaction Type</div>
                        <select id="filter-type" name="type" class="form-select form-select-sm"
                            data-types-url="{{ route('admin.inventory-transactions.types') }}"
                            data-selected="{{ request('type', '') }}" style="width:200px;">
                            <option value="">Select Type</option>
                        </select>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Location Type</div>
                        <select id="filter-location-type" name="location_type" class="form-select form-select-sm" style="width:160px;">
                            <option value="">All</option>
                            <option value="warehouse" {{ request('location_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                            <option value="store" {{ request('location_type') === 'store' ? 'selected' : '' }}>Store</option>
                        </select>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" id="filter-date-from" name="date_from" class="form-control form-control-sm"
                                value="{{ request('date_from', '') }}" style="width:145px;">
                            <span class="text-muted small">—</span>
                            <input type="date" id="filter-date-to" name="date_to" class="form-control form-control-sm"
                                value="{{ request('date_to', '') }}" style="width:145px;">
                        </div>
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
