// Loader overlay helpers (must be at top so available before use)
function showAddressLoader() {
  let loader = document.getElementById('address-loader-overlay');
  if (!loader) {
    loader = document.createElement('div');
    loader.id = 'address-loader-overlay';
    loader.style.position = 'fixed';
    loader.style.top = 0;
    loader.style.left = 0;
    loader.style.width = '100vw';
    loader.style.height = '100vh';
    loader.style.background = 'rgba(255,255,255,0.6)';
    loader.style.zIndex = 100000;
    loader.style.display = 'flex';
    loader.style.alignItems = 'center';
    loader.style.justifyContent = 'center';
    loader.innerHTML = '<div class="animate-spin rounded-full h-16 w-16 border-t-4 border-primary border-solid"></div>';
    document.body.appendChild(loader);
  }
  loader.style.display = 'flex';
}
function hideAddressLoader() {
  const loader = document.getElementById('address-loader-overlay');
  if (loader) loader.style.display = 'none';
}

// Debounce helper
function debounce(fn, delay) {
  let timer = null;
  return function(...args) {
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}

function ensureCityOption(selectEl, cityValue){
  if(!selectEl || !cityValue) return;

  const wanted = String(cityValue);
  const existing = Array.from(selectEl.options).find((opt) => opt.value === wanted);
  if(existing) return;

  const customOption = document.createElement('option');
  customOption.value = wanted;
  customOption.textContent = wanted;
  selectEl.appendChild(customOption);
}

function attachCityCountrySync(citySelectId, countryInputId){
  const citySelect = document.getElementById(citySelectId);
  const countryInput = document.getElementById(countryInputId);
  if(!citySelect || !countryInput) return;

  citySelect.addEventListener('change', () => {
    const selected = citySelect.options[citySelect.selectedIndex];
    if(!selected) return;

    const country = selected.getAttribute('data-country') || '';
    if(country){
      countryInput.value = country;
    }
  });
}

// 1. Move Modal to Body on Load (Fixes stacking context "under header" issue)
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addr-unique-modal');
    const mobileForm = document.getElementById('mobile-address-form');
    const mobileFab = document.querySelector('button[onclick="openAddModal()"].fixed');
    if(modal) document.body.appendChild(modal);
    if(mobileForm) document.body.appendChild(mobileForm);
    if(mobileFab) document.body.appendChild(mobileFab);

    attachCityCountrySync('field-city', 'field-country');
    attachCityCountrySync('mobile-field-city', 'mobile-field-country');

    // Allow closing overlays with Escape for better accessibility.
    document.addEventListener('keydown', (e) => {
      if(e.key !== 'Escape') return;
      const desktopOpen = modal && !modal.classList.contains('hidden');
      const mobileOpen = mobileForm && !mobileForm.classList.contains('translate-y-full');
      if(desktopOpen) closeModal();
      if(mobileOpen) closeMobileForm();
    });
});

const REQUIRED_FIELDS = ['type', 'name', 'street', 'city_id', 'country'];

// Helper to get CSRF token
function getCsrfToken(){
  const t = document.querySelector('input[name="_token"]');
  return t ? t.value : '';
}

function getErrorBox(isMobile){
  return document.getElementById(isMobile ? 'mobile-address-form-errors' : 'address-form-errors');
}

function setFieldError(input, message){
  if(!input) return;

  input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-200');
  input.setAttribute('aria-invalid', 'true');

  let errorEl = input.parentElement.querySelector('.js-field-error');
  if(!errorEl){
    errorEl = document.createElement('p');
    errorEl.className = 'js-field-error mt-1.5 text-xs font-medium text-red-600';
    input.parentElement.appendChild(errorEl);
  }
  errorEl.textContent = message;
}

function clearFieldError(input){
  if(!input) return;

  input.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-200');
  input.removeAttribute('aria-invalid');
  const errorEl = input.parentElement.querySelector('.js-field-error');
  if(errorEl) errorEl.remove();
}

function clearFormErrors(form, isMobile){
  const box = getErrorBox(isMobile);
  if(box){
    box.classList.add('hidden');
    box.textContent = '';
  }

  form.querySelectorAll('input[name], select[name], textarea[name]').forEach((el) => {
    clearFieldError(el);
  });
}

function renderValidationErrors(form, errors, isMobile){
  clearFormErrors(form, isMobile);

  const box = getErrorBox(isMobile);
  if(box){
    box.classList.remove('hidden');

    const allMessages = [];
    Object.keys(errors || {}).forEach((field) => {
      const msgs = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
      msgs.forEach((msg) => allMessages.push(String(msg)));
    });

    if(allMessages.length > 0){
      box.innerHTML = `<strong class="block mb-1">${trans('Please fix the following:')}</strong><ul class="list-disc pl-5 space-y-0.5">${allMessages.map((m) => `<li>${m}</li>`).join('')}</ul>`;
    }
  }

  Object.keys(errors || {}).forEach((field) => {
    const input = form.querySelector(`[name="${field}"]`);
    const msgs = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
    if(input && msgs.length > 0){
      setFieldError(input, String(msgs[0]));
    }
  });
}

