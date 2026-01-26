@extends('admin.layouts.app')
@section('title', 'Stock Report')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Stock Report</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="stockReportTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Location</th>
                                        <th>Total Qty</th>
                                        <th>Sold Qty</th>
                                        <th>Reserved Qty</th>
                                        <th>Available Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $index => $report)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ optional($report->variant)->sku ?? 'N/A' }}</td>
                                            <td>{{ optional($report->product)->name ?? 'N/A' }}</td>
                                            <td>{{ optional($report->variant)->variant_name ?? 'N/A' }}</td>
                                            <td>{{ optional(optional($report->product)->brand)->name ?? 'N/A' }}</td>
                                            <td>{{ optional(optional($report->product)->category)->{'category-name'} ?? 'N/A' }}</td>
                                            <td>{{ optional(optional($report->product)->subCategory)->{'sub-category-name'} ?? 'N/A' }}</td>
                                            <td>
                                                @if($report->warehouse_id && $report->warehouse)
                                                    <span class="badge bg-primary">Warehouse</span> {{ $report->warehouse->warehouse_name }}
                                                @elseif($report->store_id && $report->store)
                                                    <span class="badge bg-success">Store</span> {{ $report->store->store_name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span title="Total = Available + Reserved + Sold">{{ $report->total_qty }}</span>
                                            </td>
                                            <td>
                                                <span title="Sold (Completed/Paid/Delivered orders)">{{ $report->sold_qty }}</span>
                                            </td>
                                            <td>
                                                <span title="Reserved (Pending/Processing orders)">{{ $report->reserved_qty }}</span>
                                            </td>
                                            <td>
                                                <span title="Available (Current stock not reserved)">{{ $report->available_qty }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/stockReportTable.js') }}"></script>
@endpush
