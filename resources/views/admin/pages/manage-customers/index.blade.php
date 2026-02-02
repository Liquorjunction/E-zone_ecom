@extends('admin.layouts.app')
@section('title', 'Manage Customers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Manage Customers</h4>
                        <small class="text-muted">View and manage all registered customers</small>
                    </div>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table id="customers-table" class="table table-striped table-hover w-100"
                            data-route="{{ route('admin.customers.data') }}">
                            <thead class="bg-light text-uppercase small">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joining Date</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data Loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/manage-customers.js') }}"></script>
@endpush
