<div class="card shadow-sm border-0 mb-4" id="filters-card" style="display: none;">
    <div class="card-header bg-soft-info border-0 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-info"></i>
            <h6 class="mb-0">Filters</h6>
        </div>
        <!-- Action Buttons -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary" id="apply-filters">
                <i class="bi bi-search me-1"></i>Apply Filters
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-filters">
                <i class="bi bi-x me-1"></i>Clear
            </button>
        </div>
    </div>
    <div class="card-body p-4" id="filters-body">
        <form id="filter-form" class="row g-3">
            <div class="col-md-6 col-lg-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from">
            </div>
            <div class="col-md-6 col-lg-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to">
            </div>
             <div class="col-md-6 col-lg-3">
                <label for="rating" class="form-label">Rating</label>
                <select class="form-select" id="rating" name="rating">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
        </form>
    </div>
</div>
