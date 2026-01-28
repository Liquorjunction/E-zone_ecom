document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('orders-filter-form');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        fetch(window.location.pathname + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            // Replace only the orders list container
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newList = doc.querySelector('#orders-list-container');
            if (newList) {
                document.getElementById('orders-list-container').innerHTML = newList.innerHTML;
            } else {
                // fallback: replace whole body
                document.body.innerHTML = html;
            }
        });
    });
    // Optional: auto-submit on change for filters
    form.querySelectorAll('select,input[type=date]').forEach(el => {
        el.addEventListener('change', () => form.requestSubmit());
    });
});
