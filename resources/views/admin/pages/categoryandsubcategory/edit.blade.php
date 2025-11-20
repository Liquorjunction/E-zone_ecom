@extends('admin.layouts.app')

@section('title', 'Edit Category or Subcategory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit {{ ucfirst($type) }}</h4>
                </div>
                <div class="card-body">
                    {{-- Form Action includes Type and ID in query string --}}
                    <form action="{{ route('admin.category-subcategory.update', ['id' => $item->id, 'type' => $type]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- CONDITIONAL INPUTS BASED ON TYPE --}}
                        @if($type === 'category')
                            {{-- Category Inputs --}}
                            <div class="mb-3">
                                <label for="category-name" class="form-label">Category Name</label>
                                <input type="text" name="category-name" id="category-name" class="form-control" value="{{ old('category-name', $item->{'category-name'}) }}" required>
                                @error('category-name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category-icon" class="form-label">Category Icon</label>
                                <input type="file" name="category-icon" id="category-icon" class="form-control">
                                @error('category-icon') <div class="text-danger">{{ $message }}</div> @enderror
                                @if($item->{'category-icon'})
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $item->{'category-icon'}) }}" alt="Current Icon" width="100">
                                    </div>
                                @endif
                            </div>

                        @else
                            {{-- SubCategory Inputs --}}
                            <div class="mb-3">
                                <label for="sub-category-name" class="form-label">Sub Category Name</label>
                                <input type="text" name="sub-category-name" id="sub-category-name" class="form-control" value="{{ old('sub-category-name', $item->{'sub-category-name'}) }}" required>
                                @error('sub-category-name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Parent Category</label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->{'category-name'} }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sub-category-icon" class="form-label">Sub Category Icon</label>
                                <input type="file" name="sub-category-icon" id="sub-category-icon" class="form-control">
                                @error('sub-category-icon') <div class="text-danger">{{ $message }}</div> @enderror
                                @if($item->{'sub-category-icon'})
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $item->{'sub-category-icon'}) }}" alt="Current Icon" width="100">
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Shared Status Input --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Active</label>
                            {{-- Note: We use 'status' as the name to match the Request logic --}}
                            <input type="checkbox" name="status" id="status" value="1" {{ old('status', $item->status) ? 'checked' : '' }}>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
