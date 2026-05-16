@props(['status' || 'is_active'])
@php
    $isActive = false;
    if (is_bool($status)) {
        $isActive = $status;
    } elseif (is_numeric($status)) {
        $isActive = ((int)$status === 1);
    } elseif (is_string($status)) {
        $isActive = (strtolower($status) === 'active' || $status === '1');
    }
@endphp
<span class="badge {{ $isActive ? 'bg-primary' : 'bg-light text-dark' }}">
    {{ $isActive ? 'Active' : 'Inactive' }}
</span>
