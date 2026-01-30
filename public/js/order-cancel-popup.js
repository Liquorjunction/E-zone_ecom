document.addEventListener("DOMContentLoaded", function () {
    const cancelModal = document.getElementById("cancel-order-modal");
    if (cancelModal && cancelModal.parentNode !== document.body) {
        document.body.appendChild(cancelModal);
    }
    if (cancelModal) {
        cancelModal.classList.add("bg-black", "bg-opacity-50");
        cancelModal.style.backgroundColor = "rgba(0,0,0,0.5)";
    }
    const cancelForm = document.getElementById("cancel-order-form");
    const cancelIdInput = document.getElementById("cancel-order-id");
    const cancelOrderNumberSpan = document.getElementById("cancel-order-number");
    const openCancelBtns = document.querySelectorAll(".open-cancel-modal-btn");
    const closeCancelBtns = document.querySelectorAll(".close-cancel-modal-btn");
    if (openCancelBtns && cancelForm && cancelModal) {
        openCancelBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
                const orderId = this.getAttribute("data-order-id");
                const orderNumber = this.getAttribute("data-order-number");
                cancelIdInput.value = orderId;
                if (cancelOrderNumberSpan) cancelOrderNumberSpan.textContent = orderNumber;
                cancelForm.action = "/client/orders/cancel";
                cancelModal.classList.remove("hidden");
                cancelModal.classList.add("flex");
            });
        });
    }
    if (closeCancelBtns && cancelForm && cancelModal) {
        closeCancelBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
                cancelModal.classList.add("hidden");
                cancelModal.classList.remove("flex");
                cancelForm.reset();
            });
        });
    }
    if (cancelModal && cancelForm) {
        cancelModal.addEventListener("click", function (e) {
            if (e.target === this) {
                cancelModal.classList.add("hidden");
                cancelModal.classList.remove("flex");
                cancelForm.reset();
            }
        });
        cancelForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(cancelForm);
            const submitBtn = cancelForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Cancelling...';
            }
            fetch(cancelForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token') || document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    // Always hide the cancel modal before showing the universal popup
                    cancelModal.classList.add("hidden");
                    cancelModal.classList.remove("flex");
                    cancelForm.reset();
                    if (data && (data.success || data.status === 'success')) {
                        window.showUniversalPopup(data.message || 'Order cancelled successfully', 'success');
                        if (typeof ajaxLoad === 'function') {
                            ajaxLoad(window.location.href);
                        } else {
                            setTimeout(() => window.location.reload(), 1200);
                        }
                    } else {
                        window.showUniversalPopup((data && data.message) || 'Failed to cancel order', 'failure');
                    }
                })
                .catch(() => {
                    // Also hide the modal before showing failure popup
                    cancelModal.classList.add("hidden");
                    cancelModal.classList.remove("flex");
                    cancelForm.reset();
                    window.showUniversalPopup('Failed to cancel order', 'failure');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Confirm Cancel';
                    }
                });
        });
    }
});
