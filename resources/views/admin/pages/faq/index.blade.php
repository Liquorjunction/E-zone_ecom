@extends('admin.layouts.app')
@section('title', 'FAQ Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">FAQ Management</h4>
                        @can('faqs.create')
                            <div>
                                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add FAQ
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasAction = auth()->user()->can('faqs.update') || auth()->user()->can('faqs.delete');
                            @endphp
                            <table id="faqs-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.faqs.data') }}"
                                data-has-action="{{ $hasAction ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Answer</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        @canany(['faqs.update', 'faqs.delete'])
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

    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/faq.js') }}"></script>
    @endpush
@endsection
