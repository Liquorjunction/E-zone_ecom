@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Orders</h4>
                        <small class="text-muted">Manage all customer orders</small>
                    </div>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table id="orders-table" class="table table-striped table-hover w-100"
                            data-route="{{ route('admin.orders.data') }}">
                            <thead class="bg-light text-uppercase small">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Order Status</th>
                                    <th>Cancelled By</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Status</th>
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

{{-- NAPS Payment Status Modal Container --}}
<div id="naps-modal-container"></div>

@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/orders.js') }}"></script>
    <script>
        $(document).on('click', '.naps-status-action', function () {
            var url = $(this).data('naps-url');
            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    if (response.success === false) {
                        alert(response.message || 'Could not retrieve NAPS status.');
                        return;
                    }
                    $('#naps-modal-container').html(response.modal_html);
                    var el = document.getElementById('napsModal');
                    if (el && el.parentElement !== document.body) {
                        document.body.appendChild(el);
                    }
                    var modal = new bootstrap.Modal(el);
                    modal.show();
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to fetch NAPS status.';
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
