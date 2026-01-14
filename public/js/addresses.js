// 1. Move Modal to Body on Load (Fixes stacking context "under header" issue)
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addr-unique-modal');
    const mobileForm = document.getElementById('mobile-address-form');
    const mobileFab = document.querySelector('button[onclick="openAddModal()"].fixed');
    if(modal) document.body.appendChild(modal);
    if(mobileForm) document.body.appendChild(mobileForm);
    if(mobileFab) document.body.appendChild(mobileFab);
});

// Helper to get CSRF token
function getCsrfToken(){
  const t = document.querySelector('input[name="_token"]');
  return t ? t.value : '';
}

// Set an address as default via AJAX
async function setDefault(id){
  if(!confirm('Set this address as your default?')) return;
  const token = getCsrfToken();
  try{
    const res = await fetch('/client/addresses/default', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: id })
    });
    const json = await res.json();
    if(!res.ok){
      alert(json.message || 'Failed to set default address');
      return;
    }

    const newDefaultId = json.data?.id || id;

    // Clear previous default badge/button
    const prevBadge = document.querySelector('.address-card [data-default="1"]');
    if(prevBadge){
      const prevCard = prevBadge.closest('.address-card');
      if(prevCard){
        // replace badge with button
        prevBadge.outerHTML = `<button onclick="setDefault(${prevCard.dataset.id})" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-primary hover:bg-primary/10 rounded-full transition-colors duration-200">Set Default</button>`;
        try{ let obj = JSON.parse(prevCard.dataset.address || '{}'); obj.is_default = false; prevCard.dataset.address = JSON.stringify(obj); } catch(e){}
      }
    }

    // Set new default badge
    const newCard = document.getElementById('address-card-' + newDefaultId);
    if(newCard){
      const btn = newCard.querySelector('button[onclick^="setDefault"]');
      if(btn) btn.outerHTML = `<span data-default="1" class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/15 rounded-full flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>Default</span>`;
      try{ let obj = JSON.parse(newCard.dataset.address || '{}'); obj.is_default = true; newCard.dataset.address = JSON.stringify(obj); } catch(e){}
    }

  }catch(e){
    console.error(e);
    alert('Failed to set default address');
  }
}

// Open Add Modal
function openAddModal(){
  if(window.innerWidth < 640) {
    openMobileAddForm();
  } else {
    // Desktop
    document.getElementById('addr-modal-title').textContent = 'Add Address';
    document.getElementById('address-form').action = '/client/addresses';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('address-id').value = '';

    ['type','address_label','name','street','city','state','postal_code','country'].forEach(function(k){
      const el = document.getElementById('field-'+k.replace('_','-')) || document.getElementById('field-'+k);
      if(el) el.value = '';
    });
    // sensible default
    const defaultType = document.getElementById('field-type');
    if (defaultType) defaultType.value = 'shipping';

    // Unique IDs
    const modal = document.getElementById('addr-unique-modal');
    const content = document.getElementById('addr-unique-content');

    modal.classList.remove('hidden');
    modal.classList.add('flex'); // Ensure flex is added

    // Double RAF to ensure browser paints the display:flex state before transitioning opacity
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    });
  }
}

// Mobile Add Form
function openMobileAddForm(){
  document.getElementById('mobile-form-title').textContent = 'Add Address';
  document.getElementById('mobile-address-form-element').action = '/client/addresses';
  document.getElementById('mobile-form-method').value = 'POST';
  document.getElementById('mobile-address-id').value = '';

  ['type','address_label','name','street','city','state','postal_code','country'].forEach(function(k){
    const el = document.getElementById('mobile-field-'+k.replace('_','-')) || document.getElementById('mobile-field-'+k);
    if(el) el.value = '';
  });
  const mobileDefaultType = document.getElementById('mobile-field-type');
  if (mobileDefaultType) mobileDefaultType.value = 'shipping';

  const form = document.getElementById('mobile-address-form');
  form.classList.remove('translate-y-full');
  document.body.style.overflow = 'hidden';
}

// Close Mobile Form
function closeMobileForm(){
  const form = document.getElementById('mobile-address-form');
  form.classList.add('translate-y-full');
  document.body.style.overflow = '';
}

// Submit Mobile Form
function submitMobileForm() {
   document.getElementById('mobile-address-form-element').dispatchEvent(new Event('submit', { cancelable: true }));
}

