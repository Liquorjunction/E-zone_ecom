@extends('admin.layouts.app')
@section('title', 'Product Details')
@section('header-title', 'Product Details')
@section('header-description', 'View detailed information about the product')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.products.show', $product) as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $product->getTranslation('name', 'en') }}
                                <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'danger' }} fs-6 ms-2">{{ ucfirst($product->status) }}</span>
                                @if($product->is_bestseller)
                                    <span class="badge bg-warning text-dark fs-6 ms-2">⭐ Bestseller</span>
                                @endif
                            </h4>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="btn btn-warning btn-sm me-1">Edit</a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">Back</a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                @if ($product->cover_image)
                                    <img src="{{ asset('storage/' . $product->cover_image) }}"
                                        class="img-fluid rounded mb-3 border" alt="Cover Image">
                                @else
                                    <div class="alert alert-secondary text-center">No Cover Image</div>
                                @endif

                                <div class="card border-0 shadow-sm bg-light">
                                    <div class="card-body">
                                        <h5 class="mb-3">Pricing & Category</h5>
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>Base Price:</strong>
                                                <span>${{ number_format($product->price, 2) }}</span>
                                            </li>
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>SKU:</strong> <span>{{ $product->sku }}</span>
                                            </li>
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>Brand:</strong> <span>{{ $product->brand->name ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>Category:</strong>
                                                <span>{{ $product->category->getTranslation('category-name', 'en') ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>Sub Category:</strong>
                                                <span>{{ $product->subCategory->getTranslation('sub-category-name', 'en') ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0">
                                                <strong>Bestseller:</strong>
                                                <span>
                                                    @if($product->is_bestseller)
                                                        <span class="badge bg-warning text-dark">⭐ Yes</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <ul class="nav nav-tabs mb-3" id="langTabs" role="tablist">
                                    @foreach(['en' => 'English', 'fr' => 'French', 'ar' => 'Arabic'] as $code => $label)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $code == 'en' ? 'active' : '' }}" id="tab-{{ $code }}" data-bs-toggle="tab" data-bs-target="#lang-{{ $code }}" type="button" role="tab">{{ $label }}</button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content" id="langTabsContent">
                                    @foreach(['en' => 'English', 'fr' => 'French', 'ar' => 'Arabic'] as $code => $label)
                                        <div class="tab-pane fade {{ $code == 'en' ? 'show active' : '' }}" id="lang-{{ $code }}" role="tabpanel" @if($code == 'ar') dir="rtl" @endif>
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="mb-0">{{ $label }}</h5>
                                                <div class="text-end text-muted small">Language: {{ strtoupper($code) }}</div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="small text-muted">Description</h6>
                                                    <div class="product-desc">{!! nl2br(e($product->getTranslation('description', $code))) !!}</div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <h6 class="small text-muted">Dimensions</h6>
                                                            <div class="fw-medium">{{ $product->getTranslation('dimensions', $code) ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <h6 class="small text-muted">Warranty</h6>
                                                            <div class="fw-medium">{{ $product->getTranslation('warranty', $code) ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Specifications</h6>
                                                    @php
                                                        $rawSpecs = $product->getTranslation('specifications', $code, false);
                                                        $specs = is_array($rawSpecs) ? $rawSpecs : json_decode($rawSpecs, true);
                                                        $specs = $specs ?: [];
                                                    @endphp
                                                    @if(!empty($specs))
                                                        <div class="row">
                                                            @foreach($specs as $spec)
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="small text-muted">{{ $spec['title'] ?? '' }}</div>
                                                                    <div>{{ $spec['value'] ?? '' }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted">No specifications</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Variants</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover table-sm align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width:48px">#</th>
                                                                    <th>Name</th>
                                                                    <th style="min-width:240px">Attributes</th>
                                                                    <th style="width:100px">Price</th>
                                                                    <th style="width:160px">SKU</th>
                                                                    <th style="width:90px">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($product->productVariants as $i => $variant)
                                                                    @php
                                                                        $vName = $variant->getTranslation('variant_name', $code, false);
                                                                        $vAttrsRaw = $variant->getTranslation('attributes', $code, false);
                                                                        $vAttrs = is_array($vAttrsRaw) ? $vAttrsRaw : json_decode($vAttrsRaw, true);
                                                                        $vAttrs = $vAttrs ?: [];
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $i + 1 }}</td>
                                                                        <td class="fw-semibold">{{ is_array($vName) ? json_encode($vName, JSON_UNESCAPED_UNICODE) : $vName }}</td>
                                                                        <td>
                                                                            @if(!empty($vAttrs))
                                                                                <div class="d-flex flex-wrap gap-2">
                                                                                    @foreach($vAttrs as $attr)
                                                                                        @php $k = $attr['key'] ?? ''; $val = $attr['value'] ?? ''; @endphp
                                                                                        <div class="badge bg-light text-dark border d-inline-flex align-items-center" style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                                                            <strong class="me-1 small text-muted">{{ $k }}</strong>
                                                                                            <span class="small ms-1">{{ is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>${{ number_format($variant->price, 2) }}</td>
                                                                        <td>{{ $variant->sku }}</td>
                                                                        <td>
                                                                            <span class="badge bg-{{ $variant->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($variant->status) }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
