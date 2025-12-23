@php
  $id = $address->id ?? ($address['id'] ?? null);
@endphp
<div id="address-card-{{ $id }}" class="address-card group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary/30" data-address="{{ json_encode($address) }}" data-id="{{ $id }}">
  <input type="hidden" class="address-card-input-id" value="{{ $id }}">
  <div class="bg-gradient-to-r from-primary/5 to-primary/10 px-6 py-4 border-b border-gray-100">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="bg-white p-2 rounded-lg shadow-sm">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900">{{ $address->address_label ?? ($address->type ?? 'Address') }}</h3>
      </div>
      @if(isset($address->is_default) && $address->is_default)
        <span data-default="1" class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/15 rounded-full flex items-center gap-1">Default</span>
      @else
        <button onclick="setDefault({{ $id }})" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-primary hover:bg-primary/10 rounded-full transition-colors duration-200">Set Default</button>
      @endif
    </div>
  </div>

  <div class="px-6 py-5">
    <div class="space-y-3">
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
        <p class="font-semibold text-gray-900">{{ $address->name }}</p>
      </div>
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        <p class="text-gray-700 text-sm leading-relaxed">{!! nl2br(e($address->street)) !!}<br>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>{{ $address->country }}</p>
      </div>
    </div>
  </div>

  <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
    <button type="button" onclick="openEditModalFromElem(this)" class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">Edit</button>
    <button type="button" onclick="deleteAddress({{ $id }})" class="flex items-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 transition-colors">Delete</button>
  </div>
</div>
