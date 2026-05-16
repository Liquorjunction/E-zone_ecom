@php
    $currentStatus = $order->status ?? '';
@endphp

@if(isset($order) && isset($statusLabels) && isset($availableStatuses))
    <div class="d-flex align-items-center gap-2">
        <span class="badge px-3 py-2 fw-semibold"
            style="background: #e9ecef; color: #333; border-radius: 12px; font-size: 0.95em;">
            {{ $statusLabels[$currentStatus] ?? ucfirst($currentStatus) }}
        </span>
        @if($canUpdate && count($availableStatuses) > 1)
            <div class="dropdown datatable-status-dropdown">
                <button class="btn btn-link p-0 border-0 text-secondary dropdown-toggle" type="button" id="orderStatusDropdown{{ $order->id }}" data-bs-toggle="dropdown" aria-expanded="false" title="Change Status">
                    <i class="bi bi-pencil-square fs-5"></i>
                </button>
                <ul class="dropdown-menu shadow-sm small" aria-labelledby="orderStatusDropdown{{ $order->id }}">
                    @foreach($availableStatuses as $value)
                        @if($value !== $currentStatus && isset($statusLabels[$value]))
                            <li>
                                <button type="button" class="dropdown-item change-order-status-action"
                                    data-order-id="{{ $order->id }}"
                                    data-status="{{ $value }}"
                                    data-url="{{ route('admin.orders.update-order-status') }}">
                                    {{ $statusLabels[$value] }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
