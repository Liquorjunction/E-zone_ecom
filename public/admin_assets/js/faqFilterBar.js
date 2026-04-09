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
        var categorySelect = document.getElementById('faq_category_id');

        if (!filterForm || !categorySelect) {
            return;
        }

        var applyButton = filterForm.querySelector("button[type='submit']");
        var resetButton = document.getElementById('reset-filters-btn');

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

        if (applyButton) {
            applyButton.addEventListener('click', function (event) {
                event.preventDefault();
                reloadTable();
            });
        }

        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            reloadTable();
        });

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

        categorySelect.addEventListener('change', function () {
            reloadTable();
        });
    });
})();
