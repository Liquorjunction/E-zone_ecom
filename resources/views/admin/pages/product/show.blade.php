@extends('admin.layouts.app')
@section('title', 'Product Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Product Details</h4>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('storage/' . $product->cover_image) }}" alt="Cover Image" class="img-fluid">
                            </div>
                            <div class="col-md-8">
                                <h3>{{ $product->name }}</h3>
                                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                                <p><strong>Price:</strong> ${{ $product->price }}</p>
                                <p><strong>Brand:</strong> {{ $product->brand->name }}</p>
                                <p><strong>Category:</strong> {{ $product->category->name }}</p>
                                <p><strong>Sub Category:</strong> {{ $product->subCategory->name }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($product->status) }}</p>
                                <p><strong>Description:</strong> {{ $product->description }}</p>
                                <p><strong>Dimensions:</strong> {{ $product->dimensions }}</p>
                                <p><strong>Specifications:</strong> {{ $product->specifications }}</p>
                                <p><strong>Warranty:</strong> {{ $product->warranty }}</p>
                            </div>
                        </div>

                        {{-- Variants --}}
                        @if($product->productVariants->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Variants</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>SKU</th>
                                                    <th>Price</th>
                                                    <th>Attributes</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->productVariants as $variant)
                                                    <tr>
                                                        <td>{{ $variant->variant_name }}</td>
                                                        <td>{{ $variant->sku }}</td>
                                                        <td>${{ $variant->price }}</td>
                                                        <td>{{ json_encode($variant->attributes) }}</td>
                                                        <td>{{ ucfirst($variant->status) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Images --}}
                        @if($product->images->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Gallery Images</h4>
                                    <div class="row">
                                        @foreach($product->images as $image)
                                            <div class="col-md-3">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image" class="img-thumbnail">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
