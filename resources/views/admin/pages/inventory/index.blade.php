@extends('admin.layouts.app')
@section('title', 'Inventory Batches')
@section('header-title', 'Inventory Batches')
@section('header-description', 'Manage your inventory batches grouped by inward entries')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.inventory.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Inventory Batches</h4>
                <p class="text-secondary small mb-0">Manage your inventory batches grouped by inward entries</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative"
                    data-bs-toggle="collapse" data-bs-target="#filter-panel"
                    aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
                @can('inventories.create')
                    {{-- Created To Add Inventory Stock --}}
                    {{-- <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                        data-bs-target="#seed-all-inventory-modal">
                        <i class="fas fa-layer-group me-1"></i> Seed All Inventory
                    </button> --}}
                    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Batch
                    </a>
                @endcan
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse mb-4" id="filter-panel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-primary"></i>
                        <span class="fw-semibold text-dark small">Filter Inventory Batches</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse"
                        data-bs-target="#filter-panel" aria-label="Close filters"></button>
                </div>
                <div class="card-body px-4 py-4">
                    <form id="inventory-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-start" style="gap:6.5rem;">

                            {{-- Exclude Sold --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Batch Filters</div>
                                <div class="form-check form-switch mb-0 mt-1">
                                    <input class="form-check-input filter-input" type="checkbox"
                                        name="exclude_sold" id="exclude_sold" value="1"
                                        {{ request('exclude_sold') ? 'checked' : '' }}>
                                    <label class="form-check-label small text-dark" for="exclude_sold">Unsold Batches</label>
                                </div>
                                <div class="form-check form-switch mb-0 mt-1">
                                    <input class="form-check-input filter-input" type="checkbox"
                                        name="exclude_completely_sold" id="exclude_completely_sold" value="1"
                                        {{ request('exclude_completely_sold') ? 'checked' : '' }}>
                                    <label class="form-check-label small text-dark" for="exclude_completely_sold">Exclude Completely Sold (0 qty)</label>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Location Type --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Location Type</div>
                                <select name="location_type" id="location_type"
                                    class="form-select form-select-sm filter-input" style="width:160px;">
                                    <option value="">All</option>
                                    <option value="warehouse" {{ request('location_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                    <option value="store"     {{ request('location_type') === 'store'     ? 'selected' : '' }}>Store</option>
                                </select>
                            </div>

                            {{-- Child divider: hidden until location_type selected --}}
                            <div id="location-id-divider" class="vr opacity-25 align-self-stretch {{ request('location_type') ? '' : 'd-none' }}"></div>

                            {{-- Specific Location: hidden until location_type selected --}}
                            <div id="location-id-wrapper" class="{{ request('location_type') ? '' : 'd-none' }}">
                                <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Location</div>
                                <select name="location_id" id="location_id"
                                    class="form-select form-select-sm filter-input" style="width:200px;">
                                    <option value="">All</option>
                                </select>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            {{-- Date Range --}}
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2" style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" name="date_from" id="date_from"
                                        class="form-control form-control-sm filter-input"
                                        value="{{ request('date_from') }}" style="width:150px;">
                                    <span class="text-muted small">—</span>
                                    <input type="date" name="date_to" id="date_to"
                                        class="form-control form-control-sm filter-input"
                                        value="{{ request('date_to') }}" style="width:150px;">
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

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <table id="inventory-table" class="table table-striped table-bordered w-100"
                    data-route="{{ route('admin.inventory.data') }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Batch No</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Summary</th>
                            <th>Total Qty</th>
                            <th>Subtotal Cost</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>

    @can('inventories.create')
        <div class="modal fade seed-all-inventory-modal" id="seed-all-inventory-modal" tabindex="-1" aria-hidden="true" style="z-index: 99999;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-success text-white border-0 px-4 py-3">
                        <div>
                            <h5 class="modal-title mb-1">Seed Inventory for All Products</h5>
                            <p class="mb-0 small text-white-75">Creates stock rows for every product and variant.</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.inventory.seed-all') }}" class="modal-body px-4 py-4">
                        @csrf
                        <div class="alert alert-warning border-0 mb-4">
                            This will create or refresh inventory rows for every product and variant in the selected location.
                            The inventory cost price will use the lower value between the product price and the variant price.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="seed-location-type" class="form-label fw-semibold">Location Type</label>
                                <select name="location_type" id="seed-location-type" class="form-select" required>
                                    <option value="">Select location type</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="store">Store</option>
                                </select>
                            </div>
                            <div class="col-md-4 seed-location-wrapper d-none" id="seed-warehouse-wrapper">
                                <label for="seed-warehouse-id" class="form-label fw-semibold">Warehouse</label>
                                <select name="warehouse_id" id="seed-warehouse-id" class="form-select">
                                    <option value="">Select warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 seed-location-wrapper d-none" id="seed-store-wrapper">
                                <label for="seed-store-id" class="form-label fw-semibold">Store</label>
                                <select name="store_id" id="seed-store-id" class="form-select">
                                    <option value="">Select store</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="seed-quantity" class="form-label fw-semibold">Quantity per item</label>
                                <input type="number" name="quantity" id="seed-quantity" class="form-control" value="100" min="1" step="1" required>
                            </div>
                            <div class="col-md-12">
                                <label for="seed-notes" class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" id="seed-notes" class="form-control" rows="3" maxlength="500" placeholder="Optional note for the batch"></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-bolt me-1"></i> Seed Inventory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        window.warehouseList = @json($warehouses->map(fn($w) => ['id' => $w->id, 'name' => $w->warehouse_name]));
        window.storeList     = @json($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->store_name]));
    </script>
    <script src="{{ asset('admin_assets/js/datatableJs/inventory.js') }}"></script>
    <script src="{{ asset('admin_assets/js/inventoryFilterBar.js') }}"></script>
    @can('inventories.create')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const seedModal = document.getElementById('seed-all-inventory-modal');
                if (seedModal && seedModal.parentElement !== document.body) {
                    document.body.appendChild(seedModal);
                }

                const locationType = document.getElementById('seed-location-type');
                const warehouseWrapper = document.getElementById('seed-warehouse-wrapper');
                const storeWrapper = document.getElementById('seed-store-wrapper');
                const warehouseInput = document.getElementById('seed-warehouse-id');
                const storeInput = document.getElementById('seed-store-id');

                function syncSeedLocationFields() {
                    const value = locationType.value;

                    warehouseWrapper.classList.toggle('d-none', value !== 'warehouse');
                    storeWrapper.classList.toggle('d-none', value !== 'store');

                    warehouseInput.required = value === 'warehouse';
                    storeInput.required = value === 'store';

                    if (value !== 'warehouse') {
                        warehouseInput.value = '';
                    }
                    if (value !== 'store') {
                        storeInput.value = '';
                    }
                }

                locationType?.addEventListener('change', syncSeedLocationFields);
                syncSeedLocationFields();
            });
        </script>
    @endcan
@endpush

@push('styles')
    <style>
        .seed-all-inventory-modal {
            z-index: 99999 !important;
        }

        .modal-backdrop.show {
            z-index: 99998 !important;
        }
    </style>
@endpush
