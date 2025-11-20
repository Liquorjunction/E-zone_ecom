@extends('admin.layouts.app')

@section('header-title', 'Edit Hero Slider')
@section('header-description', 'Update hero slider details')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.hero-slider.edit') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div>
            <form method="POST" action="{{ route('admin.hero-slider.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $slider->id }}">
                <div class="row">
                    <div class="col-xl-4 col-lg-5">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Hero Image</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="profile-img-edit position-relative">
                                        <img id="slider-preview" src="{{ $slider->image ? asset('storage/' . $slider->image) : asset('admin_assets/images/avatars/01.png') }}" alt="slider-pic"
                                            class="theme-color-default-img profile-pic rounded avatar-100">
                                        <div class="upload-icone bg-primary">
                                            <label for="image" style="cursor: pointer;">
                                                <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                                                    <path fill="#ffffff"
                                                        d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z">
                                                    </path>
                                                </svg>
                                            </label>
                                            <input class="file-upload" type="file" id="image" name="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                        </div>
                                    </div>
                                    <div class="img-extension mt-3">
                                        <div class="d-inline-block align-items-center">
                                            <span>Only .jpg, .png, .jpeg, .gif, .webp, .bmp, .ico, .tiff, .avif allowed (max 5MB)</span>
                                        </div>
                                    </div>
                                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status:</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $slider->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="sort_order">Display Order:</label>
                                    <input type="number" name="sort_order" class="form-control" id="sort_order"
                                        placeholder="e.g., 1" min="0" value="{{ $slider->sort_order }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Slider Information</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="title">Title:</label>
                                            <input type="text" name="title" class="form-control" id="title"
                                                placeholder="Hero Title" value="{{ $slider->title }}" required>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="subtitle">Subtitle:</label>
                                            <input type="text" name="subtitle" class="form-control" id="subtitle"
                                                placeholder="Hero Subtitle" value="{{ $slider->subtitle }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="button_text">Button Label:</label>
                                            <input type="text" name="button_text" class="form-control" id="button_text"
                                                placeholder="e.g., Shop Now" value="{{ $slider->button_text }}">
                                            @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="button_link">Button Link:</label>
                                            <input type="url" name="button_link" class="form-control" id="button_link"
                                                placeholder="https://example.com" value="{{ $slider->button_link }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update Slider</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
