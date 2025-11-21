@extends('admin.layouts.app')
@section('title', 'Create Category')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Category</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.categories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category-name" id="category-name" class="form-control"
                            value="{{ old('category-name') }}">
                        @error('category-name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Icon</label>
                        <input type="file" name="category-icon" id="category-icon" class="form-control">
                        @error('category-icon')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        {{-- Value 1 is fine because Repository uses $request->has('status') --}}
                        <input type="checkbox" class="form-check-input" name="status" id="active" value="1"
                            checked>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                    <button type="submit" class="btn btn-success">Save Category</button>
                    <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin_assets/js/categoryForm.js') }}"></script>
@endsection
