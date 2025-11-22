@extends('admin.layouts.app')

@section('title', 'Edit Card')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cards.edit') as $breadcrumb)
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
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Edit Card</h4>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cards.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $card->id }}">

                {{-- Language Tabs --}}
                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                    </li>
                </ul>

                <div class="tab-content" id="langTabContent">
                    {{-- English Fields --}}
                    <div class="tab-pane fade show active" id="en" role="tabpanel">
                        <div class="form-group mb-3">
                            <label>Title (EN)</label>
                            <input type="text" name="title[en]" class="form-control" value="{{ $card->getTranslation('title', 'en') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Description (EN)</label>
                            <textarea name="description[en]" class="form-control" rows="3">{{ $card->getTranslation('description', 'en') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Link Text (EN)</label>
                            <input type="text" name="link_text[en]" class="form-control" value="{{ $card->getTranslation('link_text', 'en') }}">
                        </div>
                    </div>

                    {{-- French Fields --}}
                    <div class="tab-pane fade" id="fr" role="tabpanel">
                        <div class="form-group mb-3">
                            <label>Title (FR)</label>
                            <input type="text" name="title[fr]" class="form-control" value="{{ $card->getTranslation('title', 'fr') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label>Description (FR)</label>
                            <textarea name="description[fr]" class="form-control" rows="3">{{ $card->getTranslation('description', 'fr') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Link Text (FR)</label>
                            <input type="text" name="link_text[fr]" class="form-control" value="{{ $card->getTranslation('link_text', 'fr') }}">
                        </div>
                    </div>

                    {{-- Arabic Fields --}}
                    <div class="tab-pane fade" id="ar" role="tabpanel">
                        <div class="form-group mb-3" dir="rtl">
                            <label>Title (AR)</label>
                            <input type="text" name="title[ar]" class="form-control" value="{{ $card->getTranslation('title', 'ar') }}">
                        </div>
                        <div class="form-group mb-3" dir="rtl">
                            <label>Description (AR)</label>
                            <textarea name="description[ar]" class="form-control" rows="3">{{ $card->getTranslation('description', 'ar') }}</textarea>
                        </div>
                        <div class="form-group mb-3" dir="rtl">
                            <label>Link Text (AR)</label>
                            <input type="text" name="link_text[ar]" class="form-control" value="{{ $card->getTranslation('link_text', 'ar') }}">
                        </div>
                    </div>
                </div>

                {{-- Common Fields (Image, Link, etc.) --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Link URL</label>
                            <input type="url" name="link" class="form-control" value="{{ $card->link }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $card->sort_order }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                            @if($card->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$card->image) }}" width="100" class="rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $card->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $card->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Card</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
