@extends('admin.layouts.app')

@section('title', 'Shipping & Returns')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between border-bottom flex-wrap py-3">
                        <div class="header-title">
                            <h4 class="card-title">Shipping & Returns Policy List</h4>
                        </div>
                        <div class="card-action">
                            @can('shipping_return.create')
                                @if ($shippingReturnCount == 0)
                                    {
                                    <a href="{{ route('admin.shipping-return.create') }}"
                                        class="btn btn-sm btn-primary add-button">
                                        <i class="fa fa-plus mr-2"></i>Add New
                                    </a>
                                    }
                                @else
                                    <div>
                                        <small class="text-muted">Only one Shipping & Returns entry is allowed.</small>
                                    </div>
                                @endif
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="shipping-return-table" class="table table-striped"
                                data-route="{{ route('admin.shipping-return.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title (EN)</th>
                                        <th>Description (EN)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables will populate this --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/shippingReturn.js') }}"></script>
    @endpush
@endsection
