@extends('admin.layouts.app')
@section('title', 'Create About Us')
@section('header-title', 'Create About Us')
@section('header-description', 'Add a new About Us entry for the website')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.about_us.create') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create About Us</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.about_us.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.about_us.store') }}" method="POST" enctype="multipart/form-data" id="aboutUsForm">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default) <span class="text-danger">*</span></button>
                                </li>
                            </ul>

                            <div class="tab-content" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control" value="{{ old('title.en') }}" required>
                                        @error('title.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Description (EN) <span class="text-danger">*</span></label>
                                        <div id="editor_en" style="height: 300px;">{!! old('description.en') !!}</div>
                                        <textarea name="description[en]" id="hidden_description_en" style="display:none"></textarea>
                                        @error('description.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save About Us</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Use the unified translation handler --}}
        <script src="{{ asset('admin_assets/js/aboutUs.js') }}"></script>
    @endpush
@endsection
