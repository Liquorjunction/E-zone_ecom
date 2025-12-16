@extends('admin.layouts.app')

@section('title', isset($referenceItem) ? 'Edit Inventory Batch' : 'Inward New Stock')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">
                            {{ isset($referenceItem) ? 'Edit Batch: ' . $referenceItem->batch_no : 'Inward New Stock' }}
                        </h4>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    {{-- FORM START --}}
                    {{-- Action toggles between STORE and UPDATE routes based on existence of $referenceItem --}}
                    <form action="{{ isset($referenceItem) ? route('admin.inventory.update', $referenceItem->id) : route('admin.inventory.store') }}"
                          method="POST"
                          id="batch-form"
                          data-variants-url="{{ route('admin.inventory.get-variants') }}">

                        @csrf
                        @if(isset($referenceItem))
                            @method('PUT')
                        @endif

                        {{-- SECTION 1: GLOBAL DETAILS --}}
                        <div class="card mb-4 shadow-none border bg-soft-primary">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-12 mb-2">
                                        <h6 class="fw-bold text-primary mb-0">
                                            <i class="bi bi-geo-alt-fill me-2"></i>Step 1: Batch Details
                                        </h6>
                                    </div>

                                    {{-- Determine Values (Old Input > DB Value > Default) --}}
                                    @php
                                        $locType = old('location_type', $referenceItem->location_type ?? 'warehouse');
                                        $selWarehouse = old('warehouse_id', $referenceItem->warehouse_id ?? '');
                                        $selStore = old('store_id', $referenceItem->store_id ?? '');
                                        $notes = old('notes', $referenceItem->notes ?? '');
                                    @endphp

                                    {{-- Location Type --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Location Type <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="location_type" id="lt_warehouse" value="warehouse"
                                                    {{ $locType === 'warehouse' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lt_warehouse">Warehouse</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="location_type" id="lt_store" value="store"
                                                    {{ $locType === 'store' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lt_store">Store</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Warehouse Dropdown --}}
                                    <div class="col-md-4" id="warehouse_div" style="{{ $locType !== 'warehouse' ? 'display:none;' : '' }}">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Select Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                            <option value="">Choose Warehouse...</option>
                                            @foreach($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ $selWarehouse == $w->id ? 'selected' : '' }}>
                                                    {{ $w->warehouse_name ?? $w->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Store Dropdown --}}
                                    <div class="col-md-4" id="store_div" style="{{ $locType !== 'store' ? 'display:none;' : '' }}">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Select Store <span class="text-danger">*</span></label>
                                        <select name="store_id" class="form-select @error('store_id') is-invalid @enderror">
                                            <option value="">Choose Store...</option>
                                            @foreach($stores as $s)
                                                <option value="{{ $s->id }}" {{ $selStore == $s->id ? 'selected' : '' }}>
                                                    {{ $s->store_name ?? $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Notes --}}
                                    <div class="col-md-5 mt-3 mt-md-0">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Reference / Notes</label>
                                        <input type="text" name="notes" class="form-control" placeholder="e.g. PO-8832 from Supplier X" value="{{ $notes }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: ITEMS TABLE --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-box-seam me-2"></i>Step 2: Product List
                                    </h6>
                                    <button type="button" class="btn btn-success btn-sm rounded-pill" id="add-row-btn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Item
                                    </button>
                                </div>

                                {{-- Validation Errors for Items Array --}}
                                @if($errors->has('items'))
                                    <div class="alert alert-danger p-2 small">
                                        <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first('items') }}
                                    </div>
                                @endif

                                <div class="table-responsive rounded border">
                                    <table class="table table-striped table-hover align-middle mb-0" id="inventory-table">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th style="width: 30%">Product <span class="text-warning">*</span></th>
                                                <th style="width: 25%">Variant</th>
                                                <th style="width: 15%">Cost Price <span class="text-warning">*</span></th>
                                                <th style="width: 15%">Quantity <span class="text-warning">*</span></th>
                                                <th style="width: 50px" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body" class="bg-white">

                                            {{-- CASE A: Validation Failed (Prioritize Old Input) --}}
                                            @if(old('items'))
                                                @foreach(old('items') as $key => $item)
                                                    @include('admin.pages.inventory.partials.row-create', ['key' => $key, 'item' => $item, 'products' => $products])
                                                @endforeach

                                            {{-- CASE B: Edit Mode (Load from DB) --}}
                                            @elseif(isset($batchItems) && count($batchItems) > 0)
                                                @foreach($batchItems as $key => $item)
                                                    {{-- We manually construct the row here to ensure values map correctly from Model --}}
                                                    <tr class="item-row">
                                                        <td>
                                                            <select name="items[{{ $key }}][product_id]" class="form-select product-select" required>
                                                                <option value="">Select Product</option>
                                                                @foreach($products as $p)
                                                                    <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                                                        {{ $p->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="items[{{ $key }}][product_variant_id]" class="form-select variant-select">
                                                                @if($item->variant)
                                                                    <option value="{{ $item->product_variant_id }}" selected>
                                                                        {{ $item->variant->variant_name }}
                                                                    </option>
                                                                @else
                                                                    <option value="">Select Product First</option>
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" step="0.01" name="items[{{ $key }}][cost_price]" class="form-control" value="{{ $item->cost_price }}" required>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="items[{{ $key }}][quantity]" class="form-control text-center" value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-soft-danger btn-sm remove-row rounded-circle">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            {{-- CASE C: Empty (JS will add first row on load) --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ isset($referenceItem) ? 'Update Inventory Batch' : 'Save Inventory Batch' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Template for JS --}}
<template id="row-template">
    <tr class="item-row">
        <td>
            <select name="items[INDEX][product_id]" class="form-select product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][product_variant_id]" class="form-select variant-select">
                <option value="">Select Product First</option>
            </select>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" name="items[INDEX][cost_price]" class="form-control" placeholder="0.00" required>
            </div>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][quantity]" class="form-control text-center" placeholder="Qty" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-soft-danger btn-sm remove-row rounded-circle">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@push('scripts')
{{-- Include the Form Logic JS --}}
    <script src="{{ asset('admin_assets/js/inventory.js') }}"></script>
@endpush
