@extends('admin.layouts.app')
@section('title', 'Dispatch Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Dispatches</h3>
                    <a href="{{ route('admin.dispatch.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Dispatch
                    </a>
                </div>
                <div class="card-body">
                    <table id="dispatch-table" class="table table-bordered table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Items</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables or backend will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
