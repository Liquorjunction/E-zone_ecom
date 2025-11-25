@extends('admin.layouts.app')

@section('title', 'Create Card')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cards.create') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Card</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cards.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Card Information</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    {{-- Language Tabs --}}
                                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default)</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="langTabContent">
                                        {{-- English Inputs --}}
                                        <div class="tab-pane fade show active" id="en" role="tabpanel">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="title[en]" class="form-control" value="{{ old('title.en') }}" placeholder="Card Title" required>
                                                @error('title.en') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Description (EN) <span class="text-danger">*</span></label>
                                                <textarea name="description[en]" class="form-control" rows="3" placeholder="Card Description" required>{{ old('description.en') }}</textarea>
                                                @error('description.en') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Link Text (EN)</label>
                                                <input type="text" name="link_text[en]" class="form-control" value="{{ old('link_text.en') }}" placeholder="e.g. Shop Now">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="link">Link URL:</label>
                                            <input type="url" name="link" class="form-control" id="link"
                                                placeholder="https://example.com" value="{{ old('link') }}">
                                            @error('link') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-2">Create Card</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Card Image</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="profile-img-edit position-relative">
                                            <img id="profile-preview"
                                                src="{{ asset('admin_assets/images/avatars/01.png') }}"
                                                alt="profile-pic"
                                                class="theme-color-default-img profile-pic rounded avatar-100">
                                            <div class="upload-icone bg-primary">
                                                <label for="image" style="cursor: pointer;">
                                                    <svg class="upload-button icon-14" width="14"
                                                        viewBox="0 0 24 24">
                                                        <path fill="#ffffff"
                                                            d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z">
                                                        </path>
                                                    </svg>
                                                </label>
                                                <input class="file-upload" type="file" id="image" name="image"
                                                    accept="image/*" style="display: none;"
                                                    onchange="previewImage(this)" required>
                                            </div>
                                        </div>
                                        <div class="img-extension mt-3">
                                            <div class="d-inline-block align-items-center">
                                                <span>Only .jpg, .png, .jpeg, .gif, .webp (max 5MB)</span>
                                            </div>
                                        </div>
                                        @error('image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Status:</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status" value="active" checked>
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="sort_order">Display Order:</label>
                                        <input type="number" name="sort_order" class="form-control" id="sort_order"
                                            placeholder="e.g., 1" min="0" value="{{ old('sort_order') }}">
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
