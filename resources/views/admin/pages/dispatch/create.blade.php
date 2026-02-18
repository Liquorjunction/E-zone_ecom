@extends('admin.layouts.app')
@section('title', 'Create Dispatch')

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Create Dispatch</h4>
                <p class="text-secondary small mb-0">Dispatch order items — full order or item-by-item</p>
            </div>
            <a href="{{ route('admin.dispatch.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <form id="dispatchForm" method="POST" action="{{ route('admin.dispatch.store') }}">
            @csrf

            {{-- Section 1: Order Display --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold letter-spacing-1 border-bottom pb-2 mb-3">Order Details</p>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-medium">Order Number</label>
                            <div class="form-control-plaintext fw-semibold fs-5">
                                #{{ isset($selectedOrder) ? ($selectedOrder->order_number ?? $selectedOrder->id) : '—' }}
                            </div>
                            <input type="hidden" name="order_id" value="{{ isset($selectedOrder) ? $selectedOrder->id : '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Dispatch Items Table --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold border-bottom pb-2 mb-3">Order Items</p>
                    <div class="table-responsive border rounded-2">
                        <table class="table table-hover align-middle mb-0" id="dispatch-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-muted small fw-semibold">Product</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Variant</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Ordered Qty</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Available Qty</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Dispatch Qty</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Location<br><span class="fw-normal text-muted small">(Store / Warehouse)</span></th>
                                    <th class="text-uppercase text-muted small fw-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($orderItems) && count($orderItems))
                                    @foreach ($orderItems as $item)
                                        <tr>
                                            <td class="fw-medium">
                                                {{ $item->product ? ($item->product->getTranslation('name', 'en') ?? $item->product->name ?? '—') : '—' }}
                                            </td>
                                            <td>
                                                @if ($item->variant && ($item->variant->getTranslation('variant_name', 'en') ?? $item->variant->variant_name))
                                                    <span class="badge bg-light text-secondary border font-monospace">
                                                        {{ $item->variant->getTranslation('variant_name', 'en') ?? $item->variant->variant_name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="font-monospace fw-medium text-secondary">{{ $item->quantity }}</span>
                                                @php
                                                    $alreadyDispatched = $item->dispatched_qty ?? 0;
                                                    $remainingQty = $item->quantity - $alreadyDispatched;
                                                @endphp
                                                @if($alreadyDispatched > 0)
                                                    <div class="small text-success">Dispatched: {{ $alreadyDispatched }}</div>
                                                @endif
                                                <div class="small text-warning">Remaining: <span class="remaining-qty" data-item-id="{{ $item->id }}">{{ $remainingQty }}</span></div>
                                            </td>
                                            <td>
                                                <span class="font-monospace fw-medium text-success">
                                                    {{ $item->variant ? ($item->variant->total_stock ?? $item->variant->getTotalStockAttribute() ?? '—') : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <input type="number"
                                                    name="dispatch_qty[{{ $item->id }}]"
                                                    min="1"
                                                    max="{{ $remainingQty }}"
                                                    value="{{ $remainingQty }}"
                                                    class="form-control text-center font-monospace fw-medium dispatch-qty-input"
                                                    data-item-id="{{ $item->id }}"
                                                    style="width: 80px;">
                                                <div class="invalid-feedback d-none" id="qty-error-{{ $item->id }}"></div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 align-items-center mb-1">
                                                    @php
                                                        $variantInventories = $item->variant && $item->variant->relationLoaded('inventory') ? $item->variant->inventory : ($item->variant->inventory ?? []);
                                                        $storeQtys = [];
                                                        $warehouseQtys = [];
                                                        foreach ($variantInventories as $inv) {
                                                            if ($inv->store_id) {
                                                                $storeQtys[$inv->store_id] = ($storeQtys[$inv->store_id] ?? 0) + $inv->quantity;
                                                            }
                                                            if ($inv->warehouse_id) {
                                                                $warehouseQtys[$inv->warehouse_id] = ($warehouseQtys[$inv->warehouse_id] ?? 0) + $inv->quantity;
                                                            }
                                                        }
                                                        $storeCount = 0;
                                                        $lastStoreId = null;
                                                        foreach ($stores as $store) {
                                                            if(isset($storeQtys[$store->id]) && $storeQtys[$store->id] > 0) {
                                                                $storeCount++;
                                                                $lastStoreId = $store->id;
                                                            }
                                                        }
                                                        $warehouseCount = 0;
                                                        $lastWarehouseId = null;
                                                        foreach ($warehouses as $wh) {
                                                            if(isset($warehouseQtys[$wh->id]) && $warehouseQtys[$wh->id] > 0) {
                                                                $warehouseCount++;
                                                                $lastWarehouseId = $wh->id;
                                                            }
                                                        }
                                                    @endphp
                                                    @if(($storeCount + $warehouseCount) === 1)
                                                        @if($storeCount === 1)
                                                            <select name="dispatch_from_store_id[{{ $item->id }}]" class="form-select form-select-sm" data-item-id="{{ $item->id }}" style="min-width: 150px;">
                                                                @foreach ($stores as $store)
                                                                    @if(isset($storeQtys[$store->id]) && $storeQtys[$store->id] > 0)
                                                                        <option value="{{ $store->id }}" selected>
                                                                            {{ method_exists($store, 'getTranslation') ? $store->getTranslation('store_name', 'en') : ($store->store_name ?? $store->name ?? 'N/A') }} (Qty: {{ $storeQtys[$store->id] }})
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        @elseif($warehouseCount === 1)
                                                            <select name="dispatch_from_warehouse_id[{{ $item->id }}]" class="form-select form-select-sm" data-item-id="{{ $item->id }}" style="min-width: 150px;">
                                                                @foreach ($warehouses as $wh)
                                                                    @if(isset($warehouseQtys[$wh->id]) && $warehouseQtys[$wh->id] > 0)
                                                                        <option value="{{ $wh->id }}" selected>
                                                                            {{ method_exists($wh, 'getTranslation') ? $wh->getTranslation('warehouse_name', 'en') : ($wh->warehouse_name ?? $wh->name ?? 'N/A') }} (Qty: {{ $warehouseQtys[$wh->id] }})
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    @else
                                                        <select name="location_type[{{ $item->id }}]" class="form-select form-select-sm location-type-select" data-item-id="{{ $item->id }}" style="min-width: 140px;">
                                                            <option value="">Select Location Type</option>
                                                            @if($storeCount > 0)
                                                                <option value="store">Store</option>
                                                            @endif
                                                            @if($warehouseCount > 0)
                                                                <option value="warehouse">Warehouse</option>
                                                            @endif
                                                        </select>
                                                        <select name="dispatch_from_store_id[{{ $item->id }}]" class="form-select form-select-sm store-dropdown d-none" data-item-id="{{ $item->id }}" style="min-width: 150px;">
                                                            <option value="">Select Store</option>
                                                            @foreach ($stores as $store)
                                                                @if(isset($storeQtys[$store->id]) && $storeQtys[$store->id] > 0)
                                                                    <option value="{{ $store->id }}">
                                                                        {{ method_exists($store, 'getTranslation') ? $store->getTranslation('store_name', 'en') : ($store->store_name ?? $store->name ?? 'N/A') }} (Qty: {{ $storeQtys[$store->id] }})
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <select name="dispatch_from_warehouse_id[{{ $item->id }}]" class="form-select form-select-sm warehouse-dropdown d-none" data-item-id="{{ $item->id }}" style="min-width: 150px;">
                                                            <option value="">Select Warehouse</option>
                                                            @foreach ($warehouses as $wh)
                                                                @if(isset($warehouseQtys[$wh->id]) && $warehouseQtys[$wh->id] > 0)
                                                                    <option value="{{ $wh->id }}">
                                                                        {{ method_exists($wh, 'getTranslation') ? $wh->getTranslation('warehouse_name', 'en') : ($wh->warehouse_name ?? $wh->name ?? 'N/A') }} (Qty: {{ $warehouseQtys[$wh->id] }})
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    @php
                                                        $variantInventories = $item->variant && $item->variant->relationLoaded('inventory') ? $item->variant->inventory : ($item->variant->inventory ?? []);
                                                        $storeQtys = [];
                                                        $warehouseQtys = [];
                                                        foreach ($variantInventories as $inv) {
                                                            if ($inv->store_id) {
                                                                $storeQtys[$inv->store_id] = ($storeQtys[$inv->store_id] ?? 0) + $inv->quantity;
                                                            }
                                                            if ($inv->warehouse_id) {
                                                                $warehouseQtys[$inv->warehouse_id] = ($warehouseQtys[$inv->warehouse_id] ?? 0) + $inv->quantity;
                                                            }
                                                        }
                                                    @endphp
                                                    @if(count($storeQtys))
                                                        <div><span class="fw-semibold">Store Qty:</span>
                                                            @foreach($stores as $store)
                                                                @if(isset($storeQtys[$store->id]))
                                                                    <span class="me-2">{{ method_exists($store, 'getTranslation') ? $store->getTranslation('store_name', 'en') : ($store->store_name ?? $store->name ?? 'N/A') }}: <span class="text-success">{{ $storeQtys[$store->id] }}</span></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(count($warehouseQtys))
                                                        <div><span class="fw-semibold">Warehouse Qty:</span>
                                                            @foreach($warehouses as $wh)
                                                                @if(isset($warehouseQtys[$wh->id]))
                                                                    <span class="me-2">{{ method_exists($wh, 'getTranslation') ? $wh->getTranslation('warehouse_name', 'en') : ($wh->warehouse_name ?? $wh->name ?? 'N/A') }}: <span class="text-primary">{{ $warehouseQtys[$wh->id] }}</span></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $hasStore = false;
                                                    $hasWarehouse = false;
                                                    foreach ($stores as $store) {
                                                        if(isset($storeQtys[$store->id]) && $storeQtys[$store->id] > 0) $hasStore = true;
                                                    }
                                                    foreach ($warehouses as $wh) {
                                                        if(isset($warehouseQtys[$wh->id]) && $warehouseQtys[$wh->id] > 0) $hasWarehouse = true;
                                                    }
                                                @endphp
                                                @if(!$hasStore && !$hasWarehouse)
                                                    <span class="badge bg-danger-subtle text-danger">Out of stock at all locations</span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-send"></i> Dispatch
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <div class="fs-3 opacity-50 mb-2">📦</div>
                                            <div>No items found for the selected order.</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Section 3: Remarks + Submit --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold border-bottom pb-2 mb-3">Additional Info</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="remarks" class="form-label small fw-medium">
                                Remarks <span class="text-muted">(optional)</span>
                            </label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                placeholder="Add any notes about this dispatch..."></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 fw-semibold px-4">
                            <i class="bi bi-truck"></i> Create Dispatch
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dispatch-panel.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dispatch-create.js') }}"></script>
@endpush
