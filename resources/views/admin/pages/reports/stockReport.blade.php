@extends('admin.layouts.app')
@section('title', 'Stock Report')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between py-3">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-box-seam me-2 text-primary"></i>Stock Report
                        </h4>
                    </div>
                    <div class="card-body p-0">

                        {{-- Hidden data source for child rows --}}
                        <table id="skuChildRowsData" style="display:none;">
                            <tbody>
                                @foreach($stocks as $index => $group)
                                    @foreach($group['rows'] as $row)
                                        @php $inv = $row['inventory']; @endphp
                                        <tr class="sku-child-row" data-sku-parent="{{ $index }}">
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <span class="child-batch-badge">
                                                    <i class="bi bi-arrow-return-right me-1"></i>{{ $inv->batch_no }}
                                                </span>
                                            </td>
                                            <td>{{ optional($inv->product)->name ?? 'N/A' }}</td>
                                            <td>{{ optional($inv->variant)->variant_name ?? 'N/A' }}</td>
                                            <td>{{ optional(optional($inv->product)->brand)->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($inv->warehouse_id && $inv->warehouse)
                                                    <span class="badge bg-primary">Warehouse</span>
                                                    <span class="text-muted small">{{ $inv->warehouse->warehouse_name }}</span>
                                                @elseif($inv->store_id && $inv->store)
                                                    <span class="badge bg-success">Store</span>
                                                    <span class="text-muted small">{{ $inv->store->store_name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>—</td>
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

                        {{-- Main DataTable --}}
                        <div id="stockReportTableWrapper" class="p-3">
                            <table id="stockReportTable" class="table table-bordered dataTable">
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
                                        @php
                                            $hasChildren = count($group['rows']) > 1;
                                            $firstRow    = $group['rows'][0] ?? null;
                                            $batchNo     = $firstRow && isset($firstRow['inventory'])
                                                            ? $firstRow['inventory']->batch_no ?? null
                                                            : null;
                                        @endphp
                                        <tr class="sku-parent-row {{ $hasChildren ? 'has-children' : '' }}"
                                            data-sku-id="{{ $index }}"
                                            data-has-children="{{ $hasChildren ? '1' : '0' }}">

                                            {{-- Expand button --}}
                                            <td class="text-center align-middle expand-cell">
                                                @if($hasChildren)
                                                    <button type="button"
                                                            class="btn btn-sm expand-btn"
                                                            data-sku-id="{{ $index }}"
                                                            title="Show batch locations">
                                                        <i class="bi bi-chevron-double-down"></i>
                                                    </button>
                                                @endif
                                            </td>

                                            <td class="align-middle">{{ $index + 1 }}</td>

                                            <td class="align-middle">
                                                <span class="badge bg-secondary sku-badge">{{ $group['sku'] }}</span>
                                                @if(!$hasChildren && $batchNo)
                                                    <br><small class="text-muted">{{ $batchNo }}</small>
                                                @endif
                                                @if($hasChildren)
                                                    <br><small class="text-primary fw-semibold">
                                                        <i class="bi bi-layers me-1"></i>{{ count($group['rows']) }} batches
                                                    </small>
                                                @endif
                                            </td>

                                            <td class="align-middle fw-semibold">{{ optional($group['product'])->name ?? 'N/A' }}</td>
                                            <td class="align-middle">{{ optional($group['variant'])->variant_name ?? 'N/A' }}</td>
                                            <td class="align-middle">{{ optional($group['brand'])->name ?? 'N/A' }}</td>
                                            <td class="align-middle">{{ optional($group['category'])?->getTranslation('category-name', 'en') ?? 'N/A' }}</td>
                                            <td class="align-middle">{{ optional($group['subCategory'])?->getTranslation('sub-category-name', 'en') ?? 'N/A' }}</td>

                                            <td class="align-middle text-center">
                                                <span class="badge bg-dark qty-badge" title="Total across all locations">
                                                    {{ $group['totals']['total_qty'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-danger qty-badge" title="Sold (dispatched)">
                                                    {{ $group['totals']['sold_qty'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-warning text-dark qty-badge" title="Reserved (pending/processing)">
                                                    {{ $group['totals']['reserved_qty'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-success qty-badge" title="Available">
                                                    {{ $group['totals']['available_qty'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-info text-dark qty-badge" title="Returned / RTO restocked">
                                                    {{ $group['totals']['returned_qty'] }}
                                                </span>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/stockReportTable.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('admin_assets/js/stockReportTable.js') }}"></script>
@endpush
