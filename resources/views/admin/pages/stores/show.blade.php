@extends('admin.layouts.app')
@section('title', __('Store Details'))
@section('header-title', __('Store Details'))
@section('header-description', __('View detailed information about the store'))
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.stores.show', $store) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Store Details - #{{ $store->store_id }}</h4>
                            <small class="text-muted">View detailed information about the store</small>
                        </div>
                        <a href="{{ route('admin.stores.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 text-primary"><i class="bi bi-info-circle me-1"></i>Store Information</h6>
                                    </div>
                                    <div class="card-body py-3">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <th class="w-40">Store ID</th>
                                                <td>{{ $store->store_id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ $store->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($store->status) }}
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
                                        <h6 class="mb-0 text-primary"><i class="bi bi-image me-1"></i>Store Logo</h6>
                                    </div>
                                    <div class="card-body py-3 text-center">
                                        @if($store->store_logo)
                                            <img src="{{ asset('storage/' . $store->store_logo) }}" alt="Store Logo" style="max-height: 120px;" class="img-thumbnail mt-2 shadow-sm">
                                        @else
                                            <span class="text-muted">No logo uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light py-2 d-flex align-items-center">
                                        <i class="bi bi-translate me-2 text-primary"></i>
                                        <h6 class="mb-0">Store Details (Multilingual)</h6>
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
                                                        <th class="w-40">Store Name</th>
                                                        <td>{{ $store->getTranslation('store_name', 'en') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>{{ $store->getTranslation('location', 'en') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store Footer</th>
                                                        <td>{{ $store->getTranslation('store_footer', 'en') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pane-fr" role="tabpanel" aria-labelledby="tab-fr">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <th class="w-40">Store Name</th>
                                                        <td>{{ $store->getTranslation('store_name', 'fr') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>{{ $store->getTranslation('location', 'fr') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store Footer</th>
                                                        <td>{{ $store->getTranslation('store_footer', 'fr') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="pane-ar" role="tabpanel" aria-labelledby="tab-ar">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <th class="w-40">Store Name</th>
                                                        <td dir="rtl">{{ $store->getTranslation('store_name', 'ar') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td dir="rtl">{{ $store->getTranslation('location', 'ar') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Store Footer</th>
                                                        <td dir="rtl">{{ $store->getTranslation('store_footer', 'ar') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 w-100 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Back to Stores</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
