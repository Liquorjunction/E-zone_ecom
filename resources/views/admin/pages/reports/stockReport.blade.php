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
                        <table id="skuChildRowsData" style="display:none;">
                            <tbody>
                                @foreach($stocks as $index => $group)
                                    @foreach($group['rows'] as $row)
                                        @php $inv = $row['inventory']; @endphp
                                        <tr class="sku-child-row bg-light" data-sku-parent="{{ $index }}">
                                            <td></td>
                                            <td></td>
                                            <td class="text-muted ps-3">
                                                <small>↳ {{ $inv->batch_no }}</small>
                                            </td>
                                            <td>{{ optional($inv->product)->name ?? 'N/A' }}</td>
                                            <td>{{ optional($inv->variant)->variant_name ?? 'N/A' }}</td>
                                            <td>{{ optional(optional($inv->product)->brand)->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($inv->warehouse_id && $inv->warehouse)
                                                    <span class="badge bg-primary">Warehouse</span>
                                                    {{ $inv->warehouse->warehouse_name }}
                                                @elseif($inv->store_id && $inv->store)
                                                    <span class="badge bg-success">Store</span>
                                                    {{ $inv->store->store_name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td></td>
                                            <td>{{ $row['total_qty'] }}</td>
                                            <td>{{ $row['sold_qty'] }}</td>
                                            <td>{{ $row['reserved_qty'] }}</td>
                                            <td>{{ $row['available_qty'] }}</td>
                                            <td>{{ $row['returned_qty'] }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                        <div id="stockReportTableWrapper">
                            <table id="stockReportTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40px;"></th>
                                        <th>#</th>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Total Qty</th>
                                        <th>Sold Qty</th>
                                        <th>Reserved Qty</th>
                                        <th>Available Qty</th>
                                        <th>Returned Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $index => $group)
                                        <tr class="sku-parent-row fw-semibold"
                                            data-sku-id="{{ $index }}"
                                            data-has-children="{{ count($group['rows']) > 1 ? '1' : '0' }}">
                                            <td class="text-center p-1">
                                                @if(count($group['rows']) > 1)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary expand-btn px-2 py-0"
                                                            data-sku-id="{{ $index }}"
                                                            title="Show locations">
                                                        <i class="bi bi-chevron-double-down"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $group['sku'] }}</span>
                                                @php
                                                    $firstRow = $group['rows'][0] ?? null;
                                                    $batchNo = $firstRow && isset($firstRow['inventory']) ? $firstRow['inventory']->batch_no ?? null : null;
                                                @endphp
                                                @if(count($group['rows']) === 1 && $batchNo)
                                                    <br><small class="text-muted">{{ $batchNo }}</small>
                                                @endif
                                            </td>
                                            <td>{{ optional($group['product'])->name ?? 'N/A' }}</td>
                                            <td>{{ optional($group['variant'])->variant_name ?? 'N/A' }}</td>
                                            <td>{{ optional($group['brand'])->name ?? 'N/A' }}</td>
                                            <td>{{ optional($group['category'])?->getTranslation('category-name', 'en') ?? 'N/A' }}</td>
                                            <td>{{ optional($group['subCategory'])?->getTranslation('sub-category-name', 'en') ?? 'N/A' }}</td>
                                            <td><span class="badge bg-dark" title="Total across all locations">{{ $group['totals']['total_qty'] }}</span></td>
                                            <td><span class="badge bg-danger" title="Sold (dispatched)">{{ $group['totals']['sold_qty'] }}</span></td>
                                            <td><span class="badge bg-warning text-dark" title="Reserved (pending/processing)">{{ $group['totals']['reserved_qty'] }}</span></td>
                                            <td><span class="badge bg-success" title="Available (not reserved)">{{ $group['totals']['available_qty'] }}</span></td>
                                            <td><span class="badge bg-info text-dark" title="Returned / RTO restocked">{{ $group['totals']['returned_qty'] }}</span></td>
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
@endsection

@push('styles')
<style>
    #stockReportTableWrapper .dataTables_wrapper > .row {
        margin-left: 0;
        margin-right: 0;
    }

    #stockReportTableWrapper .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #stockReportTableWrapper {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('admin_assets/js/stockReportTable.js') }}"></script>
@endpush
