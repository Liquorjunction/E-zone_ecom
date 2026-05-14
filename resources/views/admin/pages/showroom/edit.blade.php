@extends('admin.layouts.app')

@section('title', 'Edit Showroom')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Showroom: {{ $showroom->getTranslation('name', 'en') }}</h4>
                        </div>
                        <a href="{{ route('admin.showrooms.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.showrooms.update', $showroom->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $showroom->id }}">

                            <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-edit" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1"
                                        role="tab">Step 1: Basic Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step2-tab" data-bs-toggle="pill" href="#step2"
                                        role="tab">Step 2: Details & Media</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent-edit">
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="row">
                                        {{-- Translatable Fields Container --}}
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Showroom Details</strong></label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-primary btn-translate-all"
                                                        id="btn-translate-all"
                                                        data-url="{{ route('admin.translate.batch') }}"
                                                        data-fields='["name", "address", "description", "city", "state", "country"]'>
                                                        <i class="bi bi-translate me-2"></i> Translate All Fields
                                                    </button>
                                                    <ul class="nav nav-pills nav-pills-sm mb-0 ms-2" id="langTabEdit"
                                                        role="tablist">
                                                        <li class="nav-item"><a class="nav-link active py-1 px-3"
                                                                data-bs-toggle="tab" href="#edit-lang-en">English</a></li>
                                                        <li class="nav-item"><a class="nav-link py-1 px-3"
                                                                data-bs-toggle="tab" href="#edit-lang-fr">French</a></li>
                                                        <li class="nav-item"><a class="nav-link py-1 px-3"
                                                                data-bs-toggle="tab" href="#edit-lang-ar">Arabic</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                {{-- EN --}}
                                                <div class="tab-pane fade show active" id="edit-lang-en">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Name (EN) <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="name[en]" id="name_en" class="form-control"
                                                                value="{{ $showroom->getTranslation('name', 'en') }}"
                                                                required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Address (EN) <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea name="address[en]" id="address_en" class="form-control" rows="2" required>{{ $showroom->getTranslation('address', 'en') }}</textarea>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Description (EN)</label>
                                                            <textarea name="description[en]" id="description_en" class="form-control" rows="4">{{ $showroom->getTranslation('description', 'en') }}</textarea>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">City (EN) <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="city[en]" id="city_en" class="form-control"
                                                                value="{{ $showroom->getTranslation('city', 'en') }}"
                                                                required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">State (EN) <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="state[en]" id="state_en" class="form-control"
                                                                value="{{ $showroom->getTranslation('state', 'en') }}"
                                                                required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Country (EN) <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="country[en]" id="country_en" class="form-control"
                                                                value="{{ $showroom->getTranslation('country', 'en') }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- FR --}}
                                                <div class="tab-pane fade" id="edit-lang-fr">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Name (FR)</label>
                                                            <input type="text" name="name[fr]" id="name_fr" class="form-control"
                                                                value="{{ $showroom->getTranslation('name', 'fr') }}">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Address (FR)</label>
                                                            <textarea name="address[fr]" id="address_fr" class="form-control" rows="2">{{ $showroom->getTranslation('address', 'fr') }}</textarea>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Description (FR)</label>
                                                            <textarea name="description[fr]" id="description_fr" class="form-control" rows="4">{{ $showroom->getTranslation('description', 'fr') }}</textarea>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">City (FR)</label>
                                                            <input type="text" name="city[fr]" id="city_fr" class="form-control"
                                                                value="{{ $showroom->getTranslation('city', 'fr') }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">State (FR)</label>
                                                            <input type="text" name="state[fr]" id="state_fr" class="form-control"
                                                                value="{{ $showroom->getTranslation('state', 'fr') }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Country (FR)</label>
                                                            <input type="text" name="country[fr]" id="country_fr" class="form-control"
                                                                value="{{ $showroom->getTranslation('country', 'fr') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- AR --}}
                                                <div class="tab-pane fade" id="edit-lang-ar">
                                                    <div class="row" dir="rtl">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Name (AR)</label>
                                                            <input type="text" name="name[ar]" id="name_ar" class="form-control"
                                                                value="{{ $showroom->getTranslation('name', 'ar') }}">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Address (AR)</label>
                                                            <textarea name="address[ar]" id="address_ar" class="form-control" rows="2">{{ $showroom->getTranslation('address', 'ar') }}</textarea>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Description (AR)</label>
                                                            <textarea name="description[ar]" id="description_ar" class="form-control" rows="4">{{ $showroom->getTranslation('description', 'ar') }}</textarea>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">City (AR)</label>
                                                            <input type="text" name="city[ar]" id="city_ar" class="form-control"
                                                                value="{{ $showroom->getTranslation('city', 'ar') }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">State (AR)</label>
                                                            <input type="text" name="state[ar]" id="state_ar" class="form-control"
                                                                value="{{ $showroom->getTranslation('state', 'ar') }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Country (AR)</label>
                                                            <input type="text" name="country[ar]" id="country_ar" class="form-control"
                                                                value="{{ $showroom->getTranslation('country', 'ar') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Common Fields --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $showroom->email }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $showroom->phone }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pincode</label>
                                            <input type="text" name="pincode" class="form-control"
                                                value="{{ $showroom->pincode }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sort Order</label>
                                            <input type="number" name="sort_order" class="form-control"
                                                value="{{ $showroom->sort_order }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active"
                                                    {{ $showroom->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive"
                                                    {{ $showroom->status == 'inactive' ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Update Cover Image</label>
                                            <input type="file" name="cover_image" class="form-control"
                                                accept="image/*">
                                            @if ($showroom->cover_image)
                                                <img src="{{ asset('storage/' . $showroom->cover_image) }}" alt="Cover"
                                                    class="mt-2" style="height: 60px; border-radius: 4px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary next-step"
                                            data-next="step2">Next</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Map Iframe</label>
                                            <textarea name="map_iframe" class="form-control" rows="3">{{ $showroom->map_iframe }}</textarea>
                                        </div>

                                        @php $hours = $showroom->opening_hours ?? []; @endphp
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Opening Hours</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Day</th>
                                                            <th>Open Time</th>
                                                            <th>Close Time</th>
                                                            <th>Closed</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                            @php
                                                                $key = strtolower($day);
                                                                $open = $hours[$key]['open'] ?? '09:00';
                                                                $close = $hours[$key]['close'] ?? '18:00';
                                                                $isClosed = $hours[$key]['is_closed'] ?? false;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $day }}</td>
                                                                <td><input type="time"
                                                                        name="opening_hours[{{ $key }}][open]"
                                                                        class="form-control form-control-sm open-time"
                                                                        value="{{ $open }}"></td>
                                                                <td><input type="time"
                                                                        name="opening_hours[{{ $key }}][close]"
                                                                        class="form-control form-control-sm close-time"
                                                                        value="{{ $close }}"></td>
                                                                <td><input type="checkbox"
                                                                        name="opening_hours[{{ $key }}][is_closed]"
                                                                        class="form-check-input is-closed-check"
                                                                        value="1"
                                                                        {{ $isClosed ? 'checked' : '' }}></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Gallery Images</label>
                                            <input type="file" name="gallery_images[]" id="gallery_images_input" class="form-control" multiple
                                                accept="image/*">

                                            <!-- Existing Gallery Images -->
                                            @if ($showroom->images->count() > 0)
                                            <div class="mt-3">
                                                <h6>Existing Gallery Images</h6>
                                                <div class="d-flex flex-wrap gap-2" id="existing-gallery">
                                                    @foreach ($showroom->images as $img)
                                                        <div class="position-relative gallery-image-item" data-image-id="{{ $img->id }}">
                                                            <img src="{{ asset('storage/' . $img->image) }}"
                                                                style="height:60px; width:60px; object-fit:cover; border-radius:4px;">
                                                            <button type="button" class="btn btn-sm btn-danger delete-existing-image"
                                                                style="position: absolute; top: -8px; right: -8px; padding: 2px 6px; font-size: 10px;"
                                                                data-image-id="{{ $img->id }}"
                                                                data-url="{{ route('admin.showrooms.image.delete', $img->id) }}"
                                                                title="Delete Image">
                                                                ×
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            <!-- New Gallery Images Preview -->
                                            <div class="mt-3" id="new-gallery-preview" style="display: none;">
                                                <h6>New Images to Upload</h6>
                                                <div class="d-flex flex-wrap gap-2" id="new-gallery"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-secondary prev-step"
                                            data-prev="step1">Previous</button>
                                        <button type="submit" class="btn btn-warning">Update Showroom</button>
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
