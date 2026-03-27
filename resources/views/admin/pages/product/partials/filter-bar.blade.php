{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div
            class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Products</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters">
            </button>
        </div>
        <div class="card-body px-4 py-3">
            <form id="product-filter-bar" method="GET">
                <div class="row g-3 align-items-end">

                    {{-- Availability --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-medium text-muted mb-1">
                            <i class="fas fa-box me-1 opacity-50"></i> Availability
                        </label>
                        <div class="d-flex gap-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input filter-input" type="checkbox" name="in_stock"
                                    value="1" id="in_stock" {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="in_stock">
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle px-2">In
                                        Stock</span>
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input filter-input" type="checkbox" name="out_of_stock"
                                    value="1" id="out_of_stock" {{ request('out_of_stock') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="out_of_stock">
                                    <span
                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">Out
                                        of Stock</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Bestseller --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">
                            <i class="fas fa-star me-1 opacity-50"></i> Bestseller
                        </label>
                        <div class="form-check form-switch mb-0 mt-1">
                            <input class="form-check-input filter-input" type="checkbox" id="bestseller"
                                name="best_seller" value="1" {{ request('best_seller') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="bestseller">
                                Bestsellers only
                            </label>
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">
                            <i class="fas fa-tag me-1 opacity-50"></i> Price Range
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control filter-input" name="price_min" placeholder="Min"
                                value="{{ request('price_min') }}" min="0" step="0.01">
                            <span class="input-group-text bg-light text-muted px-2">–</span>
                            <input type="number" class="form-control filter-input" name="price_max" placeholder="Max"
                                value="{{ request('price_max') }}" min="0" step="0.01">
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">
                            <i class="fas fa-calendar me-1 opacity-50"></i> Created Date
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control filter-input" name="date_from"
                                value="{{ request('date_from') }}">
                            <span class="input-group-text bg-light text-muted px-2">–</span>
                            <input type="date" class="form-control filter-input" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>

                </div>

                {{-- Filter Actions --}}
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <button type="button" id="reset-filter-btn" class="btn btn-light btn-sm px-4 border">
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

{{-- Active Filter Chips --}}
<div id="active-filter-chips" class="d-flex flex-wrap gap-2 mb-3 d-none">
    {{-- JS will populate these --}}
</div>
