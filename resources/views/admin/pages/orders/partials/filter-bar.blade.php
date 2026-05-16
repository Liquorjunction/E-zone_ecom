{{-- Collapsible Filter Panel --}}
<div class="collapse mb-4" id="filter-panel">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h text-primary"></i>
                <span class="fw-semibold text-dark small">Filter Orders</span>
            </div>
            <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                aria-label="Close filters">
            </button>
        </div>
        <div class="card-body px-4 py-4">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <div class="d-flex flex-wrap align-items-start" style="gap:6rem;">
                    {{-- Status --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                        <select id="status" name="status" class="form-select form-select-sm filter-input"
                            data-statuses-url="{{ route('admin.orders.get-status-list') }}"
                            data-selected="{{ request('status', '') }}" style="width:180px;">
                            <option value="">All Statuses</option>
                            {{-- @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('status') == $status->id ? 'selected' : '' }}>
                                    {{ ucfirst($status->name) }}
                                </option>
                            @endforeach --}}
                        </select>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    {{-- Date Range --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Order Date</div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" class="form-control form-control-sm filter-input" name="start_date"
                                value="{{ request('start_date') }}" style="width:145px;">
                            <span class="text-muted small">—</span>
                            <input type="date" class="form-control form-control-sm filter-input" name="end_date"
                                value="{{ request('end_date') }}" style="width:145px;">
                        </div>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    {{-- Payment Method --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Payment Method</div>
                        <select id="payment_method" name="payment_method" class="form-select form-select-sm filter-input"
                            data-methods-url="{{ route('admin.orders.get-payment-methods') }}"
                            data-selected="{{ request('payment_method', '') }}" style="width:200px;">
                            <option value="">All Methods</option>
                            {{-- @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}"
                                    {{ request('payment_method') == $method->id ? 'selected' : '' }}>
                                    {{ ucfirst($method->name) }}
                                </option>
                            @endforeach --}}
                        </select>
                    </div>

                    <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                    {{-- Payment Status --}}
                    <div>
                        <div class="text-uppercase text-muted fw-semibold mb-2"
                            style="font-size:0.65rem;letter-spacing:0.08em;">Payment Status</div>
                        <select id="payment_status" name="payment_status" class="form-select form-select-sm filter-input"
                            data-selected="{{ request('payment_status', '') }}" style="width:180px;">
                            <option value="">All Statuses</option>
                            {{-- @foreach ($paymentStatuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('payment_status') == $status->id ? 'selected' : '' }}>
                                    {{ ucfirst($status->name) }}
                                </option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
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