// Open Edit Modal from Element
function openEditModalFromElem(elem){
  const card = elem.closest('.address-card');
    if(!card) return;
    // Prefer simple id extraction first (dataset, hidden input, element id)
    let idCandidate = card.dataset && card.dataset.id ? card.dataset.id : null;
    if(!idCandidate){
      const h = card.querySelector('.address-card-input-id');
      if(h && h.value) idCandidate = h.value;
    }
    if(!idCandidate && card.id){
      const m = card.id.match(/address-card-(\d+)/);
      if(m && m[1]) idCandidate = m[1];
    }

    let data = {};
    if(idCandidate) data.id = idCandidate;

    // Attempt to parse full JSON payload to populate fields, merging but keeping id
    const raw = card.dataset.address || '';
    if(raw){
      try{
        const parsed = JSON.parse(raw);
        if(parsed && typeof parsed === 'object') data = Object.assign({}, parsed, data);
      }catch(e){
        try {
          const replaced = raw.replace(/&quot;/g, '"').replace(/&apos;/g, "'");
          const parsed = JSON.parse(replaced);
          if(parsed && typeof parsed === 'object') data = Object.assign({}, parsed, data);
        } catch(e2) {
          try {
            const parsed = JSON.parse(decodeURIComponent(raw));
            if(parsed && typeof parsed === 'object') data = Object.assign({}, parsed, data);
          } catch(e3) {
            console.warn("Could not parse address JSON payload; proceeding with id-only fallback.", e3);
          }
        }
      }
    }

    if(window.innerWidth < 640) {
      openMobileEditForm(data);
    } else {
      openEditModal(data, card);
    }
}

// Desktop Edit Modal
function openEditModal(data, cardElem){
  // Ensure we have an id, try fallback to card element if provided
  if((!data || !data.id) && cardElem){
    try{
      const hidden = cardElem.querySelector('.address-card-input-id');
      if(hidden && hidden.value) data = data || {}, data.id = hidden.value;
    }catch(e){}
    if((!data || !data.id) && cardElem.id){
      const m = cardElem.id.match(/address-card-(\d+)/);
      if(m && m[1]) data = data || {}, data.id = m[1];
    }
    if((!data || !data.id) && cardElem.dataset && cardElem.dataset.id){
      data = data || {};
      data.id = cardElem.dataset.id;
    }
  }

  if(!data || !data.id){
    alert('Address id missing — cannot edit this address.');
    return;
  }

  document.getElementById('addr-modal-title').textContent = 'Edit Address';
  document.getElementById('address-form').action = '/client/addresses/update';
  document.getElementById('form-method').value = 'POST';
  document.getElementById('address-id').value = data.id || '';

  const setVal = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; };

  setVal('field-type', data.type);
  setVal('field-address_label', data.address_label);
  setVal('field-name', data.name);
  setVal('field-street', data.street);
  setVal('field-city', data.city);
  setVal('field-state', data.state);
  setVal('field-postal_code', data.postal_code);
  setVal('field-country', data.country);

  const modal = document.getElementById('addr-unique-modal');
  const content = document.getElementById('addr-unique-content');
  modal.classList.remove('hidden');
  modal.classList.add('flex');

  requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    });
}

// Mobile Edit Form
function openMobileEditForm(data){
  document.getElementById('mobile-form-title').textContent = 'Edit Address';
  document.getElementById('mobile-address-form-element').action = '/client/addresses/update';
  document.getElementById('mobile-form-method').value = 'POST';
  document.getElementById('mobile-address-id').value = data.id || '';

  const setVal = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; };

  setVal('mobile-field-type', data.type);
  setVal('mobile-field-address_label', data.address_label);
  setVal('mobile-field-name', data.name);
  setVal('mobile-field-street', data.street);
  setVal('mobile-field-city', data.city);
  setVal('mobile-field-state', data.state);
  setVal('mobile-field-postal_code', data.postal_code);
  setVal('mobile-field-country', data.country);

  const form = document.getElementById('mobile-address-form');
  form.classList.remove('translate-y-full');
  document.body.style.overflow = 'hidden';
}

// Close Modal with Animation
function closeModal(){
  const content = document.getElementById('addr-unique-content');
  content.classList.add('scale-95', 'opacity-0');
  content.classList.remove('scale-100', 'opacity-100');
  setTimeout(() => {
    document.getElementById('addr-unique-modal').classList.add('hidden');
    document.getElementById('addr-unique-modal').classList.remove('flex');
  }, 300);
}

