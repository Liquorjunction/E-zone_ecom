@extends('admin.layouts.app')

@section('title', 'Categories')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.category-subcategory.categories.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Categories</h4>
                        @can('category.create')
                            <div>
                                <a href="{{ route('admin.category-subcategory.categories.create') }}" class="btn btn-primary">Add
                                    Category</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasCatActions = auth()->user()->can('category.update') || auth()->user()->can('category.delete');
                            @endphp
                            <table id="categories-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.category-subcategory.categories.data') }}"
                                data-has-actions="{{ $hasCatActions ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        @canany(['category.update', 'category.delete'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/categorySubCategory.js') }}"></script>
@endpush
