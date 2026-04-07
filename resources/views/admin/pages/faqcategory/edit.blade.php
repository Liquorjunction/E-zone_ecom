@extends('admin.layouts.app')
@section('title', 'Edit Faq Category')
@section('header-title', 'Edit Faq Category')
@section('header-description', 'Edit an existing faq category to organize your frequently asked questions')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.faq-categories.edit', $faqCategory) as $breadcrumb)
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
                            <h4 class="card-title">Edit Faq Category</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.faq-categories.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $faqCategory->id }}">
                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en"
                                        type="button" role="tab" aria-controls="en" aria-selected="true">
                                        English <span class="text-danger">*</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr"
                                        type="button" role="tab" aria-controls="fr" aria-selected="false">
                                        French
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar"
                                        type="button" role="tab" aria-controls="ar" aria-selected="false">
                                        Arabic
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="langContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active p-3" id="en" role="tabpanel"
                                    aria-labelledby="en-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-primary btn-translate-all"
                                            data-url="{{ route('admin.translate.batch') }}"
                                            data-fields='["name"]'>
                                            <i class="bi bi-translate me-2"></i> Translate All Fields
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <label for="name_en">Name (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="name[en]" class="form-control" id="name_en"
                                            value="{{ old('name.en', $faqCategory->getTranslation('name', 'en')) }}"
                                            placeholder="Enter Name in English">

                                        @error('name.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- French Tab --}}
                                <div class="tab-pane fade p-3" id="fr" role="tabpanel" aria-labelledby="fr-tab">
                                    <div class="form-group">
                                        <label for="name_fr">Name (FR) <span class="text-danger">*</span></label>
                                        <input type="text" name="name[fr]" class="form-control" id="name_fr"
                                            value="{{ old('name.fr', $faqCategory->getTranslation('name', 'fr')) }}"
                                            placeholder="Enter Name in French">

                                        @error('name.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade p-3" id="ar" role="tabpanel" aria-labelledby="ar-tab">
                                    <div class="form-group">
                                        <label for="name_ar">Name (AR) <span class="text-danger">*</span></label>
                                        <input type="text" name="name[ar]" class="form-control" id="name_ar"
                                            value="{{ old('name.ar', $faqCategory->getTranslation('name', 'ar')) }}"
                                            placeholder="Enter Name in Arabic">

                                        @error('name.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Status Field --}}
                            <div class="form-group mt-4">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" id="status">
                                    <option value="active" {{ $faqCategory->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $faqCategory->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- Submit Button --}}
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
