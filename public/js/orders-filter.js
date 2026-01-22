document.addEventListener('DOMContentLoaded', function() {
  // AJAX for filter tabs
  document.querySelectorAll('.hidden.lg\\:flex a').forEach(function(el) {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      fetchOrders(el.getAttribute('href'));
    });
  });
  // AJAX for mobile dropdown
  var select = document.querySelector('#orders-filter-form select');
  if (select) {
    select.addEventListener('change', function() {
      var url = select.form.action + (select.value ? ('?status=' + encodeURIComponent(select.value)) : '');
      fetchOrders(url);
    });
  }
  function fetchOrders(url) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.json())
      .then(data => {
        document.getElementById('orders-list-container').innerHTML = data.html;
        // Update tab and dropdown active state
        var params = new URL(url, window.location.origin).searchParams;
        var status = params.get('status') || '';
        // Update tab classes
        document.querySelectorAll('.hidden.lg\\:flex a').forEach(function(tab) {
          var tabStatus = '';
          var href = tab.getAttribute('href');
          if (href) {
            var tabParams = new URL(href, window.location.origin).searchParams;
            tabStatus = tabParams.get('status') || '';
          }
          if (tabStatus === status) {
            tab.classList.add('text-white', 'bg-primary');
            tab.classList.remove('text-gray-600', 'hover:text-primary', 'hover:bg-primary/5');
          } else {
            tab.classList.remove('text-white', 'bg-primary');
            tab.classList.add('text-gray-600', 'hover:text-primary', 'hover:bg-primary/5');
          }
        });
        // Update dropdown
        var select = document.querySelector('#orders-filter-form select');
        if (select) {
          select.value = status;
        }
      });
  }
});
