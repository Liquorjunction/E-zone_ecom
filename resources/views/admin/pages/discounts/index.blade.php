@extends('admin.layouts.app')

@section('title', 'Manage Discounts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Discounts List</h5>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Discount
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="discounts-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name (EN)</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Targets</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#discounts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.discounts.datatable') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'value', name: 'value' },
                { data: 'starts_at', name: 'starts_at' },
                { data: 'ends_at', name: 'ends_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'targets', name: 'targets', orderable: false, searchable: false },
            ]
        });

    });
</script>
@endpush
