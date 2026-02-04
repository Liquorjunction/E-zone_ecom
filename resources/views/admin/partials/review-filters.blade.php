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
            <div class="col-12">
                <label for="product" class="form-label">Product</label>
                <select class="form-select" id="product" name="product">
                    <option value="">All</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
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
