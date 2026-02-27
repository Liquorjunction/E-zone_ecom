@extends('admin.layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contact Us Enquiries</h5>
    </div>
        <div class="card-body">
            <table id="contactUsTable" class="table table-bordered table-striped"
                data-source="{{ route('admin.contact-us.data') }}">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ticket Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/contactUs.js') }}"></script>
@endpush
