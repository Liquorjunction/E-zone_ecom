@extends('admin.layouts.app')
@section('title', 'Edit SubCategory')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Edit SubCategory</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.subcategories.update', $subCategory->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Parent Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            {{-- FIX: Accessed {'category-name'} correctly --}}
                            <option value="{{ $cat->id }}" {{ (old('category_id', $subCategory->category_id) == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->{'category-name'} }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sub Category Name</label>
                    {{-- FIX: Accessed {'sub-category-name'} correctly --}}
                    <input type="text" name="sub-category-name" id="sub-category-name" class="form-control" value="{{ old('sub-category-name', $subCategory->{'sub-category-name'}) }}">
                    @error('sub-category-name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sub Category Icon</label>
                    <input type="file" name="sub-category-icon" id="sub-category-icon" class="form-control">
                    @if($subCategory->{'sub-category-icon'})
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $subCategory->{'sub-category-icon'}) }}" alt="Current Icon" width="50">
                        </div>
                    @endif
                    @error('sub-category-icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3 form-check">
                    {{-- FIX: Checked logic against Enum 'active' --}}
                    <input type="checkbox" class="form-check-input" name="status" id="active" value="1"
                        {{ (old('status') || $subCategory->status === 'active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update SubCategory</button>
                <a href="{{ route('admin.category-subcategory.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('admin_assets/js/subCategoryForm.js') }}"></script>
@endsection