// Unified AJAX Submit Handler
async function handleFormSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const isMobile = form.id === 'mobile-address-form-element';
  const url = form.action;
  const method = (form.querySelector('input[name="_method"]').value || 'POST').toUpperCase();
  const token = getCsrfToken();
  const fd = new FormData(form);
  const submitBtn = isMobile ? document.getElementById('mobile-form-submit-btn') : document.getElementById('form-submit');
  const originalBtnHtml = submitBtn.innerHTML;

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';

  try{
    // Always POST form submissions to match AJAX endpoints; `_method` stays in FormData for server-side
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: fd
    });

    const json = await res.json();
    if(!res.ok){
      alert(json.message || 'Failed to save address');
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnHtml;
      return;
    }

    const data = json.data || json;
    const jsonResp = json; // preserve full response

    // Update or add card using server-rendered HTML
    const existing = document.getElementById('address-card-' + data.id);
    const newCardHtml = jsonResp.html || null;
    if(newCardHtml){
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = newCardHtml.trim();
      const newCard = tempDiv.firstElementChild;

      if(existing){
        existing.replaceWith(newCard);
      } else {
        const grid = document.getElementById('addresses-grid');
        const emptyMsg = grid.querySelector('.col-span-full');
        if(emptyMsg) emptyMsg.remove();

        const addBtn = grid.querySelector('button[onclick="openAddModal()"]');
        if (addBtn && addBtn.parentNode === grid) {
          grid.insertBefore(newCard, addBtn);
        } else {
          grid.appendChild(newCard);
        }
      }
    }

    if(isMobile) closeMobileForm();
    else closeModal();

  }catch(err){
    console.error(err);
    alert('An error occurred while saving the address.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnHtml;
  }
}

document.getElementById('address-form').addEventListener('submit', handleFormSubmit);
document.getElementById('mobile-address-form-element').addEventListener('submit', handleFormSubmit);

function renderAddressCardHTML(data){
  const label = data.address_label || (data.type === 'billing' ? 'Billing' : (data.type === 'shipping' ? 'Shipping' : 'Address'));
  const isDefault = data.is_default ?
    `<span data-default="1" class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/15 rounded-full flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>Default</span>` :
    `<button onclick="setDefault(${data.id})" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-primary hover:bg-primary/10 rounded-full transition-colors duration-200">Set Default</button>`;

  const street = (data.street || '').replace(/\n/g, '<br>');
  const safeJson = JSON.stringify(data).replace(/'/g, "&apos;").replace(/"/g, "&quot;");

  return `
    <div id="address-card-${data.id}" class="address-card group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary/30" data-address='${safeJson}' data-id='${data.id}'>
      <input type="hidden" class="address-card-input-id" value="${data.id}">
      <div class="bg-linear-to-r from-primary/5 to-primary/10 px-6 py-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="bg-white p-2 rounded-lg shadow-sm">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">${label}</h3>
          </div>
          ${isDefault}
        </div>
      </div>
      <div class="px-6 py-5">
        <div class="space-y-3">
          <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <p class="font-semibold text-gray-900">${data.name || ''}</p>
          </div>
          <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <p class="text-gray-700 text-sm leading-relaxed">${street}<br>${data.city || ''}, ${data.state || ''} ${data.postal_code || ''}<br>${data.country || ''}</p>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
        <button type="button" onclick="openEditModalFromElem(this)" class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>Edit</button>
        <button type="button" onclick="deleteAddress(${data.id})" class="flex items-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>Delete</button>
      </div>
    </div>
  `;
}

// Delete Address via AJAX
async function deleteAddress(id){
  if(!confirm('Are you sure you want to delete this address?')) return;
  const token = getCsrfToken();
  try{
    const res = await fetch('/client/addresses/delete', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: id })
    });
    const json = await res.json();
    if(!res.ok){
      alert(json.message || 'Failed to delete address');
      return;
    }
    const el = document.getElementById('address-card-' + id);
    if(el) {
      el.style.opacity = '0';
      el.style.transform = 'scale(0.95)';
      setTimeout(() => el.remove(), 300);
    }
  }catch(e){
    console.error(e);
    alert('Failed to delete address');
  }
}

// Close modal on backdrop click
document.getElementById('addr-unique-modal').addEventListener('click', function(e){
  if(e.target === this) closeModal();
});
