{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Reviews</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters">
            </button>
        </div>
        <div class="card-body p-4">
            <form id="review-filter-bar">
                <div class="d-flex flex-wrap align-items-start" style="gap:3rem;">
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                        <select class="form-select form-select-sm filter-input" id="status" name="status"
                            data-list-url="{{ route('admin.reviews.status-list') }}" data-loaded="0"
                            style="width:170px;">
                            <option value="">Loading statuses...</option>
                        </select>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" class="form-control form-control-sm filter-input" id="date_from" name="date_from" style="width:145px;">
                            <span class="text-muted small">—</span>
                            <input type="date" class="form-control form-control-sm filter-input" id="date_to" name="date_to" style="width:145px;">
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Rating</div>
                        <select class="form-select form-select-sm filter-input" id="rating" name="rating"
                            data-list-url="{{ route('admin.reviews.rating-list') }}" data-loaded="0"
                            style="width:170px;">
                            <option value="">Loading ratings...</option>
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
                    <span id="filter-result-count" class="ms-auto text-muted small d-none">Showing filtered results</span>
                </div>
            </form>
        </div>
    </div>
</div>
