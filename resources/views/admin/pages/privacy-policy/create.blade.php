@extends('admin.layouts.app')
@section('title', 'Create Privacy Policy')
@section('header-title', 'Privacy Policy Management')
@section('header-description', 'Create and manage privacy policies displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.privacy-policy.create') as $breadcrumb)
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
                            <h4 class="card-title">Create Privacy Policy</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.privacy-policy.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.privacy-policy.store') }}" method="POST" id="privacyForm">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="en-tab" data-toggle="tab" href="#en"
                                        role="tab" aria-controls="en" aria-selected="true">
                                        English <span class="text-danger">*</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel"
                                    aria-labelledby="en-tab">
                                    <div class="form-group">
                                        <label>Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control" value="{{ old('title.en') }}" required>
                                        @error('title.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Description (EN) <span class="text-danger">*</span></label>
                                        <div id="editor_en" style="height: 300px;">{!! old('description.en') !!}</div>
                                        <textarea name="description[en]" id="hidden_description_en" style="display:none"></textarea>

                                        @error('description.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save Privacy Policy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/privacyPolicy.js') }}"></script>
    @endpush
@endsection
