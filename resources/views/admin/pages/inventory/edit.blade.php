@extends('admin.layouts.app')

@section('title', 'Edit Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Edit Inventory Record</h4>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST" id="edit-form"
                          data-variants-url="{{ route('admin.inventory.variants') }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" value="{{ $inventory->id }}">

                        <div class="row">
                            {{-- Product --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_select" class="form-select product-select @error('product_id') is-invalid @enderror" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id', $inventory->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Variant --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Variant</label>
                                <select name="product_variant_id" id="variant_select" class="form-select variant-select @error('product_variant_id') is-invalid @enderror">
                                    <option value="">Select Product First</option>
                                    @foreach($variants as $variant)
                                        <option value="{{ $variant->id }}"
                                            {{ old('product_variant_id', $inventory->product_variant_id) == $variant->id ? 'selected' : '' }}>
                                            {{ $variant->variant_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Location Type --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold">Location</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="lt_warehouse" value="warehouse"
                                            {{ old('location_type', $inventory->location_type) === 'warehouse' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lt_warehouse">Warehouse</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="lt_store" value="store"
                                            {{ old('location_type', $inventory->location_type) === 'store' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lt_store">Store</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Warehouse Select --}}
                            <div class="col-md-6 mb-3" id="warehouse_div">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}"
                                            {{ old('warehouse_id', $inventory->warehouse_id) == $w->id ? 'selected' : '' }}>
                                            {{ $w->warehouse_name ?? $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Store Select --}}
                            <div class="col-md-6 mb-3 d-none" id="store_div">
                                <label class="form-label">Store <span class="text-danger">*</span></label>
                                <select name="store_id" class="form-select @error('store_id') is-invalid @enderror">
                                    <option value="">Select Store</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}"
                                            {{ old('store_id', $inventory->store_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->store_name ?? $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity', $inventory->quantity) }}" required>
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price</label>
                                <input type="number" step="0.01" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror"
                                    value="{{ old('cost_price', $inventory->cost_price) }}">
                                @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch No</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                    <input type="text" name="batch_no" class="form-control-plaintext" readonly
                                        value="{{ old('batch_no', $inventory->batch_no) }}">
                                </div>
                                <small class="text-muted">Batch number is read-only to keep audit references intact.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $inventory->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $inventory->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="depleted" {{ $inventory->status == 'depleted' ? 'selected' : '' }}>Depleted</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $inventory->notes) }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/inventory.js') }}"></script>
@endpush
