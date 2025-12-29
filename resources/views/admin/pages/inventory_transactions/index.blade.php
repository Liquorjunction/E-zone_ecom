@extends('admin.layouts.app')
@section('title', 'Inventory Transactions')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inventory Transactions</h3>
                    </div>
                    <div class="card-body">
                        <table id="inventory-transactions-table" class="table table-bordered table-striped" style="width:100%;"
                            data-source="{{ route('admin.inventory-transactions.data') }}">
                            <thead>
                                <tr>
                                    <th></th> <!-- For the plus icon -->
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Location Type</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/inventory-transactions.js') }}"></script>
@endpush
