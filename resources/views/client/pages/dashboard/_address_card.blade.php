@php
  $id = $address->id ?? ($address['id'] ?? null);
@endphp
<div id="address-card-{{ $id }}" class="address-card group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary/30" data-address="{{ json_encode($address) }}" data-id="{{ $id }}">
  <input type="hidden" class="address-card-input-id" value="{{ $id }}">
  <div class="bg-linear-to-r from-primary/5 to-primary/10 px-6 py-4 border-b border-gray-100">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="bg-white p-2 rounded-lg shadow-sm">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900">{{ $address->address_label ?? ($address->type ?? 'Address') }}</h3>
      </div>
      @if(isset($address->is_default) && $address->is_default)
        <span data-default="1" class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/15 rounded-full flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Default
        </span>
      @else
        <button onclick="setDefault({{ $id }})" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-primary hover:bg-primary/10 rounded-full transition-colors duration-200">Set Default</button>
      @endif
    </div>
  </div>

  <div class="px-6 py-5">
    <div class="space-y-3">
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
        <p class="font-semibold text-gray-900">{{ $address->name }}</p>
      </div>
      @if(!empty($address->phone))
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
        <p class="text-gray-700 text-sm">{{ $address->phone }}</p>
      </div>
      @endif
      <div class="flex items-start gap-2">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        <p class="text-gray-700 text-sm leading-relaxed">{!! nl2br(e($address->street)) !!}<br>{{ trim(collect([$address->city, $address->state])->filter()->implode(', ') . ' ' . ($address->postal_code ?? '')) }}<br>{{ $address->country }}</p>
      </div>
    </div>
  </div>

  <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
    <button type="button" onclick="openEditModalFromElem(this)" class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
      </svg>
      Edit
    </button>
    <button type="button" onclick="deleteAddress({{ $id }})" class="flex items-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
      </svg>
      Delete
    </button>
  </div>
</div>
