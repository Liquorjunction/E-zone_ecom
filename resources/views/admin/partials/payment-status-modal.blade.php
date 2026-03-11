<!-- Bootstrap CSS for Modal -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .modal {
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal.show {
        display: block !important;
    }

    .modal-backdrop.show {
        opacity: 0.5;
    }
</style>

<div class="modal fade" id="napsModal" tabindex="-1" aria-labelledby="napsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="napsModalLabel">NAPS Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                @if (isset($napsStatus))
                    <table class="table table-bordered">

                        <tr>
                            <th>Status</th>
                            <td>{{ $napsStatus['status'] ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status Code</th>
                            <td>{{ $napsStatus['statuscode'] ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Auth Number</th>
                            <td>{{ $napsStatus['authnumber'] ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Transaction Date</th>
                            <td>{{ $napsStatus['transactiondate'] ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Transaction Time</th>
                            <td>{{ $napsStatus['transactiontime'] ?? '-' }}</td>
                        </tr>

                    </table>
                @endif

            </div>

        </div>
    </div>
</div>
