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
                    {{-- Form needs an ID to be targeted by JS --}}
                    {{-- Default action is set to category, but JS will update it immediately --}}
                    <form id="createForm" action="{{ route('admin.category-subcategory.storeCategory') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Hidden inputs to store route URLs for JavaScript --}}
                        <input type="hidden" id="route-category" value="{{ route('admin.category-subcategory.storeCategory') }}">
                        <input type="hidden" id="route-subcategory" value="{{ route('admin.category-subcategory.storeSubCategory') }}">

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" required onchange="toggleCategoryField()">
                                {{-- Added old() check to keep selection after validation error --}}
                                <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>Category</option>
                                <option value="subcategory" {{ old('type') == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="category-field" style="display: none;">
                            <label for="category_id" class="form-label">Parent Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Select Parent Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Active</label>
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
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
function toggleCategoryField() {
    const type = document.getElementById('type').value;
    const categoryField = document.getElementById('category-field');
    const categoryInput = document.getElementById('category_id');

    // Get the form element and route values
    const form = document.getElementById('createForm');
    const routeCategory = document.getElementById('route-category').value;
    const routeSubCategory = document.getElementById('route-subcategory').value;

    if (type === 'subcategory') {
        // Show Parent Category dropdown
        categoryField.style.display = 'block';
        categoryInput.required = true;

        // Switch form action to SubCategory store route
        form.action = routeSubCategory;
    } else {
        // Hide Parent Category dropdown
        categoryField.style.display = 'none';
        categoryInput.required = false;

        // Switch form action to Category store route
        form.action = routeCategory;
    }
}

// Run on page load to ensure correct state (handles "back" button or validation redirects)
document.addEventListener('DOMContentLoaded', function() {
    toggleCategoryField();
});
</script>
@endsection
