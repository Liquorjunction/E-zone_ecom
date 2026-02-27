@php
    $currentStatus = $enquiry->status ?? '';
    // Define allowed transitions (should match controller logic)
    $allowedTransitions = [
        'open' => ['in_progress', 'closed'],
        'in_progress' => ['resolved', 'closed'],
        'resolved' => ['closed'],
        'closed' => [],
    ];
    $transitions = $allowedTransitions[$currentStatus] ?? [];
@endphp

@if(isset($enquiry) && isset($statusLabels) && isset($availableStatuses))
    <div class="d-flex align-items-center gap-2">
        <span class="badge px-3 py-2 fw-semibold"
            style="background: #e9ecef; color: #333; border-radius: 12px; font-size: 0.95em;">
            {{ $statusLabels[$currentStatus] ?? ucfirst($currentStatus) }}
        </span>
        @if($canUpdate && count($availableStatuses) > 1 && count($transitions) > 0)
            <div class="dropdown">
                <button class="btn btn-link p-0 border-0 text-secondary dropdown-toggle" type="button" id="enquiryStatusDropdown{{ $enquiry->id }}" data-bs-toggle="dropdown" aria-expanded="false" title="Change Status">
                    <i class="bi bi-pencil-square fs-5"></i>
                </button>
                <ul class="dropdown-menu shadow-sm small" aria-labelledby="enquiryStatusDropdown{{ $enquiry->id }}">
                    @foreach($transitions as $value)
                        @if(isset($statusLabels[$value]))
                            <li>
                                <button type="button" class="dropdown-item change-enquiry-status-action"
                                    data-enquiry-id="{{ $enquiry->id }}"
                                    data-status="{{ $value }}"
                                    data-url="{{ route('admin.contact-us.updateStatus') }}">
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
