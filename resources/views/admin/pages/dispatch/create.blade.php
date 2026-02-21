@extends('admin.layouts.app')
@section('title', 'Create Dispatch')

@section('content')
    <div class="container-fluid py-4">
        @php
            $orderItems = $order->orderItems ?? [];
        @endphp

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Create Dispatch</h4>
                <p class="text-secondary small mb-0">Dispatch order items — full order or item-by-item</p>
                @if (isset($order) && $order->status)
                    <div class="mt-2">
                        <span
                            class="badge 
                            @if ($order->status === 'partially_dispatched') bg-warning text-dark
                            @elseif($order->status === 'dispatched') bg-primary
                            @elseif($order->status === 'delivered') bg-success
                            @elseif($order->status === 'cancelled') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                @endif
            </div>
            <a href="{{ route('admin.dispatch.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <form id="dispatchForm" method="POST" action="{{ route('admin.dispatch.store') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ isset($order) ? $order->id : '' }}">

            {{-- Section 1: Order Display --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold letter-spacing-1 border-bottom pb-2 mb-3">
                        Order Details
                    </p>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-medium">Order Number</label>
                            <div class="form-control-plaintext fw-semibold fs-5">
                                #{{ isset($order) ? $order->order_number ?? $order->id : '—' }}
                            </div>
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
                                    <th class="text-uppercase text-muted small fw-semibold">Ordered</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Available Stock</th>
                                    <th class="text-uppercase text-muted small fw-semibold" style="width: 130px;">Dispatch
                                        Qty</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Location <br><span
                                            class="fw-normal text-muted small">(Store / Warehouse)</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $stores =
                                        isset($order) && property_exists($order, 'stores')
                                            ? $order->stores
                                            : $stores ?? [];
                                    $warehouses =
                                        isset($order) && property_exists($order, 'warehouses')
                                            ? $order->warehouses
                                            : $warehouses ?? [];
                                @endphp
                                @if (count($orderItems))
                                    @foreach ($orderItems as $item)
                                        @php
                                            $alreadyDispatched = $item->dispatched_qty ?? 0;
                                            $remainingQty = $item->quantity - $alreadyDispatched;
                                        @endphp
                                        {{-- Show dispatched row if any dispatched --}}
                                        @if ($alreadyDispatched > 0)
                                            <tr class="table-success">
                                                <td class="fw-medium">
                                                    {{ $item->product ? $item->product->getTranslation('name', 'en') ?? ($item->product->name ?? '—') : '—' }}
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
                                                <td><span class="font-monospace fw-medium text-secondary">{{ $alreadyDispatched }}</span></td>
                                                <td>—</td>
                                                <td><span class="fw-semibold text-success">Dispatched</span></td>
                                                <td><span class="badge bg-success-subtle text-success">Dispatched</span></td>
                                            </tr>
                                        @endif
                                        {{-- Show remaining row if any left to dispatch --}}
                                        @if ($remainingQty > 0)
                                            <tr @if ($alreadyDispatched > 0) class="table-warning" @endif>
                                                <td class="fw-medium">
                                                    {{ $item->product ? $item->product->getTranslation('name', 'en') ?? ($item->product->name ?? '—') : '—' }}
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
                                                <td><span
                                                        class="font-monospace fw-medium text-secondary">{{ $item->quantity }}</span>
                                                </td>
                                                <td><span
                                                        class="font-monospace fw-medium text-success">{{ $item->variant ? $item->variant->total_stock ?? ($item->variant->getTotalStockAttribute() ?? '—') : '—' }}</span>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="order_item_id[]"
                                                        value="{{ $item->id }}">
                                                    <input type="number" name="quantity[]" min="0"
                                                        max="{{ $remainingQty }}" value="0"
                                                        class="form-control text-center font-monospace fw-medium dispatch-qty-input"
                                                        data-item-id="{{ $item->id }}" step="1">
                                                    <div class="small text-warning mt-1">Remaining: {{ $remainingQty }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-2 mb-1">
                                                        @php
                                                            $variantInventories =
                                                                $item->variant &&
                                                                $item->variant->relationLoaded('inventory')
                                                                    ? $item->variant->inventory
                                                                    : $item->variant->inventory ?? [];
                                                            $storeQtys = [];
                                                            $warehouseQtys = [];
                                                            $storeCount = 0;
                                                            $warehouseCount = 0;
                                                            foreach ($variantInventories as $inv) {
                                                                if ($inv->store_id) {
                                                                    $storeQtys[$inv->store_id] =
                                                                        ($storeQtys[$inv->store_id] ?? 0) +
                                                                        $inv->quantity;
                                                                }
                                                                if ($inv->warehouse_id) {
                                                                    $warehouseQtys[$inv->warehouse_id] =
                                                                        ($warehouseQtys[$inv->warehouse_id] ?? 0) +
                                                                        $inv->quantity;
                                                                }
                                                            }
                                                            foreach ($stores as $store) {
                                                                if (
                                                                    isset($storeQtys[$store->id]) &&
                                                                    $storeQtys[$store->id] > 0
                                                                ) {
                                                                    $storeCount++;
                                                                }
                                                            }
                                                            foreach ($warehouses as $wh) {
                                                                if (
                                                                    isset($warehouseQtys[$wh->id]) &&
                                                                    $warehouseQtys[$wh->id] > 0
                                                                ) {
                                                                    $warehouseCount++;
                                                                }
                                                            }
                                                        @endphp
                                                        @if ($storeCount + $warehouseCount === 1)
                                                            @if ($storeCount === 1)
                                                                <select name="inventory_id[]"
                                                                    class="form-select form-select-sm"
                                                                    style="min-width: 150px;">
                                                                    <option value="">Select Location</option>
                                                                    @foreach ($variantInventories as $inv)
                                                                        @if ($inv->store_id && isset($storeQtys[$inv->store_id]) && $storeQtys[$inv->store_id] > 0)
                                                                            <option value="{{ $inv->id }}">
                                                                                {{ method_exists($inv->store, 'getTranslation') ? $inv->store->getTranslation('store_name', 'en') : $inv->store->store_name ?? 'N/A' }}
                                                                                (Qty: {{ $storeQtys[$inv->store_id] }})
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @elseif($warehouseCount === 1)
                                                                <select name="inventory_id[]"
                                                                    class="form-select form-select-sm"
                                                                    style="min-width: 150px;">
                                                                    <option value="">Select Location</option>
                                                                    @foreach ($variantInventories as $inv)
                                                                        @if ($inv->warehouse_id && isset($warehouseQtys[$inv->warehouse_id]) && $warehouseQtys[$inv->warehouse_id] > 0)
                                                                            <option value="{{ $inv->id }}">
                                                                                {{ method_exists($inv->warehouse, 'getTranslation') ? $inv->warehouse->getTranslation('warehouse_name', 'en') : $inv->warehouse->warehouse_name ?? 'N/A' }}
                                                                                (Qty:
                                                                                {{ $warehouseQtys[$inv->warehouse_id] }})
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        @elseif($storeCount + $warehouseCount > 1)
                                                            <select class="form-select form-select-sm location-type-select"
                                                                onchange="toggleLocationInputs(this)"
                                                                style="min-width: 140px;">
                                                                <option value="">Select Location Type</option>
                                                                @if ($storeCount > 0)
                                                                    <option value="store">Store</option>
                                                                @endif
                                                                @if ($warehouseCount > 0)
                                                                    <option value="warehouse">Warehouse</option>
                                                                @endif
                                                            </select>
                                                            <select name="inventory_id[]"
                                                                class="form-select form-select-sm store-dropdown d-none"
                                                                disabled style="min-width: 150px;">
                                                                <option value="">Select Store</option>
                                                                @foreach ($variantInventories as $inv)
                                                                    @if ($inv->store_id && isset($storeQtys[$inv->store_id]) && $storeQtys[$inv->store_id] > 0)
                                                                        <option value="{{ $inv->id }}">
                                                                            {{ method_exists($inv->store, 'getTranslation') ? $inv->store->getTranslation('store_name', 'en') : $inv->store->store_name ?? 'N/A' }}
                                                                            — Qty: {{ $storeQtys[$inv->store_id] }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                            <select name="inventory_id[]"
                                                                class="form-select form-select-sm warehouse-dropdown d-none"
                                                                disabled style="min-width: 150px;">
                                                                <option value="">Select Warehouse</option>
                                                                @foreach ($variantInventories as $inv)
                                                                    @if ($inv->warehouse_id && isset($warehouseQtys[$inv->warehouse_id]) && $warehouseQtys[$inv->warehouse_id] > 0)
                                                                        <option value="{{ $inv->id }}">
                                                                            {{ method_exists($inv->warehouse, 'getTranslation') ? $inv->warehouse->getTranslation('warehouse_name', 'en') : $inv->warehouse->warehouse_name ?? 'N/A' }}
                                                                            — Qty: {{ $warehouseQtys[$inv->warehouse_id] }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <select name="inventory_id[]" class="d-none" disabled></select>
                                                            <span class="badge bg-danger-subtle text-danger">Out of
                                                                stock</span>
                                                        @endif
                                                    </div>
                                                </td>
                                        @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
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
                        <button type="submit"
                            class="btn btn-primary d-inline-flex align-items-center gap-2 fw-semibold px-4">
                            <i class="bi bi-truck"></i> Create Dispatch
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
        // Internal script to dynamically enable/disable inputs so empty arrays aren't sent for hidden fields
        function toggleLocationInputs(selectElement) {
            const container = selectElement.parentElement;
            const storeDropdown = container.querySelector('.store-dropdown');
            const warehouseDropdown = container.querySelector('.warehouse-dropdown');

            if (selectElement.value === 'store') {
                if (storeDropdown) {
                    storeDropdown.classList.remove('d-none');
                    storeDropdown.disabled = false;
                    storeDropdown.required = true;
                }
                if (warehouseDropdown) {
                    warehouseDropdown.classList.add('d-none');
                    warehouseDropdown.disabled = true;
                    warehouseDropdown.required = false;
                }
            } else if (selectElement.value === 'warehouse') {
                if (warehouseDropdown) {
                    warehouseDropdown.classList.remove('d-none');
                    warehouseDropdown.disabled = false;
                    warehouseDropdown.required = true;
                }
                if (storeDropdown) {
                    storeDropdown.classList.add('d-none');
                    storeDropdown.disabled = true;
                    storeDropdown.required = false;
                }
            } else {
                // If they deselect, disable both
                if (storeDropdown) {
                    storeDropdown.classList.add('d-none');
                    storeDropdown.disabled = true;
                    storeDropdown.required = false;
                }
                if (warehouseDropdown) {
                    warehouseDropdown.classList.add('d-none');
                    warehouseDropdown.disabled = true;
                    warehouseDropdown.required = false;
                }
            }
        }
    </script>
@endsection

@push('scripts')
    {{-- You can safely remove the old dispatch-panel.js script if the internal one does everything you need --}}
@endpush
