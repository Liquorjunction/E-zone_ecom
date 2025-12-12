@extends('admin.layouts.app')

@section('title', 'Edit Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Edit Inventory</h4>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.update', $inventory->id ?? '') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $inventory->id ?? '' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                    <option value="">Select Product</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" {{ (old('product_id', $inventory->product_id ?? '') == $product->id) ? 'selected' : '' }}>{{ is_array($product->name) ? ($product->name['en'] ?? reset($product->name)) : $product->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Variant</label>
                                <select name="product_variant_id" class="form-select @error('product_variant_id') is-invalid @enderror">
                                    <option value="">Select Variant</option>
                                    @foreach($variants ?? [] as $variant)
                                        <option value="{{ $variant->id }}" {{ (old('product_variant_id', $inventory->product_variant_id ?? '') == $variant->id) ? 'selected' : '' }}>{{ is_array($variant->variant_name) ? ($variant->variant_name['en'] ?? reset($variant->variant_name)) : $variant->variant_name }}</option>
                                    @endforeach
                                </select>
                                @error('product_variant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Warehouse</label>
                                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses ?? [] as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', $inventory->warehouse_id ?? '') == $warehouse->id) ? 'selected' : '' }}>{{ is_array($warehouse->warehouse_name) ? ($warehouse->warehouse_name['en'] ?? reset($warehouse->warehouse_name)) : $warehouse->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Store</label>
                                <select name="store_id" class="form-select @error('store_id') is-invalid @enderror">
                                    <option value="">Select Store</option>
                                    @foreach($stores ?? [] as $store)
                                        <option value="{{ $store->id }}" {{ (old('store_id', $inventory->store_id ?? '') == $store->id) ? 'selected' : '' }}>{{ is_array($store->store_name) ? ($store->store_name['en'] ?? reset($store->store_name)) : $store->store_name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" min="0" value="{{ old('quantity', $inventory->quantity ?? '') }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch No</label>
                                <input type="text" name="batch_no" class="form-control @error('batch_no') is-invalid @enderror" value="{{ old('batch_no', $inventory->batch_no ?? '') }}">
                                @error('batch_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ (old('status', $inventory->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ (old('status', $inventory->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                                    <option value="depleted" {{ (old('status', $inventory->status ?? '') == 'depleted') ? 'selected' : '' }}>Depleted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $inventory->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Update Inventory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
