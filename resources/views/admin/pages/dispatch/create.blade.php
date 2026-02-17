
{{-- Dispatch Create Page --}}
<div class="dispatch-page-container">
    <div class="dispatch-header">
        <div class="dispatch-header-left">
            <div class="dispatch-icon-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2" />
                    <path d="M16 8h4l3 5v3h-7V8z" />
                    <circle cx="5.5" cy="18.5" r="2.5" />
                    <circle cx="18.5" cy="18.5" r="2.5" />
                </svg>
            </div>
            <div>
                <h2 class="dispatch-page-title">
                    @if(isset($order))
                        Dispatch for Order #{{ $order->order_number ?? $order->id }}
                    @else
                        Create Dispatch
                    @endif
                </h2>
                <div class="dispatch-page-subtitle">
                    @if(isset($order))
                        <strong>Customer:</strong> {{ $order->customer_name ?? '-' }}<br>
                        <strong>Date:</strong> {{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}<br>
                        <strong>Status:</strong> {{ ucfirst($order->status ?? '-') }}
                    @else
                        Select an order to view details
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="dispatchForm" method="POST" action="{{ route('admin.dispatch.store') }}">
        @csrf
        <div class="dispatch-body">
            <input type="hidden" id="dispatch_order_id" name="order_id" value="">

                    {{-- Section: Select Order --}}
                    <div class="dispatch-section">
                        <div class="dispatch-section-label">
                            <span class="dispatch-section-number">01</span>
                            <span class="dispatch-section-text">Select Order</span>
                        </div>
                        <div class="dispatch-fields-grid">
                            <div class="dispatch-field-group dispatch-field-full">
                                <label for="order_id_display" class="dispatch-label">Order</label>
                                <input type="text" class="dispatch-select" id="order_id_display" name="order_id_display" value="" readonly placeholder="Order ID will appear here" />
                                <input type="hidden" id="order_id" name="order_id" value="" />
                            </div>
                        </div>
                    </div>

                    {{-- Section: Order Items List --}}
                    <div class="dispatch-section">
                        <div class="dispatch-section-label">
                            <span class="dispatch-section-number">02</span>
                            <span class="dispatch-section-text">Order Items to Dispatch</span>
                        </div>
                        <div class="dispatch-table-wrapper" id="dispatch-items-table-wrapper">
                            <table class="dispatch-table" id="dispatch-items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Ordered Qty</th>
                                        <th>Dispatch Qty</th>
                                        <th>Inventory Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Rows populated by JS --}}
                                </tbody>
                            <style>
                                /* Ensure the inventory select is wide enough for names and quantities */
                                .dispatch-table .inventory-select {
                                    min-width: 180px;
                                    max-width: 100%;
                                }
                                /* Make the dispatch qty input smaller for better fit */
                                .dispatch-table .dispatch-qty-input {
                                    width: 80px;
                                }
                            </style>
                            </table>
                        </div>
                    </div>

                    {{-- Section: Select Store & Warehouse --}}
                    <div class="dispatch-section">
                        <div class="dispatch-section-label">
                            <span class="dispatch-section-number">03</span>
                            <span class="dispatch-section-text">Select Dispatch Location</span>
                        </div>
                        <div class="dispatch-fields-grid">
                            <div class="dispatch-field-group">
                                <label for="dispatch_from_store_id" class="dispatch-label">Store</label>
                                <select class="dispatch-select" id="dispatch_from_store_id" name="dispatch_from_store_id">
                                    <option value="">Select Store</option>
                                </select>
                            </div>
                            <div class="dispatch-field-group">
                                <label for="dispatch_from_warehouse_id" class="dispatch-label">Warehouse</label>
                                <select class="dispatch-select" id="dispatch_from_warehouse_id" name="dispatch_from_warehouse_id">
                                    <option value="">Select Warehouse</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Dispatch Details --}}
                    <div class="dispatch-section">
                        <div class="dispatch-section-label">
                            <span class="dispatch-section-number">02</span>
                            <span class="dispatch-section-text">Dispatch Details</span>
                        </div>

                        <div class="dispatch-fields-grid">
                            <div class="dispatch-field-group">
                                <label for="dispatch_method" class="dispatch-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3m4 0h1a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1m-6-6l4 4-4 4" />
                                    </svg>
                                    Dispatch Method
                                </label>
                                <div class="dispatch-select-wrapper">
                                    <select class="dispatch-select" id="dispatch_method" name="dispatch_method">
                                        <option value="courier">🚚 Courier</option>
                                        <option value="pickup">🏪 Pickup</option>
                                    </select>
                                    <span class="dispatch-select-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="dispatch-field-group dispatch-field-full">
                                <label for="remarks" class="dispatch-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                    Remarks
                                </label>
                                <textarea class="dispatch-textarea" id="remarks" name="remarks" rows="3"
                                    placeholder="Add any notes or special instructions…"></textarea>
                            </div>
                        </div>
                    </div>
                </div>


        <div class="dispatch-footer">
            <a href="{{ url()->previous() }}" class="dispatch-btn dispatch-btn-cancel">Cancel</a>
            <button type="submit" class="dispatch-btn dispatch-btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2" />
                    <path d="M16 8h4l3 5v3h-7V8z" />
                    <circle cx="5.5" cy="18.5" r="2.5" />
                    <circle cx="18.5" cy="18.5" r="2.5" />
                </svg>
                Create Dispatch
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script src="{{ asset('admin_assets/js/dispatch-create.js') }}"></script>
@endpush
