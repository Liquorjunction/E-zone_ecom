@extends('admin.layouts.app')
@section('title', 'Create Terms and Conditions')
@section('header-title', 'Terms and Conditions Management')
@section('header-description', 'Create and manage terms and conditions displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.tncs.create') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
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
                            <h4 class="card-title">Create Terms and Conditions</h4>
                        </div>
                        <a href="{{ route('admin.tncs.index') }}" class="btn btn-sm btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.tncs.store') }}" id="tncForm">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item"><button class="nav-link active" id="en-tab" data-bs-toggle="tab"
                                        data-bs-target="#en" type="button">English</button></li>
                            </ul>

                            {{-- Language Tabs Content --}}
                            <div class="tab-content mt-3">
                                {{-- English --}}
                                <div class="tab-pane fade show active" id="en">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Title (EN) <span class="text-danger">*</span></label>
                                        </div>
                                        <input type="text" name="title[en]" id="title_en" class="form-control"
                                            value="{{ old('title.en') }}">
                                        @error('title.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Content (EN) <span class="text-danger">*</span></label>
                                        </div>
                                        <div id="editor_content_en" style="height: 300px;"></div>
                                        <textarea name="content[en]" id="content_en" style="display:none">{{ old('content.en') }}</textarea>
                                        @error('content.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- French --}}
                                <div class="tab-pane fade" id="fr">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Title (FR)</label>
                                        </div>
                                        <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                            value="{{ old('title.fr') }}">
                                        @error('title.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Content (FR)</label>
                                        </div>
                                        <div id="editor_content_fr" style="height: 300px;"></div>
                                        <textarea name="content[fr]" id="content_fr" style="display:none">{{ old('content.fr') }}</textarea>
                                        @error('content.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Arabic --}}
                                <div class="tab-pane fade" id="ar">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Title (AR)</label>
                                        </div>
                                        <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                            value="{{ old('title.ar') }}" dir="rtl">
                                        @error('title.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Content (AR)</label>
                                        </div>
                                        <div id="editor_content_ar" style="height: 300px;"></div>
                                        <textarea name="content[ar]" id="content_ar" style="display:none" dir="rtl">{{ old('content.ar') }}</textarea>
                                        @error('content.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Status Field --}}
                            <div class="form-group mt-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            {{-- Save Button --}}
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save Terms and Conditions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/tncEditor.js') }}"></script>
@endpush
