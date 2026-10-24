@extends('admin.layouts.app')
@section('title', 'Create Brand')
@section('header-title', 'Create Brand')
@section('header-description', 'Add a new brand with optional hero slider banners and category showcase cards')
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
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-1">Create Brand</h4>
                <p class="text-muted small mb-0">Configure basic details, promotional hero sliders, and category showcase sections</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Brands
                </a>
                <button type="submit" form="brand-wizard-form" class="btn btn-sm btn-primary">
                    <i class="bi bi-save me-1"></i> Create Brand
                </button>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" id="brand-wizard-form">
                @csrf

                {{-- Wizard Navigation Pills --}}
                <ul class="nav nav-pills mb-4 nav-fill bg-light p-1.5 rounded-3 border" id="pills-tab-brand" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold py-2.5" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1" type="button" role="tab">
                            <i class="bi bi-info-circle me-1"></i> Step 1: Brand Info & Story
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2" type="button" role="tab">
                            <i class="bi bi-images me-1"></i> Step 2: Hero Slider Banners
                            <span class="badge bg-secondary ms-1" id="banner-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3" type="button" role="tab">
                            <i class="bi bi-grid me-1"></i> Step 3: Category Showcases
                            <span class="badge bg-secondary ms-1" id="showcase-count-badge">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent-brand">

                    {{-- ======================================================== --}}
                    {{-- STEP 1: BRAND INFO & STORY                               --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        <div class="row g-3">
                            {{-- Left Column: Translatable Text Inputs --}}
                            <div class="col-lg-8 col-md-12">
                                <div class="card border p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <ul class="nav nav-tabs" id="langTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                                            </li>
                                        </ul>

                                        <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                            data-url="{{ route('admin.translate.batch') }}"
                                            data-fields='["name", "tagline", "description"]'>
                                            <i class="bi bi-translate me-1"></i> Auto Translate Fields
                                        </button>
                                    </div>

                                    <div class="tab-content" id="langTabContent">
                                        {{-- English Tab --}}
                                        <div class="tab-pane fade show active" id="en" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="name_en" class="form-label fw-semibold">Brand Name (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="name[en]" id="name_en" class="form-control"
                                                       value="{{ old('name.en') }}"
                                                       placeholder="e.g. Sharp" required>
                                                @error('name.en')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="tagline_en" class="form-label fw-semibold">Tagline / Subtitle (EN)</label>
                                                <input type="text" name="tagline[en]" id="tagline_en" class="form-control"
                                                       value="{{ old('tagline.en') }}"
                                                       placeholder="e.g. In step with your future">
                                                <div class="form-text text-muted">Displays as the prominent tagline under or beside the brand logo on the brand page.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="description_en" class="form-label fw-semibold">Brand Story / Description (EN)</label>
                                                <textarea name="description[en]" id="description_en" rows="4" class="form-control"
                                                          placeholder="Since 1915, an attitude of giving sincere thought to the people who use product...">{{ old('description.en') }}</textarea>
                                                <div class="form-text text-muted">Paragraph displayed in the intro card introducing the brand history and mission.</div>
                                            </div>
                                        </div>

                                        {{-- French Tab --}}
                                        <div class="tab-pane fade" id="fr" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="name_fr" class="form-label fw-semibold">Brand Name (FR)</label>
                                                <input type="text" name="name[fr]" id="name_fr" class="form-control"
                                                       value="{{ old('name.fr') }}"
                                                       placeholder="e.g. Sharp">
                                            </div>

                                            <div class="mb-3">
                                                <label for="tagline_fr" class="form-label fw-semibold">Tagline / Subtitle (FR)</label>
                                                <input type="text" name="tagline[fr]" id="tagline_fr" class="form-control"
                                                       value="{{ old('tagline.fr') }}"
                                                       placeholder="e.g. En phase avec votre avenir">
                                            </div>

                                            <div class="mb-3">
                                                <label for="description_fr" class="form-label fw-semibold">Brand Story / Description (FR)</label>
                                                <textarea name="description[fr]" id="description_fr" rows="4" class="form-control"
                                                          placeholder="Description en français...">{{ old('description.fr') }}</textarea>
                                            </div>
                                        </div>

                                        {{-- Arabic Tab --}}
                                        <div class="tab-pane fade" id="ar" role="tabpanel">
                                            <div class="mb-3" dir="rtl">
                                                <label for="name_ar" class="form-label fw-semibold">Brand Name (AR)</label>
                                                <input type="text" name="name[ar]" id="name_ar" class="form-control"
                                                       value="{{ old('name.ar') }}"
                                                       placeholder="e.g. شارب">
                                            </div>

                                            <div class="mb-3" dir="rtl">
                                                <label for="tagline_ar" class="form-label fw-semibold">Tagline / Subtitle (AR)</label>
                                                <input type="text" name="tagline[ar]" id="tagline_ar" class="form-control"
                                                       value="{{ old('tagline.ar') }}"
                                                       placeholder="e.g. في خطوة مع مستقبلك">
                                            </div>

                                            <div class="mb-3" dir="rtl">
                                                <label for="description_ar" class="form-label fw-semibold">Brand Story / Description (AR)</label>
                                                <textarea name="description[ar]" id="description_ar" rows="4" class="form-control"
                                                          placeholder="وصف بالعربية...">{{ old('description.ar') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Brand Logo & Status --}}
                            <div class="col-lg-4 col-md-12">
                                <div class="card border p-3 shadow-xs">
                                    <div class="text-center mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-muted">
                                            Brand Logo <span class="text-danger">*</span> <br>
                                            <span class="text-info" style="font-size: 0.8em; text-transform: none;">(Recommended: Square 1:1, transparent PNG)</span>
                                        </label>
                                        <div class="bg-light p-3 rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 140px;">
                                            <img id="iconPreview"
                                                 src="{{ asset('images/no_image.jpg') }}"
                                                 alt="Brand Logo"
                                                 class="img-fluid rounded"
                                                 style="max-width: 140px; max-height: 140px; object-fit: contain; opacity: 0.7;">
                                        </div>
                                        <input id="brand-icon" type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                                        <div class="form-text mt-1 text-xs">Accepted: jpeg, png, webp, avif. Max 2MB.</div>
                                        <div class="mt-2 text-danger small" id="iconFeedback" style="display:none;"></div>
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label class="form-label d-block fw-bold">Brand Status</label>
                                        <input type="hidden" name="status" value="inactive">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active" checked>
                                            <label class="form-check-label" for="statusSwitch">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 1 Navigation --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next-step" data-next="step2">
                                Next: Hero Banners <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- STEP 2: HERO SLIDER BANNERS                              --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Hero Slider Information:</strong> These slides render in the full-width promotional slider at the top of the brand page.
                                Recommended aspect ratio is <strong>1600 × 562 px</strong> (or 16:9 / 16:6).
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Promotional Banner Slides</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-banner-btn">
                                <i class="bi bi-plus-circle me-1"></i> Add Banner Slide
                            </button>
                        </div>

                        <div id="banners-container" class="space-y-3">
                            <div class="text-center py-4 text-muted bg-light rounded border border-dashed" id="no-banners-msg">
                                <i class="bi bi-images fs-1 text-secondary mb-2 d-block"></i>
                                <p class="mb-1">No hero banners created yet.</p>
                                <small>Click <strong>"Add Banner Slide"</strong> above to add promotional slides.</small>
                            </div>
                        </div>

                        {{-- Step 2 Navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step1">
                                <i class="bi bi-arrow-left me-1"></i> Previous: Brand Info
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="step3">
                                Next: Category Showcases <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- STEP 3: CATEGORY SHOWCASES                               --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Category Showcase Cards:</strong> These render in an alternating left/right layout on the brand page.
                                Add custom Button Text and Button URL (such as an anchor <code>#products-catalog</code> or a category page) to direct visitors anywhere you want.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Showcase Cards</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-showcase-btn">
                                <i class="bi bi-plus-circle me-1"></i> Add Showcase Card
                            </button>
                        </div>

                        <div id="showcases-container">
                            <div class="text-center py-4 text-muted bg-light rounded border border-dashed" id="no-showcases-msg">
                                <i class="bi bi-grid fs-1 text-secondary mb-2 d-block"></i>
                                <p class="mb-1">No category showcases created yet.</p>
                                <small>Click <strong>"Add Showcase Card"</strong> above to showcase product categories with lifestyle photography.</small>
                            </div>
                        </div>

                        {{-- Step 3 Navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step2">
                                <i class="bi bi-arrow-left me-1"></i> Previous: Hero Banners
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Create Brand
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- Templates for Dynamic Rows --}}
<template id="banner-row-template">
    <div class="card border mb-3 banner-item shadow-2xs" data-index="__INDEX__">
        <input type="hidden" name="banners[__INDEX__][id]" value="">
        <input type="hidden" name="banners[__INDEX__][_delete]" value="0" class="banner-delete-flag">

        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 col-sm-4 text-center">
                    <div class="bg-dark rounded overflow-hidden p-1 d-flex align-items-center justify-content-center" style="height: 90px;">
                        <img src="{{ asset('images/no_image.jpg') }}"
                             alt="Banner Preview"
                             class="img-fluid rounded banner-preview-img"
                             style="max-height: 85px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="mt-1 small text-success fw-semibold">New Slide</div>
                </div>

                <div class="col-md-5 col-sm-8">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" name="banners[__INDEX__][image]" class="form-control form-control-sm banner-file-input" accept="image/*" required>
                        <div class="form-text text-2xs text-muted">Recommended 1600 × 562 px.</div>
                    </div>
                    <div>
                        <label class="form-label small fw-bold mb-1">Alt Text / Title</label>
                        <input type="text" name="banners[__INDEX__][alt_text][en]" class="form-control form-control-sm"
                               placeholder="e.g. Summer Appliances Promotion">
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Sort Order</label>
                        <input type="number" name="banners[__INDEX__][sort_order]" class="form-control form-control-sm"
                               value="__SORT_ORDER__" min="0">
                    </div>
                    <div>
                        <label class="form-label small fw-bold mb-1">Status</label>
                        <select name="banners[__INDEX__][status]" class="form-select form-select-sm">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-1 col-sm-6 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-banner-btn" title="Remove Banner">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="showcase-card-template">
    <div class="card border mb-4 showcase-item shadow-2xs" data-index="__INDEX__">
        <input type="hidden" name="showcases[__INDEX__][id]" value="">
        <input type="hidden" name="showcases[__INDEX__][_delete]" value="0" class="showcase-delete-flag">

        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-pill">New Card</span>
                <span class="fw-bold showcase-header-title text-dark">New Category Showcase</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm remove-showcase-btn py-0 px-2" title="Remove Card">
                <i class="bi bi-trash me-1"></i> Remove
            </button>
        </div>

        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Lifestyle Photo <span class="text-danger">*</span></label>
                    <div class="bg-light rounded border p-1 text-center mb-2" style="height: 120px;">
                        <img src="{{ asset('images/no_image.jpg') }}"
                             alt="Showcase Preview"
                             class="img-fluid rounded showcase-preview-img"
                             style="max-height: 110px; width: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="showcases[__INDEX__][image]" class="form-control form-control-sm showcase-file-input" accept="image/*" required>
                    <div class="form-text text-2xs text-muted">Recommended: 800×600 or 1:1 square.</div>
                </div>

                <div class="col-md-9">
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold mb-1">Button URL (Target Link)</label>
                            <input type="text" name="showcases[__INDEX__][button_url]" class="form-control form-control-sm showcase-btn-url"
                                   placeholder="e.g. #products-catalog or /category/refrigerators">
                            <div class="form-text text-2xs text-muted">Page link or anchor ID for the card's CTA button.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="showcases[__INDEX__][sort_order]" class="form-control form-control-sm"
                                   value="__SORT_ORDER__" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Status</label>
                            <select name="showcases[__INDEX__][status]" class="form-select form-select-sm">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="border rounded p-2 bg-light/50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <ul class="nav nav-tabs nav-tabs-sm" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-en-__INDEX__" type="button">English</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-fr-__INDEX__" type="button">French</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-ar-__INDEX__" type="button">Arabic</button>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-translate-showcase" data-index="__INDEX__">
                                <i class="bi bi-translate me-1"></i> Auto Translate Card
                            </button>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="sc-en-__INDEX__">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Card Name / Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="showcases[__INDEX__][title][en]" class="form-control form-control-sm showcase-title-en"
                                               placeholder="e.g. Laundry" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (EN)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][en]" class="form-control form-control-sm showcase-btn-text-en"
                                               placeholder="e.g. View Laundry">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold mb-1">Description (EN)</label>
                                        <textarea name="showcases[__INDEX__][description][en]" rows="2" class="form-control form-control-sm showcase-desc-en"
                                                  placeholder="With Sharp Washing Machines, enjoy clothes that are ultra-clean..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="sc-fr-__INDEX__">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Title (FR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][fr]" class="form-control form-control-sm showcase-title-fr"
                                               placeholder="e.g. Buanderie">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (FR)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][fr]" class="form-control form-control-sm showcase-btn-text-fr"
                                               placeholder="e.g. Voir la buanderie">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold mb-1">Description (FR)</label>
                                        <textarea name="showcases[__INDEX__][description][fr]" rows="2" class="form-control form-control-sm showcase-desc-fr"
                                                  placeholder="Description en français..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="sc-ar-__INDEX__">
                                <div class="row g-2" dir="rtl">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Title (AR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][ar]" class="form-control form-control-sm showcase-title-ar"
                                               placeholder="e.g. غسيل الملابس">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (AR)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][ar]" class="form-control form-control-sm showcase-btn-text-ar"
                                               placeholder="e.g. عرض منتجات الغسيل">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold mb-1">Description (AR)</label>
                                        <textarea name="showcases[__INDEX__][description][ar]" rows="2" class="form-control form-control-sm showcase-desc-ar"
                                                  placeholder="وصف بالعربية..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
    <style>
        .select2-container--disabled {
            pointer-events: none !important;
            opacity: 0.65 !important;
            cursor: not-allowed !important;
        }
        .select2-container--disabled .select2-selection {
            background-color: #f8f9fa !important;
            cursor: not-allowed !important;
            border-color: #dee2e6 !important;
        }
    </style>
    <script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof setupImagePreview === 'function') {
                setupImagePreview('brand-icon', 'iconPreview', 'iconFeedback');
            }

            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const nextId = this.getAttribute('data-next');
                    const tabTrigger = document.querySelector(`button[data-bs-target="#${nextId}"]`);
                    if (tabTrigger) {
                        bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const prevId = this.getAttribute('data-prev');
                    const tabTrigger = document.querySelector(`button[data-bs-target="#${prevId}"]`);
                    if (tabTrigger) {
                        bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });

            let bannerCounter = 0;
            const bannersContainer = document.getElementById('banners-container');
            const addBannerBtn = document.getElementById('add-banner-btn');
            const noBannersMsg = document.getElementById('no-banners-msg');
            const bannerBadge = document.getElementById('banner-count-badge');

            function updateBannerBadge() {
                const activeCount = bannersContainer.querySelectorAll('.banner-item:not([style*="display: none"])').length;
                if (bannerBadge) bannerBadge.textContent = activeCount;
            }

            if (addBannerBtn) {
                addBannerBtn.addEventListener('click', function() {
                    if (noBannersMsg) noBannersMsg.style.display = 'none';
                    const template = document.getElementById('banner-row-template').innerHTML;
                    const newHtml = template
                        .replace(/__INDEX__/g, bannerCounter)
                        .replace(/__SORT_ORDER__/g, bannerCounter);
                    bannersContainer.insertAdjacentHTML('beforeend', newHtml);
                    bannerCounter++;
                    updateBannerBadge();
                });
            }

            bannersContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('banner-file-input')) {
                    const file = e.target.files[0];
                    if (file) {
                        const card = e.target.closest('.banner-item');
                        const img = card.querySelector('.banner-preview-img');
                        if (img) img.src = URL.createObjectURL(file);
                    }
                }
            });

            bannersContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-banner-btn');
                if (removeBtn) {
                    e.preventDefault();
                    removeBtn.closest('.banner-item').remove();
                    updateBannerBadge();
                }
            });

            let showcaseCounter = 0;
            const showcasesContainer = document.getElementById('showcases-container');
            const addShowcaseBtn = document.getElementById('add-showcase-btn');
            const noShowcasesMsg = document.getElementById('no-showcases-msg');
            const showcaseBadge = document.getElementById('showcase-count-badge');

            function updateShowcaseBadge() {
                const activeCount = showcasesContainer.querySelectorAll('.showcase-item:not([style*="display: none"])').length;
                if (showcaseBadge) showcaseBadge.textContent = activeCount;
            }

            if (addShowcaseBtn) {
                addShowcaseBtn.addEventListener('click', function() {
                    if (noShowcasesMsg) noShowcasesMsg.style.display = 'none';
                    const template = document.getElementById('showcase-card-template').innerHTML;
                    const newHtml = template
                        .replace(/__INDEX__/g, showcaseCounter)
                        .replace(/__SORT_ORDER__/g, showcaseCounter);
                    showcasesContainer.insertAdjacentHTML('beforeend', newHtml);
                    showcaseCounter++;
                    updateShowcaseBadge();
                });
            }

            showcasesContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('showcase-file-input')) {
                    const file = e.target.files[0];
                    if (file) {
                        const card = e.target.closest('.showcase-item');
                        const img = card.querySelector('.showcase-preview-img');
                        if (img) img.src = URL.createObjectURL(file);
                    }
                }
            });

            showcasesContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('showcase-title-en')) {
                    const card = e.target.closest('.showcase-item');
                    const headerTitle = card.querySelector('.showcase-header-title');
                    if (headerTitle) headerTitle.textContent = e.target.value.trim() || 'New Category Showcase';
                }
            });

            showcasesContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-showcase-btn');
                if (removeBtn) {
                    e.preventDefault();
                    removeBtn.closest('.showcase-item').remove();
                    updateShowcaseBadge();
                    return;
                }

                const transBtn = e.target.closest('.btn-translate-showcase');
                if (transBtn) {
                    e.preventDefault();
                    const card = transBtn.closest('.showcase-item');
                    const titleEn = card.querySelector('.showcase-title-en');
                    const descEn = card.querySelector('.showcase-desc-en');
                    const btnTextEn = card.querySelector('.showcase-btn-text-en');

                    const titleVal = titleEn ? titleEn.value.trim() : '';
                    const descVal = descEn ? descEn.value.trim() : '';
                    const btnTextVal = btnTextEn ? btnTextEn.value.trim() : '';

                    if (!titleVal && !descVal && !btnTextVal) {
                        alert('Please fill in the English Title, Description, or Button Text before translating.');
                        return;
                    }

                    const originalBtnHtml = transBtn.innerHTML;
                    transBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Translating...';
                    transBtn.disabled = true;

                    const payload = {};
                    if (titleVal) payload.title = titleVal;
                    if (descVal) payload.description = descVal;
                    if (btnTextVal) payload.button_text = btnTextVal;

                    axios.post('{{ route("admin.translate.batch") }}', { fields: payload })
                        .then(function(res) {
                            if (res.data && res.data.success && res.data.translations) {
                                const trans = res.data.translations;
                                if (trans.title) {
                                    const titleFr = card.querySelector('.showcase-title-fr');
                                    const titleAr = card.querySelector('.showcase-title-ar');
                                    if (titleFr && trans.title.fr) titleFr.value = trans.title.fr;
                                    if (titleAr && trans.title.ar) titleAr.value = trans.title.ar;
                                }
                                if (trans.description) {
                                    const descFr = card.querySelector('.showcase-desc-fr');
                                    const descAr = card.querySelector('.showcase-desc-ar');
                                    if (descFr && trans.description.fr) descFr.value = trans.description.fr;
                                    if (descAr && trans.description.ar) descAr.value = trans.description.ar;
                                }
                                if (trans.button_text) {
                                    const btnTextFr = card.querySelector('.showcase-btn-text-fr');
                                    const btnTextAr = card.querySelector('.showcase-btn-text-ar');
                                    if (btnTextFr && trans.button_text.fr) btnTextFr.value = trans.button_text.fr;
                                    if (btnTextAr && trans.button_text.ar) btnTextAr.value = trans.button_text.ar;
                                }
                                if (typeof showSuccessToast === 'function') {
                                    showSuccessToast();
                                }
                            }
                        })
                        .catch(function(err) {
                            console.error('Translation error:', err);
                            alert('Translation failed.');
                        })
                        .finally(function() {
                            transBtn.innerHTML = originalBtnHtml;
                            transBtn.disabled = false;
                        });
                }
            });

            // 5. Client-Side Validation before Form Submission
            const brandForm = document.getElementById('brand-wizard-form');
            if (brandForm) {
                brandForm.addEventListener('submit', function(e) {
                    const showcaseItems = document.querySelectorAll('.showcase-item');
                    let hasError = false;
                    let firstErrorElement = null;

                    showcaseItems.forEach((card) => {
                        const deleteFlag = card.querySelector('.showcase-delete-flag');
                        if ((deleteFlag && deleteFlag.value === '1') || card.style.display === 'none') {
                            return;
                        }

                        // Validate Card Name / Title (EN)
                        const titleInput = card.querySelector('.showcase-title-en');
                        if (titleInput) {
                            if (!titleInput.value.trim()) {
                                hasError = true;
                                titleInput.classList.add('is-invalid');
                                let feedback = titleInput.parentNode.querySelector('.invalid-feedback');
                                if (!feedback) {
                                    feedback = document.createElement('div');
                                    feedback.className = 'invalid-feedback d-block';
                                    titleInput.parentNode.appendChild(feedback);
                                }
                                feedback.textContent = 'Card Name is required.';

                                if (!firstErrorElement) {
                                    firstErrorElement = { element: titleInput, card: card, tab: 'en' };
                                }
                            } else {
                                titleInput.classList.remove('is-invalid');
                                const feedback = titleInput.parentNode.querySelector('.invalid-feedback');
                                if (feedback) feedback.remove();
                            }
                        }

                        // Validate Lifestyle Photo
                        const fileInput = card.querySelector('.showcase-file-input');
                        const previewImg = card.querySelector('.showcase-preview-img');
                        const hasExistingImage = previewImg && previewImg.src && !previewImg.src.includes('no_image.jpg');
                        const hasSelectedFile = fileInput && fileInput.files && fileInput.files.length > 0;

                        if (fileInput) {
                            if (!hasSelectedFile && !hasExistingImage) {
                                hasError = true;
                                fileInput.classList.add('is-invalid');
                                let feedback = fileInput.parentNode.querySelector('.invalid-feedback');
                                if (!feedback) {
                                    feedback = document.createElement('div');
                                    feedback.className = 'invalid-feedback d-block';
                                    fileInput.parentNode.appendChild(feedback);
                                }
                                feedback.textContent = 'Lifestyle photo is required.';

                                if (!firstErrorElement) {
                                    firstErrorElement = { element: fileInput, card: card, tab: null };
                                }
                            } else {
                                fileInput.classList.remove('is-invalid');
                                const feedback = fileInput.parentNode.querySelector('.invalid-feedback');
                                if (feedback) feedback.remove();
                            }
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Switch to Step 3 tab
                        const step3TabBtn = document.querySelector('button[data-bs-target="#step3"]');
                        if (step3TabBtn) {
                            bootstrap.Tab.getOrCreateInstance(step3TabBtn).show();
                        }

                        if (firstErrorElement) {
                            if (firstErrorElement.tab === 'en') {
                                const cardIndex = firstErrorElement.card.getAttribute('data-index');
                                const enTabBtn = firstErrorElement.card.querySelector(`button[data-bs-target="#sc-en-${cardIndex}"]`);
                                if (enTabBtn) {
                                    bootstrap.Tab.getOrCreateInstance(enTabBtn).show();
                                }
                            }
                            firstErrorElement.element.focus();
                            firstErrorElement.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }
                });
            }

            // Clear errors on real-time input/change
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('showcase-title-en') && e.target.value.trim()) {
                    e.target.classList.remove('is-invalid');
                    const feedback = e.target.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('showcase-file-input') && e.target.files.length > 0) {
                    e.target.classList.remove('is-invalid');
                    const feedback = e.target.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();
                }
            });

            // Auto-switch to tab containing server-side validation error on load
            const firstServerInvalid = document.querySelector('.is-invalid, .invalid-feedback.d-block');
            if (firstServerInvalid) {
                const tabPane = firstServerInvalid.closest('.tab-pane');
                if (tabPane && tabPane.id) {
                    const tabTrigger = document.querySelector(`button[data-bs-target="#${tabPane.id}"]`);
                    if (tabTrigger) {
                        bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                    }
                }
            }
        });
    </script>
@endpush
