@extends('admin.layouts.app')
@section('title', 'Create Shipping & Return Policy')
@section('header-title', 'Create Shipping & Return Policy')
@section('header-description', 'Create a new shipping and return policy to be displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.shipping-return.create') as $breadcrumb)
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create Policy</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.shipping-return.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shipping-return.store') }}" method="POST" id="shippingForm">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default)</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel">
                                    <div class="form-group mb-3">
                                        <label>Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control" value="{{ old('title.en') }}" required>
                                        @error('title.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Content (EN) <span class="text-danger">*</span></label>
                                        <div id="editor_content_en" style="height: 300px;">{!! old('content.en') !!}</div>
                                        <textarea name="content[en]" id="content_en" class="d-none"></textarea>
                                        @error('content.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- French Tab --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel">
                                    <div class="form-group mb-3">
                                        <label>Title (FR)</label>
                                        <input type="text" name="title[fr]" id="title_fr" class="form-control" value="{{ old('title.fr') }}" placeholder="Auto-translated if empty">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Content (FR)</label>
                                        <div id="editor_content_fr" style="height: 300px;">{!! old('content.fr') !!}</div>
                                        <textarea name="content[fr]" id="content_fr" class="d-none"></textarea>
                                    </div>
                                </div>

                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel">
                                    <div class="form-group mb-3" dir="rtl">
                                        <label>Title (AR)</label>
                                        <input type="text" name="title[ar]" id="title_ar" class="form-control" value="{{ old('title.ar') }}" placeholder="Auto-translated if empty">
                                    </div>
                                    <div class="form-group mb-3" dir="rtl">
                                        <label>Content (AR)</label>
                                        <div id="editor_content_ar" style="height: 300px;">{!! old('content.ar') !!}</div>
                                        <textarea name="content[ar]" id="content_ar" class="d-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-group mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" value="active" checked>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save Policy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/shippingReturnEditor.js') }}"></script>
    @endpush
@endsection
