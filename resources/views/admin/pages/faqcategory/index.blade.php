@extends('admin.layouts.app')

@section('title', 'Faq Categories')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Faq Categories</h4>
                        @can('faqs.create')
                            <div>
                                <a href="{{ route('admin.faq-categories.create') }}" class="btn btn-primary">Add Faq Category</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="faqcategories-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.faq-categories.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/faqcategories.js') }}"></script>
    @endpush
@endsection
