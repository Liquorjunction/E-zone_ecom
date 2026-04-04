<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Customers</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters"></button>
        </div>

        <div class="card-body px-4 py-4">
            <form id="filter-form">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <label for="status-filter" class="form-label text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Status</label>
                        <select class="form-select form-select-sm" id="status-filter" data-filter="status">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="date-from-filter" class="form-label text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">From Date</label>
                        <input type="date" class="form-control form-control-sm" id="date-from-filter" data-filter="date_from">
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label for="date-to-filter" class="form-label text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">To Date</label>
                        <input type="date" class="form-control form-control-sm" id="date-to-filter" data-filter="date_to">
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-primary btn-sm px-4" id="apply-filters">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-4 border" id="clear-filters">
                        <i class="fas fa-times me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
