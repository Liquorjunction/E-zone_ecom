window.openDispatchModal = function(orderId) {
    // Redirect to the dispatch create page for the given order
    if (orderId) {
        window.open('/admin/dispatch/create/' + orderId, '_blank');
    } else {
        window.open('/admin/dispatch/create', '_blank');
    }
};

// Intercept form submit to build items array and send via AJAX
$(document).on('submit', '#dispatchForm', function(e) {
	e.preventDefault();
	var $form = $(this);
	var orderId = $('#order_id').val();
	var dispatchMethod = $('#dispatch_method').val();
	var remarks = $('#remarks').val();
	// Build items array from table rows
	var items = [];
	$('#dispatch-items-table tbody tr').each(function() {
		var $row = $(this);
		var order_item_id = $row.data('order-item-id');
		var inventory_id = $row.find('.inventory-select').val();
		var quantity = $row.find('.dispatch-qty-input').val();
		var dispatch_from_store_id = $('#dispatch_from_store_id').val();
		var dispatch_from_warehouse_id = $('#dispatch_from_warehouse_id').val();
		if (order_item_id && inventory_id && quantity) {
			items.push({
				order_item_id: order_item_id,
				inventory_id: inventory_id,
				quantity: quantity,
				dispatch_from_store_id: dispatch_from_store_id,
				dispatch_from_warehouse_id: dispatch_from_warehouse_id
			});
		}
	});
	if (!items.length) {
		Swal.fire({ icon: 'error', title: 'Error', text: 'Please add at least one item to dispatch.' });
		return;
	}
	var payload = {
		order_id: orderId,
		dispatch_method: dispatchMethod,
		remarks: remarks,
		items: items
	};
	var url = $form.attr('action');
	var csrfToken = $('meta[name="csrf-token"]').attr('content') || window.Laravel && window.Laravel.csrfToken;
	$.ajax({
		url: url,
		type: 'POST',
		data: JSON.stringify(payload),
		contentType: 'application/json',
		headers: {
			'X-CSRF-TOKEN': csrfToken,
			'Accept': 'application/json'
		},
		success: function(data) {
			if (data.success) {
				Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Dispatch created.' });
				$('#dispatchModal').modal('hide');
			} else {
				Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to create dispatch.' });
			}
		},
		error: function(xhr) {
			let msg = 'Failed to create dispatch.';
			if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
			Swal.fire({ icon: 'error', title: 'Error', text: msg });
		}
	});
});

$(document).on('click', '.accept-action', function (e) {
	e.preventDefault();
	var button = $(this);
	var acceptUrl = button.data('accept-url');
	var acceptId = button.data('accept-id');
	var $table = button.closest('table');
	var csrfToken = window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : $('meta[name="csrf-token"]').attr('content');

	if (!acceptUrl) {
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Accept URL not specified.'
		});
		return;
	}

	Swal.fire({
		title: 'Are you sure?',
		text: 'Do you want to accept this item?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#28a745',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, accept it!'
	}).then((result) => {
		if (result.isConfirmed) {
			// Prefer axios if available, else fallback to fetch, else jQuery.ajax
			if (typeof axios !== 'undefined') {
				const formData = new URLSearchParams();
				formData.append('id', acceptId);
				formData.append('_token', csrfToken);

				axios.post(acceptUrl, formData, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					}
				})
				.then(function (response) {
					var data = response.data;
					if (data.success) {
						Swal.fire({
							icon: 'success',
							title: 'Accepted!',
							html: (data.message ? data.message + '<br><br>' : '') + '<button id="open-dispatch-modal-btn" class="swal2-confirm swal2-styled" style="margin-top:10px;">Open Dispatch Modal</button>',
							showConfirmButton: false,
							willOpen: () => {
								document.getElementById('open-dispatch-modal-btn').addEventListener('click', function() {
									if (typeof openDispatchModal === 'function') {
										openDispatchModal(data.order_id || acceptId);
									}
									Swal.close();
								});
							},
							didClose: () => {
								if ($table.length && $.fn.DataTable.isDataTable($table)) {
									$table.DataTable().ajax.reload(null, false);
								} else {
									location.reload();
								}
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Failed',
							text: data.message || 'Accept failed.'
						});
					}
				})
				.catch(function (error) {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: error.response && error.response.data && error.response.data.message ? error.response.data.message : 'An error occurred while accepting.'
					});
				});
			} else if (window.fetch) {
				const formData = new URLSearchParams();
				formData.append('id', acceptId);
				formData.append('_token', csrfToken);

				fetch(acceptUrl, {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					},
					body: formData
				})
				.then(function (response) {
					if (!response.ok) throw response;
					return response.json();
				})
				.then(function (data) {
					if (data.success) {
						Swal.fire({
							icon: 'success',
							title: 'Accepted!',
							text: data.message || 'Item has been accepted.'
						}).then(() => {
							if ($table.length && $.fn.DataTable.isDataTable($table)) {
								$table.DataTable().ajax.reload(null, false);
							} else {
								location.reload();
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Failed',
							text: data.message || 'Accept failed.'
						});
					}
				})
				.catch(async function (error) {
					let message = 'An error occurred while accepting.';
					if (error.json) {
						const errData = await error.json();
						if (errData && errData.message) message = errData.message;
					}
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: message
					});
				});
			} else {
				$.ajax({
					url: acceptUrl,
					type: 'POST',
					data: {
						id: acceptId,
						_token: csrfToken
					},
					success: function (data) {
						if (data.success) {
							Swal.fire({
								icon: 'success',
								title: 'Accepted!',
								text: data.message || 'Item has been accepted.'
							}).then(() => {
								if ($table.length && $.fn.DataTable.isDataTable($table)) {
									$table.DataTable().ajax.reload(null, false);
								} else {
									location.reload();
								}
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Failed',
								text: data.message || 'Accept failed.'
							});
						}
					},
					error: function (xhr) {
						let message = 'An error occurred while accepting.';
						if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: message
						});
					}
				});
			}
		}
	});
});