function validateAddressForm(form, isMobile){
  const errors = {};

  REQUIRED_FIELDS.forEach((field) => {
    const input = form.querySelector(`[name="${field}"]`);
    const value = input ? String(input.value || '').trim() : '';
    if(!value){
      errors[field] = [trans('This field is required.')];
    }
  });

  const postal = form.querySelector('[name="postal_code"]');
  if(postal && String(postal.value || '').trim().length > 0 && String(postal.value || '').trim().length < 3){
    errors.postal_code = [trans('Invalid postal code.')];
  }

  if(Object.keys(errors).length > 0){
    renderValidationErrors(form, errors, isMobile);
    const firstInvalid = form.querySelector('[aria-invalid="true"]');
    if(firstInvalid) firstInvalid.focus();
    return false;
  }

  clearFormErrors(form, isMobile);
  return true;
}

function bindInlineValidation(form, isMobile){
  if(!form) return;

  form.addEventListener('input', (e) => {
    if(!e.target || !e.target.name) return;
    clearFieldError(e.target);
  });

  form.addEventListener('change', (e) => {
    if(!e.target || !e.target.name) return;
    clearFieldError(e.target);
  });

  const box = getErrorBox(isMobile);
  if(box){
    box.classList.add('hidden');
  }
}

// Set an address as default via AJAX
async function setDefault(id){
  showUniversalPopup(trans('Set this address as your default?'), 'success', 0, {
    confirmation: true,
    confirmText: 'Yes',
    cancelText: 'No',
    onConfirm: async () => {
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
          showUniversalPopup(json.message || trans('Failed to set default address'), 'error', 3000);
          return;
        }

        const newDefaultId = json.data?.id || id;

        // Clear previous default badge/button
        const prevBadge = document.querySelector('.address-card [data-default="1"]');
        if(prevBadge){
          const prevCard = prevBadge.closest('.address-card');
          if(prevCard){
            // replace badge with button
            prevBadge.outerHTML = `<button onclick="setDefault(${prevCard.dataset.id})" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-primary hover:bg-primary/10 rounded-full transition-colors duration-200">${trans('Set Default')}</button>`;
            try{ let obj = JSON.parse(prevCard.dataset.address || '{}'); obj.is_default = false; prevCard.dataset.address = JSON.stringify(obj); } catch(e){}
          }
        }

        // Set new default badge
        const newCard = document.getElementById('address-card-' + newDefaultId);
        if(newCard){
          const btn = newCard.querySelector('button[onclick^="setDefault"]');
          if(btn) btn.outerHTML = `<span data-default="1" class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/15 rounded-full flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>${trans('Default')}</span>`;
          try{ let obj = JSON.parse(newCard.dataset.address || '{}'); obj.is_default = true; newCard.dataset.address = JSON.stringify(obj); } catch(e){}
        }
        showUniversalPopup((trans('Address set as default')), 'success', 2000);
      }catch(e){
        console.error(e);
        showUniversalPopup(trans('Failed to set default address'), 'error', 3000);
      }
    }
  });
}

// Open Add Modal
function openAddModal(){
  if(window.innerWidth < 640) {
    openMobileAddForm();
  } else {
    // Desktop
    document.getElementById('addr-modal-title').textContent = trans('Add Address');
    document.getElementById('address-form').action = '/client/addresses';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('address-id').value = '';

    ['type','address_label','name','phone','street','city','postal_code','country'].forEach(function(k){
      const el = document.getElementById('field-'+k.replace('_','-')) || document.getElementById('field-'+k);
      if(el) el.value = '';
    });
    // sensible default
    const defaultType = document.getElementById('field-type');
    if (defaultType) defaultType.value = 'shipping';

    clearFormErrors(document.getElementById('address-form'), false);

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

    document.body.style.overflow = 'hidden';
  }
}

// Mobile Add Form
function openMobileAddForm(){
  document.getElementById('mobile-form-title').textContent = 'Add Address';
  document.getElementById('mobile-address-form-element').action = '/client/addresses';
  document.getElementById('mobile-form-method').value = 'POST';
  document.getElementById('mobile-address-id').value = '';

  ['type','address_label','name','phone','street','city','postal_code','country'].forEach(function(k){
    const el = document.getElementById('mobile-field-'+k.replace('_','-')) || document.getElementById('mobile-field-'+k);
    if(el) el.value = '';
  });
  const mobileDefaultType = document.getElementById('mobile-field-type');
  if (mobileDefaultType) mobileDefaultType.value = 'shipping';

  clearFormErrors(document.getElementById('mobile-address-form-element'), true);

  const form = document.getElementById('mobile-address-form');
  form.classList.remove('translate-y-full');
  document.body.style.overflow = 'hidden';
}

