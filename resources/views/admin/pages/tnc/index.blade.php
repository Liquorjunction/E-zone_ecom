@extends('admin.layouts.app')
@section('title', 'Terms and Conditions')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Terms and Conditions</h4>
                        @can('tnc.create')
                            @if($tncCount == 0)
                                <div>
                                    <a href="{{ route('admin.tncs.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Terms and Conditions
                                    </a>
                                </div>
                            @else
                                <div>
                                    <small class="text-muted">Only one Terms and Conditions entry is allowed.</small>
                                </div>
                            @endif
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tnc-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.tncs.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Last Updated</th>
                                        {{-- <th>Status</th> --}}
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
        <script src="{{ asset('admin_assets/js/datatableJs/tnc.js') }}"></script>
    @endpush
@endsection
