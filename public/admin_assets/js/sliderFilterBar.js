document.addEventListener("DOMContentLoaded", function () {
	function el(id) {
		return document.getElementById(id);
	}

	function getSelectedStatus() {
		return document.querySelector("input[name='status']:checked")?.value || "all";
	}

	function hasActiveFilters() {
		return getSelectedStatus() !== "all";
	}

	function reloadTable() {
		if (window.sliderDataTable) {
			window.sliderDataTable.ajax.reload();
		}
	}

	var filterPanel = el("filter-panel");
	var filterForm = el("slider-filter-bar");
	var resetBtn = el("reset-filter-btn");

	if (filterPanel && hasActiveFilters()) {
		new bootstrap.Collapse(filterPanel, { toggle: false }).show();
	}

	if (filterForm) {
		filterForm.addEventListener("submit", function (e) {
			if (window.sliderDataTable) {
				e.preventDefault();
				reloadTable();
			}
		});

		filterForm.addEventListener("keydown", function (e) {
			if (e.key === "Enter" && window.sliderDataTable) {
				e.preventDefault();
				reloadTable();
			}
		});
	}

	if (resetBtn) {
		resetBtn.addEventListener("click", function () {
			var statusAll = el("status_all");
			if (statusAll) {
				statusAll.checked = true;
			}

			if (window.sliderDataTable) {
				reloadTable();
				return;
			}

			window.location.href = window.location.pathname;
		});
	}
});
