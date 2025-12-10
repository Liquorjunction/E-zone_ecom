@extends('admin.layouts.app')

@section('header-title', 'Create Hero Slider')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.hero-slider.create') as $breadcrumb)
        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
            <a href="{{ $breadcrumb->url ?? '#' }}">{{ $breadcrumb->title }}</a>
        </li>
    @endforeach
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Create Slider</h4>
                <a href="{{ route('admin.hero-slider.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.hero-slider.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-body">

                                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en" type="button">English (Default)</button></li>
                                    </ul>

                                    <div class="tab-content" id="langTabContent">
                                        <div class="tab-pane fade show active" id="en">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="title[en]" class="form-control" value="{{ old('title.en') }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Subtitle (EN)</label>
                                                <input type="text" name="subtitle[en]" class="form-control" value="{{ old('subtitle.en') }}">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Button Label (EN)</label>
                                                <input type="text" name="button_text[en]" class="form-control" value="{{ old('button_text.en') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 mt-3">
                                        <label class="form-label">Button Link (URL):</label>
                                        <input type="url" name="button_link" class="form-control" value="{{ old('button_link') }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3">Create Slider</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title">Hero Image</h4></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="profile-img-edit position-relative">
                                            <img id="slider-preview" src="{{ asset('admin_assets/images/avatars/01.png') }}" class="theme-color-default-img profile-pic rounded avatar-100">
                                            <div class="upload-icone bg-primary">
                                                <label for="image" style="cursor: pointer;">
                                                    <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24"><path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z"></path></svg>
                                                </label>
                                                <input class="file-upload" type="file" id="image" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)" required>
                                            </div>
                                        </div>
                                        @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Status:</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" value="active" checked>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Sort Order:</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
