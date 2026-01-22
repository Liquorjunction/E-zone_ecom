@extends('admin.layouts.app')
@section('title', 'Why Shop With Us')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Why Shop With Us</h4>
                        @can('why_choose_us.create')
                            {{-- <div>
                                <a href="{{ route('admin.whyshopwithus.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Entry
                                </a>
                            </div> --}}
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasAction =
                                    auth()->user()->can('why_choose_us.update') ||
                                    auth()->user()->can('why_choose_us.delete');
                            @endphp
                            <table id="whyshopwithus-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.whyshopwithus.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Icon</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        {{-- <th>Status</th> --}}
                                        @canany(['why_choose_us.update', 'why_choose_us.delete'])
                                            <th>Actions</th>
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
        <script src="{{ asset('admin_assets/js/datatableJs/whyShopWithUs.js') }}"></script>
    @endpush
@endsection
