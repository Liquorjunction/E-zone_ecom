{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Products</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse"
                data-bs-target="#filter-panel" aria-label="Close filters">
            </button>
        </div>

        <div class="card-body px-4 py-4">
            <form id="product-filter-bar" method="GET">

                {{-- All filters in one aligned row --}}
                <div class="d-flex flex-wrap align-items-center" style="gap:6.5rem !important;">

                    {{-- Availability --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Availability</div>
                        <div class="form-check mb-0">
                            <input class="form-check-input filter-input" type="checkbox"
                                name="in_stock" value="1" id="in_stock"
                                {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="form-check-label small text-dark" for="in_stock">Exclude Out of Stock</label>
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:40px;"></div>

                    {{-- Status --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                        <div class="d-flex gap-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input filter-input" type="checkbox"
                                    name="status" value="active" id="status_active"
                                    {{ request('status') === 'active' ? 'checked' : '' }}>
                                <label class="form-check-label small text-dark" for="status_active">Active</label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input filter-input" type="checkbox"
                                    name="status" value="inactive" id="status_inactive"
                                    {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                <label class="form-check-label small text-dark" for="status_inactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:40px;"></div>

                    {{-- Bestseller --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Bestseller</div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input filter-input" type="checkbox"
                                id="bestseller" name="best_seller" value="1"
                                {{ request('best_seller') ? 'checked' : '' }}>
                            <label class="form-check-label small text-dark" for="bestseller">Only</label>
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:40px;"></div>

                    {{-- Price Range --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Price Range</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" class="form-control form-control-sm filter-input"
                                name="price_min" placeholder="Min"
                                value="{{ request('price_min') }}" min="0" step="0.01"
                                style="width:90px;">
                            <span class="text-muted small">—</span>
                            <input type="number" class="form-control form-control-sm filter-input"
                                name="price_max" placeholder="Max"
                                value="{{ request('price_max') }}" min="0" step="0.01"
                                style="width:90px;">
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block" style="height:40px;"></div>

                    {{-- Date Range --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" class="form-control form-control-sm filter-input"
                                name="date_from" value="{{ request('date_from') }}"
                                style="width:145px;">
                            <span class="text-muted small">—</span>
                            <input type="date" class="form-control form-control-sm filter-input"
                                name="date_to" value="{{ request('date_to') }}"
                                style="width:145px;">
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
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

