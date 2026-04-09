@extends('admin.layouts.app')

@section('title', isset($referenceItem) ? 'Edit Inventory Batch' : 'Inward New Stock')
@section('header-title', isset($referenceItem) ? 'Edit Inventory Batch' : 'Inward New Stock')
@section('header-description', isset($referenceItem) ? 'Modify existing inventory batch details' : 'Create a new inventory batch by adding products and their quantities')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.inventory.create', isset($referenceItem) ? $referenceItem : null) as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid pt-3">
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
                    <form action="{{ isset($referenceItem) ? route('admin.inventory.update') : route('admin.inventory.store') }}"
                          method="POST"
                          id="batch-form">

                        @csrf
                        @if(isset($referenceItem))
                            @method('PUT')
                            {{-- HIDDEN ID for Update Route --}}
                            <input type="hidden" name="id" value="{{ $referenceItem->id }}">
                        @endif

                        {{-- SECTION 1: GLOBAL BATCH DETAILS --}}
                        <div class="card mb-4 shadow-none border bg-soft-primary">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-12 mb-2">
                                        <h6 class="fw-bold text-primary mb-0">
                                            <i class="bi bi-geo-alt-fill me-2"></i>Step 1: Batch Destination
                                        </h6>
                                    </div>

                                    {{-- Determine Initial Values (Old Input > DB Value > Default) --}}
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
                                        @error('location_type') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Warehouse Dropdown --}}
                                    <div class="col-md-4" id="warehouse_div" style="{{ $locType !== 'warehouse' ? 'display:none;' : '' }}">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Select Warehouse <span class="text-danger">*</span></label>
                                        @php
                                            $selectedWarehouse = isset($warehouses) ? $warehouses->firstWhere('id', $selWarehouse) : null;
                                            $selectedWarehouseText = $selectedWarehouse
                                                ? ($selectedWarehouse->getTranslation('warehouse_name', app()->getLocale())
                                                    ?? $selectedWarehouse->getTranslation('warehouse_name', 'en')
                                                    ?? ('Warehouse #' . $selWarehouse))
                                                : ('Warehouse #' . $selWarehouse);
                                        @endphp
                                        <select class="select2-ajax browser-default form-control" data-allow-clear="true" data-error="#warehouse-error-div" data-option-url="{{ route('admin.lists.warehouses') }}" data-placeholder="Select warehouse" id="warehouse_id" name="warehouse_id">
                                            @if($selWarehouse)
                                                <option selected value="{{ $selWarehouse }}">{{ $selectedWarehouseText }}</option>
                                            @endif
                                        </select>
                                        <div id="warehouse-error-div"></div>
                                        @error('warehouse_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Store Dropdown --}}
                                    <div class="col-md-4" id="store_div" style="{{ $locType !== 'store' ? 'display:none;' : '' }}">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Select Store <span class="text-danger">*</span></label>
                                        @php
                                            $selectedStore = isset($stores) ? $stores->firstWhere('id', $selStore) : null;
                                            $selectedStoreText = $selectedStore
                                                ? ($selectedStore->getTranslation('store_name', app()->getLocale())
                                                    ?? $selectedStore->getTranslation('store_name', 'en')
                                                    ?? $selectedStore->name
                                                    ?? ('Store #' . $selStore))
                                                : ('Store #' . $selStore);
                                        @endphp
                                        <select class="select2-ajax browser-default form-control" data-allow-clear="true" data-error="#store-error-div" data-option-url="{{ route('admin.lists.stores') }}" data-placeholder="Select store" id="store_id" name="store_id">
                                            @if($selStore)
                                                <option selected value="{{ $selStore }}">{{ $selectedStoreText }}</option>
                                            @endif
                                        </select>
                                        <div id="store-error-div"></div>
                                        @error('store_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                                    <button type="button" class="btn btn-success btn-sm rounded-pill shadow-sm" id="add-row-btn">
                                        <i class="bi bi-plus-lg me-1"></i> Add Item
                                    </button>
                                </div>

                                {{-- General Validation Errors --}}
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
                                                <th style="width: 15%">Unit Price <span class="text-warning">*</span></th>
                                                <th style="width: 15%">Quantity <span class="text-warning">*</span></th>
                                                <th style="width: 50px" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body" class="bg-white">

                                            {{-- CASE A: Validation Failed (Prioritize Old Input) --}}
                                            @if(old('items'))
                                                @foreach(old('items') as $key => $item)
                                                    <tr class="item-row">
                                                        <td>
                                                            <select id="item_product_{{ $key }}" class="select2-ajax browser-default form-control product-select" data-allow-clear="true" data-error="#product-error-div" data-option-url="{{ route('admin.lists.products') }}" data-placeholder="Select Product" name="items[{{ $key }}][product_id]">
                                                                <option value="">Select Product</option>
                                                                @if(!empty($item['product_id']))
                                                                    <option value="{{ $item['product_id'] }}" selected>Product #{{ $item['product_id'] }}</option>
                                                                @endif
                                                            </select>
                                                            <div id="product-error-div"></div>
                                                            @error('items.' . $key . '.product_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </td>
                                                        <td>
                                                            <select class="select2-ajax browser-default form-control variant-select" data-allow-clear="true" data-error="#variant-error-div" data-option-url="{{ route('admin.lists.variants') }}" data-placeholder="Select Variant" data-depends-on="#item_product_{{ $key }}" data-dependency-param="product_id" name="items[{{ $key }}][product_variant_id]">
                                                                <option value="">Select Product First</option>
                                                                @if(!empty($item['product_variant_id']))
                                                                    <option value="{{ $item['product_variant_id'] }}" selected>Variant #{{ $item['product_variant_id'] }}</option>
                                                                @endif
                                                            </select>
                                                            <div id="variant-error-div"></div>
                                                            @error('items.' . $key . '.product_variant_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">{{config('app.CURRENCY_SYMBOL')}}</span>
                                                                <input type="number" step="0.01" name="items[{{ $key }}][cost_price]" class="form-control" value="{{ $item['cost_price'] ?? '' }}" required>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="items[{{ $key }}][quantity]" class="form-control text-center" value="{{ $item['quantity'] ?? '' }}" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-row text-white" title="Remove item" aria-label="Remove item" data-bs-toggle="tooltip" data-bs-placement="top" style="width:36px;height:36px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            {{-- CASE B: Edit Mode (Load from DB) --}}
                                            @elseif(isset($batchItems) && count($batchItems) > 0)
                                                @foreach($batchItems as $key => $item)
                                                    <tr class="item-row">
                                                        <td>
                                                            <select id="item_product_{{ $key }}" class="select2-ajax browser-default form-control product-select" data-allow-clear="true" data-error="#product-error-div" data-option-url="{{ route('admin.lists.products') }}" data-placeholder="Select Product" name="items[{{ $key }}][product_id]">
                                                                @if($item->product)
                                                                    <option value="{{ $item->product_id }}" selected>
                                                                        {{ $item->product->getTranslation('name', app()->getLocale()) ?? $item->product->name ?? ('Product #' . $item->product_id) }}
                                                                    </option>
                                                                @else
                                                                    <option value="">Select Product</option>
                                                                @endif
                                                            </select>
                                                            <div id="product-error-div"></div>
                                                            @error('items.' . $key . '.product_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </td>
                                                        <td>
                                                            <select class="select2-ajax browser-default form-control variant-select" data-allow-clear="true" data-error="#variant-error-div" data-option-url="{{ route('admin.lists.variants') }}" data-placeholder="Select Variant" data-depends-on="#item_product_{{ $key }}" data-dependency-param="product_id" name="items[{{ $key }}][product_variant_id]">
                                                                @if($item->variant)
                                                                    <option value="{{ $item->product_variant_id }}" selected>
                                                                        {{ $item->variant->getTranslation('variant_name', app()->getLocale()) ?? $item->variant->getTranslation('variant_name', 'en') ?? ('Variant #' . $item->product_variant_id) }}
                                                                    </option>
                                                                @else
                                                                    <option value="">Select Product First</option>
                                                                @endif
                                                            </select>
                                                            <div id="variant-error-div"></div>
                                                            @error('items.' . $key . '.product_variant_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">{{config('app.CURRENCY_SYMBOL')}}</span>
                                                                <input type="number" step="0.01" name="items[{{ $key }}][cost_price]" class="form-control" value="{{ $item->cost_price }}" required>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="items[{{ $key }}][quantity]" class="form-control text-center" value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-row text-white" title="Remove item" aria-label="Remove item" data-bs-toggle="tooltip" data-bs-placement="top" style="width:36px;height:36px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                                                <i class="bi bi-trash-fill"></i>
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
            <select id="item_product_INDEX" name="items[INDEX][product_id]" class="select2-ajax browser-default form-control product-select" data-allow-clear="true" data-option-url="{{ route('admin.lists.products') }}" data-placeholder="Select Product" required>
                <option value="">Select Product</option>
            </select>
        </td>
        <td>
            <select name="items[INDEX][product_variant_id]" class="select2-ajax browser-default form-control variant-select" data-allow-clear="true" data-option-url="{{ route('admin.lists.variants') }}" data-placeholder="Select Variant" data-depends-on="#item_product_INDEX" data-dependency-param="product_id">
                <option value="">Select Product First</option>
            </select>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text">{{ config('app.CURRENCY_SYMBOL') }}</span>
                <input type="number" step="0.01" name="items[INDEX][cost_price]" class="form-control" placeholder="0.00" required>
            </div>
        </td>
        <td>
            <input type="number" step="0.01" name="items[INDEX][quantity]" class="form-control text-center" placeholder="Qty" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row text-white" title="Remove item" aria-label="Remove item" data-bs-toggle="tooltip" data-bs-placement="top" style="width:36px;height:36px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                <i class="bi bi-trash-fill"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/inventory.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
                try { new bootstrap.Tooltip(el); } catch (e) { /* bootstrap may not be loaded yet */ }
            });
        });
    </script>
@endpush
