// profile.js
function updateFileName(input) {
  const fileName = input.files[0]?.name || 'Choose image file';
  document.getElementById('file-name').textContent = fileName;
}

document.addEventListener('DOMContentLoaded', function() {
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const form = e.target;
      const url = form.action;
      const formData = new FormData(form);
      // Remove previous errors
      document.querySelectorAll('.field-error').forEach(el => el.remove());

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
          alert('Profile updated successfully!');
          window.location.reload();
        } else {
          const errorData = await response.json();
          if (errorData.errors) {
            Object.entries(errorData.errors).forEach(([field, messages]) => {
              const input = document.getElementById(field);
              if (input) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error text-xs text-red-500 mt-1';
                errorDiv.textContent = messages.join(' ');
                input.parentNode.appendChild(errorDiv);
              }
            });
          } else if (errorData.message) {
            alert(errorData.message);
          }
        }
      })
      .catch(() => {
        alert('An error occurred. Please try again.');
      });
    });
  }
});
