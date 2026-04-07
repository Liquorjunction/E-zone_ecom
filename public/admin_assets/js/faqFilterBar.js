(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }

        callback();
    }

    onReady(function () {
        var filterForm = document.getElementById('faq-filter-bar');
        var categorySelect = document.getElementById('faq-category-filter');

        if (!filterForm || !categorySelect) {
            return;
        }

        var applyButton = filterForm.querySelector("button[type='submit']");
        var resetButton = document.getElementById('reset-filters-btn');
        var categoriesUrl = filterForm.getAttribute('data-categories-url');
        var categoriesLoaded = false;
        var loadingCategories = false;

        function getTable() {
            return window.faqsTable || window.faqTable || null;
        }

        function reloadTable() {
            var dataTable = getTable();

            if (!dataTable) {
                return;
            }

            dataTable.ajax.reload();
        }

        function buildCategoryOption(value, label) {
            var option = document.createElement('option');
            option.value = String(value);
            option.textContent = label;
            return option;
        }

        function hasOptionValue(value) {
            return Array.prototype.some.call(categorySelect.options, function (option) {
                return String(option.value) === String(value);
            });
        }

        function loadCategories() {
            if (loadingCategories || !categoriesUrl) {
                return;
            }

            loadingCategories = true;

            var selectedValue = categorySelect.value || categorySelect.getAttribute('data-selected') || '';

            fetch(categoriesUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (payload) {
                    var items = payload && payload.data ? payload.data : {};

                    categorySelect.innerHTML = '';
                    categorySelect.appendChild(buildCategoryOption('', 'All Categories'));

                    Object.keys(items).forEach(function (id) {
                        categorySelect.appendChild(buildCategoryOption(id, items[id]));
                    });

                    if (selectedValue && hasOptionValue(selectedValue)) {
                        categorySelect.value = selectedValue;
                    } else {
                        categorySelect.value = '';
                    }

                    categoriesLoaded = true;
                })
                .catch(function () {
                    categorySelect.innerHTML = '';
                    categorySelect.appendChild(buildCategoryOption('', 'All Categories'));
                    categorySelect.value = '';
                })
                .finally(function () {
                    loadingCategories = false;
                });
        }

        function ensureCategoriesLoaded() {
            if (!categoriesLoaded) {
                loadCategories();
                return;
            }

            loadCategories();
        }

        if (applyButton) {
            applyButton.addEventListener('click', function (event) {
                event.preventDefault();
                reloadTable();
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', function () {
                var allStatusOption = filterForm.querySelector("input[name='status'][value='all']");
                if (allStatusOption) {
                    allStatusOption.checked = true;
                }

                var dateFromInput = filterForm.querySelector("input[name='date_from']");
                var dateToInput = filterForm.querySelector("input[name='date_to']");

                if (dateFromInput) {
                    dateFromInput.value = '';
                }

                if (dateToInput) {
                    dateToInput.value = '';
                }

                categorySelect.value = '';
                reloadTable();
            });
        }

        categorySelect.addEventListener('click', ensureCategoriesLoaded);
        categorySelect.addEventListener('focus', ensureCategoriesLoaded);
    });
})();
