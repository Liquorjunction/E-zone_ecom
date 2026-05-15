@extends('admin.layouts.app')

@section('title', 'Create Showroom')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create New Showroom</h4>
                    </div>
                    <div class="card-action">
                        <button type="button" class="btn btn-sm btn-primary btn-translate-all" id="btn-translate-all"
                            data-url="{{ route('admin.translate.batch') }}"
                            data-fields='["name","address","description","city","state","country"]'>
                            <i class="bi bi-translate me-2"></i> Translate All Fields
                        </button>
                        <a href="{{ route('admin.showrooms.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.showrooms.store') }}" method="POST" enctype="multipart/form-data" id="showroom-form">
                        @csrf

                        <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Step 1: Basic Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="step2-tab" data-bs-toggle="pill" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Step 2: Details & Media</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent-1">

                            <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                                <div class="row">
                                    {{-- Translatable Fields Container --}}
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0"><strong>Showroom Details</strong> <span class="text-danger">*</span></label>
                                            <ul class="nav nav-pills nav-pills-sm" id="langTab" role="tablist">
                                                <li class="nav-item"><a class="nav-link active py-1 px-3" data-bs-toggle="tab" href="#lang-en">English</a></li>
                                                <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#lang-fr">French</a></li>
                                                <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#lang-ar">Arabic</a></li>
                                            </ul>
                                        </div>

                                        <div class="tab-content border p-3 rounded">

                                            {{-- ENGLISH TAB (Mandatory) --}}
                                            <div class="tab-pane fade show active" id="lang-en">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Name (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="name[en]" class="form-control" placeholder="Showroom Name" required value="{{ old('name.en') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Address (EN) <span class="text-danger">*</span></label>
                                                        <textarea name="address[en]" class="form-control" rows="2" required>{{ old('address.en') }}</textarea>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="form-label mb-0">Description (EN)</label>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <small class="text-muted mb-0">Please use this for translating descriptions</small>
                                                                <button type="button" class="btn btn-sm btn-info text-white btn-translate-auto"
                                                                    data-url="{{ route('admin.translate.content') }}" data-source="editor_description_en"
                                                                    data-target-base="description"
                                                                    data-quill="true">
                                                                    <i class="bi bi-translate mr-1"></i> Translate to Others
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <textarea name="description[en]" id="description_en" class="form-control" rows="4">{{ old('description.en') }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">City (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="city[en]" class="form-control" required value="{{ old('city.en') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">State (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="state[en]" class="form-control" required value="{{ old('state.en') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Country (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="country[en]" class="form-control" required value="{{ old('country.en') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- FRENCH TAB --}}
                                            <div class="tab-pane fade" id="lang-fr">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Name (FR)</label>
                                                        <input type="text" name="name[fr]" class="form-control" placeholder="Nom du magasin" value="{{ old('name.fr') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Address (FR)</label>
                                                        <textarea name="address[fr]" class="form-control" rows="2">{{ old('address.fr') }}</textarea>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description (FR)</label>
                                                        <textarea name="description[fr]" id="description_fr" class="form-control" rows="4">{{ old('description.fr') }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">City (FR)</label>
                                                        <input type="text" name="city[fr]" class="form-control" value="{{ old('city.fr') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">State (FR)</label>
                                                        <input type="text" name="state[fr]" class="form-control" value="{{ old('state.fr') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Country (FR)</label>
                                                        <input type="text" name="country[fr]" class="form-control" value="{{ old('country.fr') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ARABIC TAB --}}
                                            <div class="tab-pane fade" id="lang-ar">
                                                <div class="row" dir="rtl">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Name (AR)</label>
                                                        <input type="text" name="name[ar]" class="form-control" placeholder="اسم المعرض" value="{{ old('name.ar') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Address (AR)</label>
                                                        <textarea name="address[ar]" class="form-control" rows="2">{{ old('address.ar') }}</textarea>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description (AR)</label>
                                                        <textarea name="description[ar]" id="description_ar" class="form-control" rows="4">{{ old('description.ar') }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">City (AR)</label>
                                                        <input type="text" name="city[ar]" class="form-control" value="{{ old('city.ar') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">State (AR)</label>
                                                        <input type="text" name="state[ar]" class="form-control" value="{{ old('state.ar') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Country (AR)</label>
                                                        <input type="text" name="country[ar]" class="form-control" value="{{ old('country.ar') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Common Fields --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" name="pincode" class="form-control" required value="{{ old('pincode') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Cover Image <span class="text-danger">*</span></label>
                                        <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-primary next-step" data-next="step2">Next: Details & Media</button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Google Map Embed Iframe</label>
                                        <textarea name="map_iframe" class="form-control" rows="3">{{ old('map_iframe') }}</textarea>
                                    </div>

                                    {{-- Opening Hours --}}
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Opening Hours</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead><tr><th>Day</th><th>Open Time</th><th>Close Time</th><th>Closed</th></tr></thead>
                                                <tbody>
                                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                                    <tr>
                                                        <td>{{ $day }}</td>
                                                        <td><input type="time" name="opening_hours[{{ strtolower($day) }}][open]" class="form-control form-control-sm open-time" value="09:00"></td>
                                                        <td><input type="time" name="opening_hours[{{ strtolower($day) }}][close]" class="form-control form-control-sm close-time" value="18:00"></td>
                                                        <td><input type="checkbox" name="opening_hours[{{ strtolower($day) }}][is_closed]" class="form-check-input is-closed-check" value="1"></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Gallery Images</label>
                                        <input type="file" name="gallery_images[]" id="gallery_images_input" class="form-control" multiple accept="image/*">

                                        <!-- New Gallery Images Preview -->
                                        <div class="mt-3" id="new-gallery-preview" style="display: none;">
                                            <h6>Images to Upload</h6>
                                            <div class="d-flex flex-wrap gap-2" id="new-gallery"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-secondary prev-step" data-prev="step1">Previous</button>
                                    <button type="submit" class="btn btn-success">Create Showroom</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets/js/showroom-form.js') }}"></script>
@endpush
@endsection
