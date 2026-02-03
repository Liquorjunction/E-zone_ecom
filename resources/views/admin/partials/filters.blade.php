<div class="card shadow-sm border-0 mb-4" id="filters-card" style="display: none;">
    <div class="card-header bg-soft-info border-0 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-info"></i>
            <h6 class="mb-0">Filters</h6>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filters" onclick="event.stopPropagation();">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset
        </button>
    </div>
    <div class="card-body p-4" id="filters-body">
        <form id="filter-form" class="row g-3">
            <!-- Status Filter -->
            <div class="col-md-6 col-lg-4">
                <label for="status-filter" class="form-label small fw-semibold">Status</label>
                <select class="form-select form-select-sm" id="status-filter" data-filter="status">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="col-md-6 col-lg-4">
                <label for="date-from-filter" class="form-label small fw-semibold">From Date</label>
                <input type="date" class="form-control form-control-sm" id="date-from-filter"
                    data-filter="date_from">
            </div>

            <div class="col-md-6 col-lg-4">
                <label for="date-to-filter" class="form-label small fw-semibold">To Date</label>
                <input type="date" class="form-control form-control-sm" id="date-to-filter"
                    data-filter="date_to">
            </div>

            <!-- Action Buttons -->
            <div class="col-12 d-flex gap-2 pt-2">
                <button type="button" class="btn btn-sm btn-primary" id="apply-filters">
                    <i class="bi bi-search me-1"></i>Apply Filters
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-filters">
                    <i class="bi bi-x me-1"></i>Clear
                </button>
            </div>
        </form>
    </div>
</div>
