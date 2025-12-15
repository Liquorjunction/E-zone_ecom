@extends('admin.layouts.app')

@section('title', 'Create Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create New Inventory</h4>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.store') }}" method="POST" id="inventory-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                    <option value="">Select Product</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}">{{ is_array($product->name) ? ($product->name['en'] ?? reset($product->name)) : $product->name }}</option>
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
                                        <option value="{{ $variant->id }}">{{ is_array($variant->variant_name) ? ($variant->variant_name['en'] ?? reset($variant->variant_name)) : $variant->variant_name }}</option>
                                    @endforeach
                                </select>
                                @error('product_variant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="loc_warehouse" value="warehouse">
                                        <label class="form-check-label" for="loc_warehouse">Warehouse</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="loc_store" value="store">
                                        <label class="form-check-label" for="loc_store">Store</label>
                                    </div>
                                </div>
                                <select name="warehouse_id" class="form-select location-select location-warehouse d-none @error('warehouse_id') is-invalid @enderror">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses ?? [] as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ is_array($warehouse->warehouse_name) ? ($warehouse->warehouse_name['en'] ?? reset($warehouse->warehouse_name)) : $warehouse->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                <select name="store_id" class="form-select location-select location-store d-none @error('store_id') is-invalid @enderror">
                                    <option value="">Select Store</option>
                                    @foreach($stores ?? [] as $store)
                                        <option value="{{ $store->id }}">{{ is_array($store->store_name) ? ($store->store_name['en'] ?? reset($store->store_name)) : $store->store_name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch No</label>
                                <input type="text" name="batch_no" class="form-control @error('batch_no') is-invalid @enderror">
                                @error('batch_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="depleted">Depleted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"></textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Create Inventory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var productSelect = document.querySelector('select[name="product_id"]');
    var variantSelect = document.querySelector('select[name="product_variant_id"]');

    function clearVariants() {
        variantSelect.innerHTML = '<option value="">Select Variant</option>';
    }

    function renderVariantOption(v) {
        var opt = document.createElement('option');
        opt.value = v.id;
        var name = v.variant_name;
        try {
            if (typeof name === 'string') {
                var parsed = JSON.parse(name);
                if (parsed && typeof parsed === 'object') {
                    name = parsed.en || Object.values(parsed)[0] || '';
                }
            } else if (typeof name === 'object') {
                name = name.en || Object.values(name)[0] || '';
            }
        } catch (e) {
        }
        opt.textContent = name || 'Variant ' + v.id;
        return opt;
    }

    function loadVariants(productId) {
        clearVariants();
        if (!productId) return;
        var url = '{{ route('admin.inventory.variants') }}' + '?product_id=' + encodeURIComponent(productId);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!Array.isArray(data)) return;
                data.forEach(function (v) {
                    variantSelect.appendChild(renderVariantOption(v));
                });
            }).catch(function (err) {
                console.error('Failed to load variants', err);
            });
    }

    if (productSelect) {
        productSelect.addEventListener('change', function (e) {
            loadVariants(e.target.value);
        });
    }
    // Location selector logic
    var locWarehouseRadio = document.getElementById('loc_warehouse');
    var locStoreRadio = document.getElementById('loc_store');
    var locWarehouseSelect = document.querySelector('.location-warehouse');
    var locStoreSelect = document.querySelector('.location-store');

    function setLocationVisibility() {
        if (locWarehouseRadio && locWarehouseRadio.checked) {
            locWarehouseSelect.classList.remove('d-none');
            locWarehouseSelect.required = true;
            locWarehouseSelect.style.display = '';
            locStoreSelect.classList.add('d-none');
            locStoreSelect.required = false;
            locStoreSelect.style.display = 'none';
            locStoreSelect.value = '';
        } else if (locStoreRadio && locStoreRadio.checked) {
            locStoreSelect.classList.remove('d-none');
            locStoreSelect.required = true;
            locStoreSelect.style.display = '';
            locWarehouseSelect.classList.add('d-none');
            locWarehouseSelect.required = false;
            locWarehouseSelect.style.display = 'none';
            locWarehouseSelect.value = '';
        } else {
            locWarehouseSelect.classList.add('d-none');
            locWarehouseSelect.required = false;
            locWarehouseSelect.style.display = 'none';
            locStoreSelect.classList.add('d-none');
            locStoreSelect.required = false;
            locStoreSelect.style.display = 'none';
        }
    }

    if (locWarehouseRadio) locWarehouseRadio.addEventListener('change', setLocationVisibility);
    if (locStoreRadio) locStoreRadio.addEventListener('change', setLocationVisibility);
    // initialize
    setLocationVisibility();
});
</script>
@endpush
