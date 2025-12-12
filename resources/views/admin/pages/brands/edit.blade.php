@extends('admin.layouts.app')
@section('title', 'Edit Brands')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Brands</h4>
                        </div>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.brands.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $brand->id }}">

                            <div class="row">
                                <div class="col-xl-12 col-lg-7">
                                    <div class="card">
                                        <div class="card-body">

                                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                                <li class="nav-item">
                                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab"
                                                        data-bs-target="#en" type="button" role="tab">English
                                                        (Default)</button>
                                                </li>
                                                <li class="nav-item">
                                                    <button class="nav-link" id="fr-tab" data-bs-toggle="tab"
                                                        data-bs-target="#fr" type="button" role="tab">French</button>
                                                </li>
                                                <li class="nav-item">
                                                    <button class="nav-link" id="ar-tab" data-bs-toggle="tab"
                                                        data-bs-target="#ar" type="button" role="tab">Arabic</button>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="langTabContent">
                                                <div class="tab-pane fade show active" id="en">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Name (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="name[en]" class="form-control" value="{{ $brand->getTranslation('name', 'en') }}" required>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="fr">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Name (FR)</label>
                                                        <input type="text" name="name[fr]" class="form-control" value="{{ $brand->getTranslation('name', 'fr') }}">
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="ar">
                                                    <div class="form-group mb-3" dir="rtl">
                                                        <label class="form-label">Name (AR)</label>
                                                        <input type="text" name="name[ar]" class="form-control" value="{{ $brand->getTranslation('name', 'ar') }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Image Upload --}}
                            <div class="row">
                                <div class="col-xl-12 col-lg-7">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                @if($brand->image && \Storage::disk('public')->exists($brand->image))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$brand->image) }}" width="80" class="rounded">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status:</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="active" {{ $brand->status == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Brand</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
