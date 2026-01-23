@extends('admin.layouts.app')

@section('title', 'Batch Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Batch Details: <span class="text-primary">{{ $inventory->batch_no }}</span>
                            </h4>
                        </div>
                        <div>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- 1. Batch Summary Section --}}
                        <div class="card shadow-none border bg-soft-light mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    {{-- Location Info --}}
                                    <div class="col-md-4 border-end">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Destination</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="p-2 bg-white rounded shadow-sm text-primary me-3">
                                                <i class="bi bi-geo-alt-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Location Type</small>
                                                <span class="fw-bold text-capitalize">{{ $inventory->location_type }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 bg-white rounded shadow-sm text-info me-3">
                                                <i class="bi bi-building fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Name</small>
                                                <span class="fw-bold">
                                                    @if ($inventory->location_type === 'warehouse')
                                                        {{ $inventory->warehouse->warehouse_name ?? '-' }}
                                                    @else
                                                        {{ $inventory->store->store_name ?? '-' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Meta Info --}}
                                    <div class="col-md-4 border-end">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Batch Info</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Date Created:</small><br>
                                            <span
                                                class="fw-bold">{{ $inventory->created_at->format('d M Y, h:i A') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Status:</small><br>
                                            @if ($inventory->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($inventory->status === 'depleted')
                                                <span class="badge bg-danger">Depleted</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Notes/Stats --}}
                                    <div class="col-md-4">
                                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Summary</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Total Items:</small><br>
                                            <span class="fw-bold">{{ $batchItems->count() }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Reference / Notes:</small><br>
                                            <span class="fst-italic">{{ $inventory->notes ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Items Table --}}
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>Product List</h5>
                        </div>


                        <div class="table-responsive rounded border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 35%">Product</th>
                                        <th style="width: 20%">Variant</th>
                                        <th style="width: 15%">Unit Price</th>
                                        <th style="width: 10%">Quantity</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 5%">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($batchItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $item->product->name ?? 'Unknown Product' }}</span>
                                                    <small class="text-muted">SKU: {{ $item->product->sku ?? '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($item->variant)
                                                    <span class="badge bg-soft-info text-dark">{{ $item->variant->variant_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($item->cost_price, 2) }}</td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($item->quantity) }}</span>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                ${{ number_format($item->cost_price * $item->quantity, 2) }}
                                            </td>
                                            <td>
                                                <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#history-{{ $item->id }}" aria-expanded="false" aria-controls="history-{{ $item->id }}">
                                                    <i class="bi bi-caret-down-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="history-{{ $item->id }}">
                                            <td colspan="7" class="bg-light">
                                                <div>
                                                    <h6 class="mb-2 text-secondary"><i class="bi bi-caret-down-fill me-2"></i>Batch History for this Product</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover align-middle mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Date/Time</th>
                                                                    <th>User</th>
                                                                    <th>Type</th>
                                                                    <th>Description</th>
                                                                    <th>Before Qty</th>
                                                                    <th>Change</th>
                                                                    <th>Balance</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $history = $productBatchHistory[$item->id] ?? []; @endphp
                                                                @forelse($history as $txn)
                                                                    <tr>
                                                                        <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                                                        <td>
                                                                            @php
                                                                                $user = $txn->created_by ? \App\Models\Admin::find($txn->created_by) : null;
                                                                            @endphp
                                                                            {{ $user ? $user->name : 'System' }}
                                                                        </td>
                                                                        <td><span class="badge bg-info text-dark text-uppercase">{{ $txn->type }}</span></td>
                                                                        <td>{{ $txn->description }}</td>
                                                                        <td>{{ $txn->before_quantity ?? '-' }}</td>
                                                                        <td>{{ $txn->quantity_change ?? $txn->quantity }}</td>
                                                                        <td>{{ $txn->after_quantity ?? '-' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="7" class="text-center text-muted">No history found for this product in this batch.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                                No items found in this batch.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold">{{ $batchItems->sum('quantity') }}</td>
                                        <td class="fw-bold text-primary">
                                            ${{ number_format($batchItems->sum(function ($i) {return $i->cost_price * $i->quantity;}),2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
