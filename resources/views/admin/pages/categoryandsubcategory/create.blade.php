@extends('admin.layouts.app')

@section('title', 'Create Category or Subcategory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Create Category or Subcategory</h4>
                </div>
                <div class="card-body">
                    {{-- Form with ID for JavaScript targeting --}}
                    <form id="createForm" action="{{ route('admin.category-subcategory.storeCategory') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Hidden inputs for JS route switching --}}
                        <input type="hidden" id="route-category" value="{{ route('admin.category-subcategory.storeCategory') }}">
                        <input type="hidden" id="route-subcategory" value="{{ route('admin.category-subcategory.storeSubCategory') }}">

                        {{-- Type Selector --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" onchange="toggleFields()">
                                <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>Category</option>
                                <option value="subcategory" {{ old('type') == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                            </select>
                        </div>

                        {{-- SECTION: CATEGORY INPUTS --}}
                        <div id="category-section">
                            <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="category-name" id="category-name" class="form-control" value="{{ old('category-name') }}">
                                @error('category-name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category Icon</label>
                                <input type="file" name="category-icon" id="category-icon" class="form-control">
                                @error('category-icon') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- SECTION: SUBCATEGORY INPUTS --}}
                        <div id="subcategory-section" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Sub Category Name</label>
                                <input type="text" name="sub-category-name" id="sub-category-name" class="form-control" value="{{ old('sub-category-name') }}">
                                @error('sub-category-name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">Select Parent Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->{'category-name'} }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sub Category Icon</label>
                                <input type="file" name="sub-category-icon" id="sub-category-icon" class="form-control">
                                @error('sub-category-icon') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Shared Status Field --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Active</label>
                            <input type="checkbox" name="status" id="status" value="1" checked>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const form = document.getElementById('createForm');

    const catSection = document.getElementById('category-section');
    const subCatSection = document.getElementById('subcategory-section');

    // Inputs to Enable/Disable
    const catName = document.getElementById('category-name');
    const subCatName = document.getElementById('sub-category-name');
    const catId = document.getElementById('category_id');

    if (type === 'subcategory') {
        // Show SubCategory
        catSection.style.display = 'none';
        subCatSection.style.display = 'block';

        // Update Form Action
        form.action = document.getElementById('route-subcategory').value;

        // Toggle Required/Disabled (Disabled inputs are not sent in request, preventing errors)
        catName.disabled = true;
        subCatName.disabled = false;
        subCatName.required = true;
        catId.disabled = false;
        catId.required = true;
    } else {
        // Show Category
        catSection.style.display = 'block';
        subCatSection.style.display = 'none';

        // Update Form Action
        form.action = document.getElementById('route-category').value;

        // Toggle Required/Disabled
        catName.disabled = false;
        catName.required = true;
        subCatName.disabled = true;
        catId.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endsection
