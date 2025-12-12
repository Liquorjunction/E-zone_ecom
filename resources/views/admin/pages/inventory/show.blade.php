@extends('admin.layouts.app')

@section('title', 'Inventory Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Inventory Details</h4>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Product</dt>
                        <dd class="col-sm-9">{{ $inventory->product->name ?? '-' }}</dd>

                        <dt class="col-sm-3">Product Variant</dt>
                        <dd class="col-sm-9">{{ $inventory->productVariant->variant_name ?? '-' }}</dd>

                        <dt class="col-sm-3">Warehouse</dt>
                        <dd class="col-sm-9">{{ $inventory->warehouse->warehouse_name ?? '-' }}</dd>

                        <dt class="col-sm-3">Store</dt>
                        <dd class="col-sm-9">{{ $inventory->store->store_name ?? '-' }}</dd>

                        <dt class="col-sm-3">Quantity</dt>
                        <dd class="col-sm-9">{{ $inventory->quantity }}</dd>

                        <dt class="col-sm-3">Batch No</dt>
                        <dd class="col-sm-9">{{ $inventory->batch_no ?? '-' }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">{{ ucfirst($inventory->status) }}</dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $inventory->notes ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
