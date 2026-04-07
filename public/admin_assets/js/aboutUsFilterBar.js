document.addEventListener("DOMContentLoaded", function () {
	function el(id) {
		return document.getElementById(id);
	}

	var filterPanel = el("filter-panel");
	var filterForm = el("about-us-filter-bar");
	var resetBtn = el("reset-filters-btn");

	function getSelectedStatus() {
		return document.querySelector("input[name='status']:checked")?.value || "all";
	}

	function hasActiveFilters() {
		var dateFrom = filterForm?.querySelector("input[name='date_from']")?.value;
		var dateTo = filterForm?.querySelector("input[name='date_to']")?.value;
		return getSelectedStatus() !== "all" || !!dateFrom || !!dateTo;
	}

	function reloadTable() {
		if (window.aboutUsDataTable) {
			window.aboutUsDataTable.ajax.reload();
		}
	}

	if (filterPanel && hasActiveFilters()) {
		new bootstrap.Collapse(filterPanel, { toggle: false }).show();
	}

	if (filterForm) {
		filterForm.addEventListener("submit", function (e) {
			if (window.aboutUsDataTable) {
				e.preventDefault();
				reloadTable();
			}
		});

		filterForm.addEventListener("keydown", function (e) {
			if (e.key === "Enter" && window.aboutUsDataTable) {
				e.preventDefault();
				reloadTable();
			}
		});
	}

	if (resetBtn) {
		resetBtn.addEventListener("click", function () {
			var statusAll = el("status_all");
			var dateFrom = filterForm?.querySelector("input[name='date_from']");
			var dateTo = filterForm?.querySelector("input[name='date_to']");
			if (statusAll) {
				statusAll.checked = true;
			}
			if (dateFrom) {
				dateFrom.value = "";
			}
			if (dateTo) {
				dateTo.value = "";
			}

			if (window.aboutUsDataTable) {
				reloadTable();
				return;
			}

			window.location.href = window.location.pathname;
		});
	}
});
