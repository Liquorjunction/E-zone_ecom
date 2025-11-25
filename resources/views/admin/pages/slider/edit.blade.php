@extends('admin.layouts.app')

@section('header-title', 'Edit Hero Slider')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div>
            <form method="POST" action="{{ route('admin.hero-slider.update', ['id' => $slider->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $slider->id }}">

                <div class="row">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Slider Information</h4></div>
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en" type="button">English</button></li>
                                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fr" type="button">French</button></li>
                                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ar" type="button">Arabic</button></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="en">
                                        <div class="form-group mb-3">
                                            <label>Title (EN)</label>
                                            <input type="text" name="title[en]" class="form-control" value="{{ $slider->getTranslation('title', 'en') }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Subtitle (EN)</label>
                                            <input type="text" name="subtitle[en]" class="form-control" value="{{ $slider->getTranslation('subtitle', 'en') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Button (EN)</label>
                                            <input type="text" name="button_text[en]" class="form-control" value="{{ $slider->getTranslation('button_text', 'en') }}">
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="fr">
                                        <div class="form-group mb-3">
                                            <label>Title (FR)</label>
                                            <input type="text" name="title[fr]" class="form-control" value="{{ $slider->getTranslation('title', 'fr') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Subtitle (FR)</label>
                                            <input type="text" name="subtitle[fr]" class="form-control" value="{{ $slider->getTranslation('subtitle', 'fr') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Button (FR)</label>
                                            <input type="text" name="button_text[fr]" class="form-control" value="{{ $slider->getTranslation('button_text', 'fr') }}">
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="ar">
                                        <div class="form-group mb-3" dir="rtl">
                                            <label>Title (AR)</label>
                                            <input type="text" name="title[ar]" class="form-control" value="{{ $slider->getTranslation('title', 'ar') }}">
                                        </div>
                                        <div class="form-group mb-3" dir="rtl">
                                            <label>Subtitle (AR)</label>
                                            <input type="text" name="subtitle[ar]" class="form-control" value="{{ $slider->getTranslation('subtitle', 'ar') }}">
                                        </div>
                                        <div class="form-group mb-3" dir="rtl">
                                            <label>Button (AR)</label>
                                            <input type="text" name="button_text[ar]" class="form-control" value="{{ $slider->getTranslation('button_text', 'ar') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label>Button Link</label>
                                    <input type="url" name="button_link" class="form-control" value="{{ $slider->button_link }}">
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Update Slider</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Image & Status</h4></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <img id="slider-preview" src="{{ $slider->image ? asset('storage/'.$slider->image) : asset('admin_assets/images/avatars/01.png') }}" class="img-fluid rounded mb-3">
                                    <input type="file" name="image" class="form-control" onchange="previewImage(this)">
                                </div>
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="active" {{ $slider->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $slider->sort_order }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts') <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script> @endpush
