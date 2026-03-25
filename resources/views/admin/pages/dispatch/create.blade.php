@extends('admin.layouts.app')
@section('title', 'Create Dispatch')

@section('content')
    <div class="container-fluid py-4">
        @php
            $orderItems = $order->orderItems ?? [];
            $orderDispatches = \App\Models\Dispatch::where('order_id', isset($order) ? $order->id : 0)
                ->whereIn('status', ['dispatched', 'delivered'])
                ->get();
        @endphp

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-semibold mb-1">Create Dispatch</h4>
                <p class="text-secondary small mb-0">Dispatch order items — split across one or multiple locations</p>
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

        {{-- Main form — JS injects hidden inputs before submit --}}
        <form id="dispatchForm" method="POST" action="{{ route('admin.dispatch.store') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ isset($order) ? $order->id : '' }}">
            <input type="hidden" name="dispatch_method" id="form_dispatch_method" value="courier">
            <input type="hidden" name="remarks" id="form_remarks" value="">
            {{-- Per-item splits injected as dispatches[i][order_item_id|inventory_id|quantity] --}}
            <div id="dispatch-hidden-inputs"></div>

            {{-- Section 1: Order Details --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold letter-spacing-1 border-bottom pb-2 mb-3">Order
                        Details</p>
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

            {{-- Section 2: Order Items --}}
            <div class="card border rounded-3 mb-4">
                <div class="card-body p-4">
                    <p class="text-uppercase text-muted small fw-semibold border-bottom pb-2 mb-3">Order Items</p>
                    <div class="table-responsive border rounded-2">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-muted small fw-semibold">Product</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Variant</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Qty Status</th>
                                    <th class="text-uppercase text-muted small fw-semibold">Allocation</th>
                                    <th class="text-uppercase text-muted small fw-semibold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($orderItems))
                                    @foreach ($orderItems as $item)
                                        @php
                                            $alreadyDispatched = $orderDispatches
                                                ->where('order_item_id', $item->id)
                                                ->sum('quantity');
                                            $remainingQty = max(0, $item->quantity - $alreadyDispatched);
                                        @endphp
                                        <tr data-item-id="{{ $item->id }}">
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

                                            <td>
                                                <div class="mb-1">
                                                    <span class="text-muted small">Ordered:</span>
                                                    <span class="fw-bold font-monospace">{{ $item->quantity }}</span>
                                                </div>
                                                @if ($alreadyDispatched > 0)
                                                    <div class="small text-success mb-1">
                                                        <i class="bi bi-check-circle"></i> Dispatched:
                                                        <span class="fw-bold font-monospace">{{ $alreadyDispatched }}</span>
                                                    </div>
                                                @endif
                                                @if ($remainingQty > 0)
                                                    <div class="small text-warning">
                                                        <i class="bi bi-hourglass-split"></i> Remaining:
                                                        <span class="fw-bold font-monospace">{{ $remainingQty }}</span>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Allocation summary (written by JS after modal) --}}
                                            <td>
                                                @if ($remainingQty > 0)
                                                    <div id="allocation-summary-{{ $item->id }}"
                                                        class="small text-muted fst-italic">
                                                        Not yet allocated
                                                    </div>
                                                @else
                                                    <div
                                                        class="d-inline-flex align-items-center gap-2 text-success small fw-semibold">
                                                        <i class="bi bi-check2-all fs-6"></i> Fully Dispatched
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($remainingQty > 0)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-1 open-dispatch-modal"
                                                        data-item-id="{{ $item->id }}"
                                                        data-order-id="{{ $order->id }}"
                                                        data-remaining="{{ $remainingQty }}"
                                                        data-product="{{ $item->product ? $item->product->getTranslation('name', 'en') ?? ($item->product->name ?? 'Product') : 'Product' }}"
                                                        data-variant="{{ $item->variant ? $item->variant->getTranslation('variant_name', 'en') ?? ($item->variant->variant_name ?? '') : '' }}">
                                                        <i class="bi bi-truck"></i> Dispatch
                                                    </button>
                                                @else
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2">
                                                        <i class="bi bi-check2-all me-1"></i> Done
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
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
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Dispatch Method</label>
                            <select class="form-select" id="dispatch_method_select">
                                <option value="courier" selected>Courier</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-medium">
                                Remarks <span class="text-muted">(optional)</span>
                            </label>
                            <textarea class="form-control" id="remarks_textarea" rows="2" placeholder="Add any notes about this dispatch..."></textarea>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <div id="allocation-status-msg" class="small text-muted">
                            Allocate at least one item to create a dispatch.
                        </div>
                        <button type="submit" id="submitDispatchBtn"
                            class="btn btn-primary d-inline-flex align-items-center gap-2 fw-semibold px-4" disabled>
                            <i class="bi bi-truck"></i> Create Dispatch
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         DISPATCH MODAL
    ════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="dispatchModal" tabindex="-1" aria-labelledby="dispatchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-semibold mb-0" id="dispatchModalLabel">
                            <i class="bi bi-truck me-2 text-primary"></i> Dispatch Item
                        </h5>
                        <div class="small text-muted mt-1" id="modal-item-subtitle"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">

                    {{-- Item qty summary bar --}}
                    <div class="row g-0 rounded-2 bg-light border mb-4 overflow-hidden">
                        <div class="col-4 p-3 border-end text-center">
                            <div class="small text-muted mb-1">To dispatch</div>
                            <div class="fw-bold fs-5 font-monospace text-warning" id="modal-remaining-display">0</div>
                        </div>
                        <div class="col-4 p-3 border-end text-center">
                            <div class="small text-muted mb-1">Allocated</div>
                            <div class="fw-bold fs-5 font-monospace text-primary" id="modal-allocated-display">0</div>
                        </div>
                        <div class="col-4 p-3 text-center">
                            <div class="small text-muted mb-1">Still needed</div>
                            <div class="fw-bold fs-5 font-monospace text-danger" id="modal-still-needed-display">0</div>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div id="modal-loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="small text-muted mt-2">Loading inventory...</div>
                    </div>

                    {{-- Error --}}
                    <div id="modal-error" class="alert alert-danger d-none py-2"></div>

                    {{-- Split rows --}}
                    <div id="split-rows-container"></div>

                    {{-- Add another location --}}
                    <button type="button" id="add-split-btn"
                        class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 mt-2 d-none">
                        <i class="bi bi-plus-circle"></i> Add Another Location
                    </button>

                    {{-- Inline validation --}}
                    <div id="modal-validation-msg" class="alert mt-3 d-none small py-2"></div>

                </div>

                <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="small text-muted" id="modal-footer-hint"></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm-dispatch-btn"
                            class="btn btn-primary fw-semibold d-inline-flex align-items-center gap-2" disabled>
                            <i class="bi bi-check-lg"></i> Confirm Allocation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Split-row template (cloned by JS) --}}
    <template id="split-row-template">
        <div class="split-row border rounded-2 p-3 mb-2 bg-white">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-medium mb-1">Location</label>
                    <select class="form-select form-select-sm split-location-select">
                        <option value="">— Select location —</option>
                    </select>
                    <div class="small mt-1 location-stock-hint text-muted"></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-medium mb-1">Qty to dispatch</label>
                    <input type="number" class="form-control form-control-sm split-qty-input font-monospace"
                        min="1" value="" placeholder="0">
                </div>
                <div class="col-md-2 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-split-btn" title="Remove row">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script>
        // Expose route to JS file
        window.ITEM_INVENTORY_URL = @json(route('admin.dispatch.item-inventory'));
    </script>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/admin-dispatch-modal.js') }}"></script>
    @endpush
@endsection