$(document).on('click', '.cancel-action', function (e) {
	e.preventDefault();
	var button = $(this);
	var cancelUrl = button.data('cancel-url');
	var cancelId = button.data('cancel-id');
	var $table = button.closest('table');
	var csrfToken = window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : $('meta[name="csrf-token"]').attr('content');

	if (!cancelUrl) {
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Cancel URL not specified.'
		});
		return;
	}

	Swal.fire({
		title: 'Cancel Reason Required',
		text: 'Please provide a reason for cancellation:',
		input: 'textarea',
		inputPlaceholder: 'Enter reason here...',
		inputAttributes: {
			'aria-label': 'Cancel reason'
		},
		inputValidator: (value) => {
			if (!value || !value.trim()) {
				return 'Cancel reason is required!';
			}
		},
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, cancel it!'
	}).then((result) => {
		if (result.isConfirmed && result.value && result.value.trim()) {
			var cancelReason = result.value.trim();
			// Prefer axios if available, else fallback to fetch, else jQuery.ajax
			if (typeof axios !== 'undefined') {
				// Send as form data, use cancel_reason as required by backend
				const formData = new URLSearchParams();
				formData.append('id', cancelId);
				formData.append('cancel_reason', cancelReason);
				formData.append('_token', csrfToken);

				axios.post(cancelUrl, formData, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					}
				})
				.then(function (response) {
					var data = response.data;
					if (data.success) {
						Swal.fire({
							icon: 'success',
							title: 'Cancelled!',
							text: data.message || 'Item has been cancelled.'
						}).then(() => {
							if ($table.length && $.fn.DataTable.isDataTable($table)) {
								$table.DataTable().ajax.reload(null, false);
							} else {
								location.reload();
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Failed',
							text: data.message || 'Cancel failed.'
						});
					}
				})
				.catch(function (error) {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: error.response && error.response.data && error.response.data.message ? error.response.data.message : 'An error occurred while cancelling.'
					});
				});
			} else if (window.fetch) {
				// Send as form data, use cancel_reason as required by backend
				const formData = new URLSearchParams();
				formData.append('id', cancelId);
				formData.append('cancel_reason', cancelReason);
				formData.append('_token', csrfToken);

				fetch(cancelUrl, {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					},
					body: formData
				})
				.then(function (response) {
					if (!response.ok) throw response;
					return response.json();
				})
				.then(function (data) {
					if (data.success) {
						Swal.fire({
							icon: 'success',
							title: 'Cancelled!',
							text: data.message || 'Item has been cancelled.'
						}).then(() => {
							if ($table.length && $.fn.DataTable.isDataTable($table)) {
								$table.DataTable().ajax.reload(null, false);
							} else {
								location.reload();
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Failed',
							text: data.message || 'Cancel failed.'
						});
					}
				})
				.catch(async function (error) {
					let message = 'An error occurred while cancelling.';
					if (error.json) {
						const errData = await error.json();
						if (errData && errData.message) message = errData.message;
					}
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: message
					});
				});
			} else {
				// Fallback to jQuery.ajax
				$.ajax({
					url: cancelUrl,
					type: 'POST',
					data: {
						id: cancelId,
						cancel_reason: cancelReason,
						_token: csrfToken
					},
					success: function (data) {
						if (data.success) {
							Swal.fire({
								icon: 'success',
								title: 'Cancelled!',
								text: data.message || 'Item has been cancelled.'
							}).then(() => {
								if ($table.length && $.fn.DataTable.isDataTable($table)) {
									$table.DataTable().ajax.reload(null, false);
								} else {
									location.reload();
								}
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Failed',
								text: data.message || 'Cancel failed.'
							});
						}
					},
					error: function (xhr) {
						let message = 'An error occurred while cancelling.';
						if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: message
						});
					}
				});
			}
		}
	});
});
