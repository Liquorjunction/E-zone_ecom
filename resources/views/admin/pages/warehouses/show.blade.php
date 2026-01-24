@extends('admin.layouts.app')
@section('title', __('Warehouse Details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Warehouse Details - #{{ $warehouse->warehouse_id }}</h4>
                            <small class="text-muted">View detailed information about the warehouse</small>
                        </div>
                        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 text-primary"><i class="bi bi-info-circle me-1"></i>Warehouse Information</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <th class="w-40">Warehouse ID</th>
                                                <td>{{ $warehouse->warehouse_id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ $warehouse->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($warehouse->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 text-primary"><i class="bi bi-box-seam me-1"></i>Inventories Count</h6>
                                    </div>
                                    <div class="card-body py-3 text-center">
                                        <span class="fs-4 fw-bold text-info">{{ $warehouse->inventories_count ?? ($warehouse->inventories->count() ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light py-2 d-flex align-items-center">
                                        <i class="bi bi-translate me-2 text-primary"></i>
                                        <h6 class="mb-0">Warehouse Details (Multilingual)</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-pills mb-3" id="langTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="tab-en" data-bs-toggle="pill" data-bs-target="#pane-en" type="button" role="tab" aria-controls="pane-en" aria-selected="true">English</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tab-fr" data-bs-toggle="pill" data-bs-target="#pane-fr" type="button" role="tab" aria-controls="pane-fr" aria-selected="false">French</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tab-ar" data-bs-toggle="pill" data-bs-target="#pane-ar" type="button" role="tab" aria-controls="pane-ar" aria-selected="false">Arabic</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content border rounded p-3 bg-light" id="langTabContent">
                                            <div class="tab-pane fade show active" id="pane-en" role="tabpanel" aria-labelledby="tab-en">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <th class="w-40">Warehouse Name</th>
                                                        <td>{{ $warehouse->getTranslation('warehouse_name', 'en') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>{{ $warehouse->getTranslation('location', 'en') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pane-fr" role="tabpanel" aria-labelledby="tab-fr">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <th class="w-40">Warehouse Name</th>
                                                        <td>{{ $warehouse->getTranslation('warehouse_name', 'fr') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>{{ $warehouse->getTranslation('location', 'fr') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <th class="w-40">Warehouse Name</th>
                                                        <td dir="rtl">{{ $warehouse->getTranslation('warehouse_name', 'ar') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td dir="rtl">{{ $warehouse->getTranslation('location', 'ar') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 w-100 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">Back to Warehouses</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
