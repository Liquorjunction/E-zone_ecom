// profile.js
function updateFileName(input) {
  const fileName = input.files[0]?.name || 'Choose image file';
  document.getElementById('file-name').textContent = fileName;

  // Clear avatar error if a file is selected
  if (input.files[0]) {
    clearFieldError('avatar');
  }
}

// Validation functions
function showFieldError(fieldId, message) {
  const errorElement = document.querySelector(`[data-error-for="${fieldId}"]`);
  const inputElement = document.getElementById(fieldId);

  if (errorElement) {
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
  }

  if (inputElement) {
    inputElement.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
    inputElement.classList.remove('border-gray-200', 'focus:border-primary', 'focus:ring-primary/10');
  }
}

function clearFieldError(fieldId) {
  const errorElement = document.querySelector(`[data-error-for="${fieldId}"]`);
  const inputElement = document.getElementById(fieldId);

  if (errorElement) {
    errorElement.textContent = '';
    errorElement.classList.add('hidden');
  }

  if (inputElement) {
    inputElement.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
    inputElement.classList.add('border-gray-200', 'focus:border-primary', 'focus:ring-primary/10');
  }
}

function clearAllErrors() {
  document.querySelectorAll('.error-message').forEach(el => {
    el.textContent = '';
    el.classList.add('hidden');
  });

  document.querySelectorAll('input').forEach(input => {
    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
    input.classList.add('border-gray-200');
  });

  // Remove old field-error elements (for server-side errors)
  document.querySelectorAll('.field-error').forEach(el => el.remove());
}

function validateForm() {
  clearAllErrors();
  let isValid = true;

  // Validate name
  const nameInput = document.getElementById('name');
  const nameValue = nameInput.value.trim();

  if (!nameValue) {
    showFieldError('name', trans('Name is required.'));
    isValid = false;
  } else if (nameValue.length < 2) {
    showFieldError('name', trans('Name must be at least 2 characters long.'));
    isValid = false;
  } else if (nameValue.length > 255) {
    showFieldError('name', trans('Name must not exceed 255 characters.'));
    isValid = false;
  } else if (!/^[a-zA-Z\s'-]+$/.test(nameValue)) {
    showFieldError('name', trans('Name can only contain letters, spaces, hyphens, and apostrophes.'));
    isValid = false;
  }

  // Validate phone
  const phoneInput = document.getElementById('phone');
  const phoneValue = phoneInput.value.trim();

  if (!phoneValue) {
    showFieldError('phone', trans('Phone number is required.'));
    isValid = false;
  } else if (!/^[\d\s\-\+\(\)]+$/.test(phoneValue)) {
    showFieldError('phone', trans('Please enter a valid phone number.'));
    isValid = false;
  } else if (phoneValue.replace(/\D/g, '').length < 10) {
    showFieldError('phone', trans('Phone number must be at least 10 digits.'));
    isValid = false;
  }

  // Validate avatar (if file is selected)
  const avatarInput = document.getElementById('avatar');
  if (avatarInput.files.length > 0) {
    const file = avatarInput.files[0];
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (!allowedTypes.includes(file.type)) {
      showFieldError('avatar', trans('Avatar must be a JPG, PNG, or GIF image.'));
      isValid = false;
    } else if (file.size > maxSize) {
      showFieldError('avatar', trans('Avatar file size must not exceed 5MB.'));
      isValid = false;
    }
  }

  return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
  const profileForm = document.getElementById('profileForm');
  const editBtn = document.getElementById('editBtn');
  const actionButtons = document.getElementById('actionButtons');
  const avatarInput = document.getElementById('avatar');
  const avatarLabel = document.getElementById('avatarLabel');

  // Add real-time validation on input blur
  const nameInput = document.getElementById('name');
  const phoneInput = document.getElementById('phone');

  let isEditMode = false;

  // Edit button functionality
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      isEditMode = !isEditMode;

      if (isEditMode) {
        // Enable editing
        if (nameInput) {
          nameInput.removeAttribute('readonly');
          nameInput.classList.remove('bg-gray-50', 'cursor-not-allowed');
          nameInput.classList.add('bg-white');
        }
        if (phoneInput) {
          phoneInput.removeAttribute('readonly');
          phoneInput.classList.remove('bg-gray-50', 'cursor-not-allowed');
          phoneInput.classList.add('bg-white');
        }
        if (avatarInput) {
          avatarInput.removeAttribute('disabled');
        }
        if (avatarLabel) {
          avatarLabel.classList.remove('cursor-not-allowed', 'opacity-60', 'bg-gray-50');
          avatarLabel.classList.add('cursor-pointer', 'hover:border-primary', 'hover:bg-primary/5');
        }

        // Show action buttons
        if (actionButtons) {
          actionButtons.classList.remove('hidden');
          actionButtons.classList.add('flex');
        }

        // Update button text
        editBtn.innerHTML = `
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Cancel
        `;
      } else {
        // Disable editing (cancel)
        window.location.reload();
      }
    });
  }

  if (nameInput) {
    nameInput.addEventListener('blur', function() {
      const value = this.value.trim();
      clearFieldError('name');

      if (!value) {
        showFieldError('name', trans('Name is required.'));
      } else if (value.length < 2) {
        showFieldError('name', trans('Name must be at least 2 characters long.'));
      } else if (value.length > 255) {
        showFieldError('name', trans('Name must not exceed 255 characters.'));
      } else if (!/^[a-zA-Z\s'-]+$/.test(value)) {
        showFieldError('name', trans('Name can only contain letters, spaces, hyphens, and apostrophes.'));
      }
    });

    nameInput.addEventListener('input', function() {
      if (this.value.trim()) {
        clearFieldError('name');
      }
    });
  }

  if (phoneInput) {
    phoneInput.addEventListener('blur', function() {
      const value = this.value.trim();
      clearFieldError('phone');

      if (!value) {
        showFieldError('phone', trans('Phone number is required.'));
      } else if (!/^[\d\s\-\+\(\)]+$/.test(value)) {
        showFieldError('phone', trans('Please enter a valid phone number.'));
      } else if (value.replace(/\D/g, '').length < 10) {
        showFieldError('phone', trans('Phone number must be at least 10 digits.'));
      }
    });

    phoneInput.addEventListener('input', function() {
      if (this.value.trim()) {
        clearFieldError('phone');
      }
    });
  }

  if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Validate form before submission
      if (!validateForm()) {
        // Scroll to first error
        const firstError = document.querySelector('.error-message:not(.hidden)');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }

      const form = e.target;
      const url = form.action;
      const formData = new FormData(form);

      // Disable submit button
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

      fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: formData
      })
      .then(async response => {
        if (response.ok) {
          const data = await response.json();
          // Show success message using universal popup
          if (typeof showUniversalPopup === 'function') {
            showUniversalPopup(trans('Profile updated successfully!'), 'success', 2000);
          }

          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          const errorData = await response.json();

          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;

          if (errorData.errors) {
            // Display server-side validation errors
            Object.entries(errorData.errors).forEach(([field, messages]) => {
              showFieldError(field, messages.join(' '));
            });

            // Scroll to first error
            const firstError = document.querySelector('.error-message:not(.hidden)');
            if (firstError) {
              firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          } else if (errorData.message) {
            // Show error message using universal popup
            if (typeof showUniversalPopup === 'function') {
              showUniversalPopup(errorData.message, 'error', 5000);
            }
          }
        }
      })
      .catch((error) => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        // Show error message using universal popup
        if (typeof showUniversalPopup === 'function') {
          showUniversalPopup(trans('An error occurred. Please try again.'), 'error', 5000);
        }
      });
    });
  }
});
