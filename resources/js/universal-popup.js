(function () {
    // Move popup to body on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('universal-popup');
        if (popup && popup.parentNode !== document.body) {
            document.body.appendChild(popup);
        }
    });

    function showUniversalPopup(message, type = "success", timeout = 3000, options = {}) {
        let popup = document.getElementById("universal-popup");
        let card = document.getElementById("universal-popup-card");
        let icon = document.getElementById("universal-popup-icon");
        let msg = document.getElementById("universal-popup-message");
        let closeBtn = document.getElementById("universal-popup-close");
        let xBtn = document.getElementById("universal-popup-x");
        let overlay = document.getElementById("universal-popup-overlay");
        let confirmButtons = document.getElementById("universal-popup-confirm-buttons");
        let confirmBtn = document.getElementById("universal-popup-confirm");
        let cancelBtn = document.getElementById("universal-popup-cancel");

        // Extract options
        const isConfirmation = options.confirmation || false;
        const onConfirm = options.onConfirm || null;
        const onCancel = options.onCancel || null;
        const confirmText = options.confirmText || "Yes";
        const cancelText = options.cancelText || "No";

        if (!popup) {
            fetch("client/components/universal-popup.blade.php")
                .then((res) => res.text())
                .then((html) => {
                    const temp = document.createElement("div");
                    temp.innerHTML = html;
                    document.body.appendChild(temp.firstElementChild);
                    showUniversalPopup(message, type, timeout, options);
                });
            return;
        }

        // Ensure popup is in body (fallback)
        if (popup.parentNode !== document.body) {
            document.body.appendChild(popup);
        }

        // Clear any existing timeout
        if (window.__universalPopupTimeout) {
            clearTimeout(window.__universalPopupTimeout);
        }

        // Card color and icon with smooth transition
        if (card && icon) {
            if (type === "success") {
                card.classList.remove("border-red-200", "bg-red-50");
                card.classList.add("border-green-200", "bg-green-50");
                icon.innerHTML = `
                    <div class="relative">
                        <svg class='w-16 h-16 text-green-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <circle cx='12' cy='12' r='10' stroke='currentColor' stroke-width='2' fill='white'/>
                            <path class="checkmark-path" stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M9 12l2 2 4-4'/>
                        </svg>
                    </div>
                `;
            } else {
                card.classList.remove("border-green-200", "bg-green-50");
                card.classList.add("border-red-200", "bg-red-50");
                icon.innerHTML = `
                    <div class="relative">
                        <svg class='w-16 h-16 text-red-500' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <circle cx='12' cy='12' r='10' stroke='currentColor' stroke-width='2' fill='white'/>
                            <path class="x-path" stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M8 8l8 8M16 8l-8 8'/>
                        </svg>
                    </div>
                `;
            }
        }

        if (msg) msg.textContent = message;

        // Toggle between single OK button and confirmation buttons
        if (isConfirmation) {
            if (closeBtn) closeBtn.classList.add("hidden");
            if (confirmButtons) {
                confirmButtons.classList.remove("hidden");
                confirmButtons.classList.add("flex");
            }
            if (confirmBtn) confirmBtn.textContent = confirmText;
            if (cancelBtn) cancelBtn.textContent = cancelText;
            console.log("Confirmation mode enabled:", { closeBtn, confirmButtons, confirmBtn, cancelBtn });
            // Disable auto-close for confirmation dialogs
            timeout = 0;
        } else {
            if (closeBtn) closeBtn.classList.remove("hidden");
            if (confirmButtons) {
                confirmButtons.classList.add("hidden");
                confirmButtons.classList.remove("flex");
            }
            console.log("Normal mode - showing OK button");
        }

        // Show popup with animation (no .hidden/.flex)
        popup.classList.remove("opacity-0", "pointer-events-none");
        popup.classList.add("opacity-100", "pointer-events-auto");

        // Trigger card animation
        if (card) {
            card.classList.remove("scale-95", "opacity-0");
            card.classList.add("scale-100", "opacity-100");
        }

        // Close function
        const closePopup = function (invokeCallback = false, isConfirmed = false) {
            if (card) {
                card.classList.remove("scale-100", "opacity-100");
                card.classList.add("scale-95", "opacity-0");
            }
            setTimeout(() => {
                popup.classList.remove("opacity-100", "pointer-events-auto");
                popup.classList.add("opacity-0", "pointer-events-none");

                // Invoke callbacks after popup is hidden
                if (invokeCallback) {
                    if (isConfirmed && onConfirm) {
                        onConfirm();
                    } else if (!isConfirmed && onCancel) {
                        onCancel();
                    }
                }
            }, 200);
            if (window.__universalPopupTimeout) {
                clearTimeout(window.__universalPopupTimeout);
            }
        };

        // Close handlers
        if (closeBtn) closeBtn.onclick = () => closePopup();
        if (xBtn) xBtn.onclick = () => closePopup(isConfirmation, false);
        if (overlay) overlay.onclick = () => closePopup(isConfirmation, false);

        // Confirmation handlers
        if (confirmBtn) {
            confirmBtn.onclick = () => closePopup(true, true);
        }
        if (cancelBtn) {
            cancelBtn.onclick = () => closePopup(true, false);
        }

        // Auto-close after timeout
        if (timeout > 0) {
            window.__universalPopupTimeout = setTimeout(closePopup, timeout);
        }
    }

    window.showUniversalPopup = showUniversalPopup;
})();
