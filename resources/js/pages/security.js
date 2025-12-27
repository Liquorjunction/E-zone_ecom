// JS for dashboard security page
document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Toggle password visibility
  document.querySelectorAll('.js-toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      // simple aria toggle
      btn.setAttribute('aria-pressed', input.type === 'text');
    });
  });

  // Live password rule validation
  const newPwdInput = document.getElementById('new_password');
  const pwdRules = {
    length: pw => pw.length >= 8,
    uppercase: pw => /[A-Z]/.test(pw),
    number: pw => /[0-9]/.test(pw),
    special: pw => /[!@#$%^&*()\-_=+\[\]{};:'"\\|,.<>\/\?`~]/.test(pw),
  };

  function updatePasswordRuleUI(pw) {
    Object.keys(pwdRules).forEach(rule => {
      const el = document.querySelector('[data-rule="' + rule + '"]');
      if (!el) return;
      const icon = el.querySelector('.js-pwd-icon');
      const passed = pwdRules[rule](pw);
      if (passed) {
        icon.classList.remove('text-gray-300');
        icon.classList.add('text-green-500');
        el.classList.remove('text-gray-600');
        el.classList.add('text-green-600');
      } else {
        icon.classList.remove('text-green-500');
        icon.classList.add('text-gray-300');
        el.classList.remove('text-green-600');
        el.classList.add('text-gray-600');
      }
    });
  }

  if (newPwdInput) {
    newPwdInput.addEventListener('input', (e) => {
      updatePasswordRuleUI(e.target.value || '');
      checkConfirmMatch();
    });
    // initialize on load
    updatePasswordRuleUI(newPwdInput.value || '');
  }

  // Confirm password matching
  const confirmInput = document.getElementById('confirm_password');
  const confirmMsg = document.querySelector('.js-confirm-msg');

  function checkConfirmMatch() {
    if (!newPwdInput || !confirmInput || !confirmMsg) return;
    const a = newPwdInput.value || '';
    const b = confirmInput.value || '';
    if (b.length === 0) {
      confirmMsg.textContent = '';
      confirmInput.classList.remove('border-green-500');
      confirmInput.classList.remove('border-red-500');
      return false;
    }
    if (a === b) {
      confirmMsg.textContent = 'Passwords match';
      confirmMsg.classList.remove('text-red-500');
      confirmMsg.classList.add('text-green-600');
      confirmInput.classList.remove('border-red-500');
      confirmInput.classList.add('border-green-500');
      return true;
    }
    confirmMsg.textContent = 'Passwords do not match';
    confirmMsg.classList.remove('text-green-600');
    confirmMsg.classList.add('text-red-500');
    confirmInput.classList.remove('border-green-500');
    confirmInput.classList.add('border-red-500');
    return false;
  }

  if (confirmInput) {
    confirmInput.addEventListener('input', () => checkConfirmMatch());
  }

  // Strength bar update inside updatePasswordRuleUI
  const strengthBar = document.getElementById('pwd-strength-bar');
  const strengthText = document.getElementById('pwd-strength-text');
  const strengthContainer = document.getElementById('pwd-strength');
  const totalRules = Object.keys(pwdRules).length;

  const originalUpdate = updatePasswordRuleUI;
  function enhancedUpdate(pw) {
    originalUpdate(pw);
    let passed = 0;
    Object.keys(pwdRules).forEach(r => { if (pwdRules[r](pw)) passed++; });
    const percent = Math.round((passed / totalRules) * 100);
    if (strengthBar) {
      strengthBar.style.width = percent + '%';
      strengthBar.classList.remove('bg-red-500','bg-yellow-400','bg-green-500');
      if (percent < 50) strengthBar.classList.add('bg-red-500');
      else if (percent < 80) strengthBar.classList.add('bg-yellow-400');
      else strengthBar.classList.add('bg-green-500');
    }
    if (strengthText) {
      let label = 'Very weak';
      if (percent < 50) label = 'Weak';
      else if (percent < 80) label = 'Moderate';
      else label = 'Strong';
      strengthText.textContent = label;
      strengthText.classList.remove('text-red-500','text-yellow-600','text-green-600');
      if (percent < 50) strengthText.classList.add('text-red-500');
      else if (percent < 80) strengthText.classList.add('text-yellow-600');
      else strengthText.classList.add('text-green-600');
    }
    // show/hide strength container only when there is input
    if (strengthContainer) {
      if ((pw || '').length > 0) strengthContainer.classList.remove('hidden');
      else strengthContainer.classList.add('hidden');
    }

    return passed === totalRules;
  }
  // replace calls
  updatePasswordRuleUI = enhancedUpdate;

  // Helper to show validation errors under inputs
  function clearErrors(form) {
    form.querySelectorAll('.js-field-error').forEach(el => el.remove());
  }

  function showErrors(form, errors) {
    clearErrors(form);
    Object.keys(errors).forEach(field => {
      const el = form.querySelector('[name="' + field + '"]');
      const msg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
      if (el) {
        const p = document.createElement('p');
        p.className = 'js-field-error text-red-500 text-xs mt-1';
        p.textContent = msg;
        el.parentNode.appendChild(p);
      }
    });
  }

  // Password update via AJAX
  const pwdForm = document.querySelector('form[action$="security/password"]');
  if (pwdForm) {
    pwdForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors(pwdForm);
      // Front-end validation: ensure all rules pass and confirmation matches
      const submitBtn = document.getElementById('pwd-submit');
      const rulesOk = newPwdInput ? updatePasswordRuleUI(newPwdInput.value || '') : true;
      const matchOk = checkConfirmMatch ? checkConfirmMatch() : true;
      if (!rulesOk || !matchOk) {
        alert('Please satisfy all password requirements and ensure confirmation matches.');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add('opacity-50','cursor-not-allowed');
        }
        return;
      }

      const formData = new FormData(pwdForm);
      try {
        const res = await fetch(pwdForm.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData,
          credentials: 'same-origin'
        });

        if (res.status === 422) {
          const json = await res.json();
          showErrors(pwdForm, json.errors || json);
          return;
        }

        if (!res.ok) throw new Error('Request failed');

        // success
        const data = await res.json().catch(() => ({}));
        const msg = data.message || 'Password updated successfully.';
        alert(msg);
        pwdForm.reset();
        // reset UI state
        if (newPwdInput) updatePasswordRuleUI('');
        if (confirmInput) { confirmInput.value = ''; checkConfirmMatch(); }
        if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('opacity-50','cursor-not-allowed'); }
      } catch (err) {
        console.error(err);
        alert('Unable to update password. Please try again.');
      }
    });
  }

  // Enable/disable submit button based on validation state
  const submitBtn = document.getElementById('pwd-submit');
  function updateSubmitState() {
    if (!submitBtn) return;
    const rulesOk = newPwdInput ? updatePasswordRuleUI(newPwdInput.value || '') : true;
    const matchOk = checkConfirmMatch ? checkConfirmMatch() : true;
    if (rulesOk && matchOk) {
      submitBtn.disabled = false;
      submitBtn.classList.remove('opacity-50','cursor-not-allowed');
    } else {
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-50','cursor-not-allowed');
    }
  }

  if (newPwdInput) newPwdInput.addEventListener('input', updateSubmitState);
  if (confirmInput) confirmInput.addEventListener('input', updateSubmitState);
  // initialize button state
  updateSubmitState();

  // Revoke session via AJAX
  document.querySelectorAll('form[action$="revoke-session"]').forEach(frm => {
    frm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const row = frm.closest('div[class*="rounded-xl"]');
      const fd = new FormData(frm);
      try {
        const res = await fetch(frm.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: fd,
          credentials: 'same-origin'
        });

        if (res.status === 422) {
          const json = await res.json();
          alert(Object.values(json.errors || {}).flat().join('\n'));
          return;
        }

        if (!res.ok) throw new Error('Request failed');

        // remove the session row or mark revoked
        if (row) {
          row.remove();
        }
      } catch (err) {
        console.error(err);
        alert('Unable to revoke session.');
      }
    });
  });

});