// Close Mobile Form
function closeMobileForm(){
  const form = document.getElementById('mobile-address-form');
  form.classList.add('translate-y-full');
  clearFormErrors(document.getElementById('mobile-address-form-element'), true);
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
            console.warn((trans('Could not parse address JSON payload; proceeding with id-only fallback.')), e3);
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
    if (typeof showUniversalPopup === 'function') {
      showUniversalPopup(trans('Address id missing — cannot edit this address.'), 'error', 3000);
    } else {
      alert(trans('Address id missing — cannot edit this address.'));
    }
    return;
  }

  document.getElementById('addr-modal-title').textContent = trans('Edit Address');
  document.getElementById('address-form').action = '/client/addresses/update';
  document.getElementById('form-method').value = 'POST';
  document.getElementById('address-id').value = data.id || '';

  const setVal = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; };

  setVal('field-type', data.type);
  setVal('field-address_label', data.address_label);
  setVal('field-name', data.name);
  setVal('field-phone', data.phone);
  setVal('field-street', data.street);
  ensureCityOption(document.getElementById('field-city'), data.city_id);
  setVal('field-city', data.city_id);
  setVal('field-postal_code', data.postal_code);
  setVal('field-country', data.country);

  clearFormErrors(document.getElementById('address-form'), false);

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

    document.body.style.overflow = 'hidden';
}

// Mobile Edit Form
function openMobileEditForm(data){
  document.getElementById('mobile-form-title').textContent = trans('Edit Address');
  document.getElementById('mobile-address-form-element').action = '/client/addresses/update';
  document.getElementById('mobile-form-method').value = 'POST';
  document.getElementById('mobile-address-id').value = data.id || '';

  const setVal = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; };

  setVal('mobile-field-type', data.type);
  setVal('mobile-field-address_label', data.address_label);
  setVal('mobile-field-name', data.name);
  setVal('mobile-field-phone', data.phone);
  setVal('mobile-field-street', data.street);
  ensureCityOption(document.getElementById('mobile-field-city'), data.city_id);
  setVal('mobile-field-city', data.city_id);
  setVal('mobile-field-postal_code', data.postal_code);
  setVal('mobile-field-country', data.country);

  clearFormErrors(document.getElementById('mobile-address-form-element'), true);

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
    clearFormErrors(document.getElementById('address-form'), false);
    document.body.style.overflow = '';
  }, 300);
}

// Unified AJAX Submit Handler
async function handleFormSubmit(e) {
  e.preventDefault();
  showAddressLoader();
  const form = e.target;
  const isMobile = form.id === 'mobile-address-form-element';
  const url = form.action;
  const method = (form.querySelector('input[name="_method"]').value || 'POST').toUpperCase();
  const token = getCsrfToken();
  const fd = new FormData(form);
  const submitBtn = isMobile ? document.getElementById('mobile-form-submit-btn') : document.getElementById('form-submit');
  const originalBtnHtml = submitBtn.innerHTML;

  if(!validateAddressForm(form, isMobile)){
    return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ' + trans('Saving...');

  try{
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
      if(json && json.errors){
        renderValidationErrors(form, json.errors, isMobile);
      }
      showUniversalPopup(json.message || trans('Failed to save address'), 'error', 3000);
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnHtml;
      hideAddressLoader();
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
        let section = null;
        if (data.type === 'shipping') {
          section = grid.querySelector('section:nth-of-type(1) .grid');
        } else if (data.type === 'billing') {
          section = grid.querySelector('section:nth-of-type(2) .grid');
        }
        if (section) {
          const emptyMsg = section.querySelector('.col-span-full');
          if(emptyMsg) emptyMsg.remove();
          section.appendChild(newCard);
        } else {
          grid.appendChild(newCard);
        }
      }
    }

    if(isMobile) closeMobileForm();
    else closeModal();

    clearFormErrors(form, isMobile);
    showUniversalPopup(trans('Address saved successfully!'), 'success', 2000);

  }catch(err){
    console.error(err);
    if (typeof showUniversalPopup === 'function') {
      showUniversalPopup(trans('An error occurred while saving the address.'), 'error', 3000);
    } else {
      alert(trans('An error occurred while saving the address.'));
    }
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnHtml;
    hideAddressLoader();
  }
}

document.getElementById('address-form').addEventListener('submit', handleFormSubmit);
document.getElementById('mobile-address-form-element').addEventListener('submit', handleFormSubmit);
bindInlineValidation(document.getElementById('address-form'), false);
bindInlineValidation(document.getElementById('mobile-address-form-element'), true);

// Delete Address via AJAX
async function deleteAddress(id){
  showUniversalPopup(trans('Are you sure you want to delete this address?'), 'error', 0, {
    confirmation: true,
    confirmText: trans('Delete'),
    cancelText: trans('Cancel'),
    onConfirm: async () => {
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
          showUniversalPopup(json.message || trans('Failed to delete address'), 'error', 3000);
          return;
        }
        const el = document.getElementById('address-card-' + id);
        if(el) {
          el.style.opacity = '0';
          el.style.transform = 'scale(0.95)';
          setTimeout(() => el.remove(), 300);
        }
        showUniversalPopup(trans('Address deleted successfully'), 'success', 2000);
      }catch(e){
        console.error(e);
        showUniversalPopup(trans('Failed to delete address'), 'error', 3000);
      }
    }
  });
}

// Close modal on backdrop click
document.getElementById('addr-unique-modal').addEventListener('click', function(e){
  if(e.target === this) closeModal();
});
