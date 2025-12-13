@extends('admin.layouts.app')

@section('title', 'Product Details')

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
                                <ul class="nav nav-tabs" id="productShowTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" id="desc-tab" data-bs-toggle="tab"
                                            data-bs-target="#desc" type="button">Description</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs"
                                            type="button">Specifications</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="variants-tab" data-bs-toggle="tab"
                                            data-bs-target="#variants" type="button">Variants</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="gallery-tab" data-bs-toggle="tab"
                                            data-bs-target="#gallery" type="button">Gallery</button>
                                    </li>
                                </ul>

                                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productShowTabsContent">

                                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                                        <h6 class="text-muted">English</h6>
                                        <p>{!! nl2br($product->getTranslation('description', 'en')) !!}</p>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-muted">French</h6>
                                                <p class="small text-muted">{!! nl2br($product->getTranslation('description', 'fr')) !!}</p>
                                            </div>
                                            <div class="col-md-6 text-end" dir="rtl">
                                                <h6 class="text-muted">Arabic</h6>
                                                <p class="small text-muted">{!! nl2br($product->getTranslation('description', 'ar')) !!}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="specs" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Dimensions:</strong>
                                                {{ $product->getTranslation('dimensions', 'en') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Warranty:</strong> {{ $product->getTranslation('warranty', 'en') }}
                                            </div>
                                        </div>
                                        <h6 class="mt-4">Detailed Specifications</h6>
                                        <table class="table table-bordered table-sm mt-2">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Feature</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $rawSpecs = $product->getTranslation('specifications', 'en', false);
                                                    $specs = is_array($rawSpecs)
                                                        ? $rawSpecs
                                                        : json_decode($rawSpecs, true);
                                                    $specs = $specs ?: [];
                                                @endphp
                                                @forelse($specs as $spec)
                                                    <tr>
                                                        <td>{{ $spec['title'] ?? '' }}</td>
                                                        <td>{{ $spec['value'] ?? '' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">No detailed
                                                            specifications added.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="variants" role="tabpanel">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Variant Name</th>
                                                    <th>Attributes</th>
                                                    <th>Price</th>
                                                    <th>SKU</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->productVariants as $i => $variant)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>
                                                            @foreach(['en','fr','ar'] as $lang)
                                                                <div><strong>{{ strtoupper($lang) }}:</strong> {{ $variant->getTranslation('variant_name', $lang, false) }}</div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @foreach(['en','fr','ar'] as $lang)
                                                                <div><strong>{{ strtoupper($lang) }}:</strong>
                                                                    @php $attrs = $variant->getTranslation('attributes', $lang, false); @endphp
                                                                    @if(is_array($attrs))
                                                                        @foreach($attrs as $k=>$v)
                                                                            <span class="badge bg-info text-dark me-1">{{ is_string($k) ? $k : json_encode($k) }}: {{ is_string($v) ? $v : json_encode($v) }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $variant->price }}</td>
                                                        <td>{{ $variant->sku }}</td>
                                                        <td>{{ ucfirst($variant->status) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="gallery" role="tabpanel">
                                        <div class="d-flex flex-wrap gap-3">
                                            @forelse($product->images as $image)
                                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                                        class="img-thumbnail"
                                                        style="height: 120px; width: 120px; object-fit: cover;">
                                                </a>
                                            @empty
                                                <p class="text-muted">No gallery images.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
