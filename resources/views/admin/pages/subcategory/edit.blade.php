@extends('admin.layouts.app')
@section('header-title', 'Edit SubCategory')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header">
                <h4>Edit SubCategory</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.subcategories.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $subCategory->id }}">

                    <div class="form-group mb-3">
                        <label>Parent Category</label>
                        <select name="category_id" class="form-control" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ $subCategory->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->getTranslation('category-name', 'en') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en"
                                type="button">English</button>
                        </li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fr"
                                type="button">French</button>
                        </li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ar"
                                type="button">Arabic</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="en">
                            <div class="form-group mb-3">
                                <label>SubCategory Name (EN)</label>
                                <input type="text" name="sub-category-name[en]" class="form-control"
                                    value="{{ $subCategory->getTranslation('sub-category-name', 'en') }}" required>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="fr">
                            <div class="form-group mb-3">
                                <label>SubCategory Name (FR)</label>
                                <input type="text" name="sub-category-name[fr]" class="form-control"
                                    value="{{ $subCategory->getTranslation('sub-category-name', 'fr') }}">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ar">
                            <div class="form-group mb-3" dir="rtl">
                                <label>SubCategory Name (AR)</label>
                                <input type="text" name="sub-category-name[ar]" class="form-control"
                                    value="{{ $subCategory->getTranslation('sub-category-name', 'ar') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Icon</label>
                        @if ($subCategory->{'sub-category-icon'})
                            <img src="{{ asset('storage/' . $subCategory->{'sub-category-icon'}) }}" width="50"
                                class="d-block mb-2">
                        @endif
                        <input type="file" name="sub-category-icon" class="form-control">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" value="active"
                            {{ $subCategory->status == 'active' ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update SubCategory</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-img');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
