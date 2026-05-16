// resources/js/timer.js
// Reusable countdown timer for Blade templates
// Usage: <span id="timer" data-timer-seconds="300">5:00</span>
//        window.startCountdownTimer('timer');


window.startCountdownTimer = function(timerId = 'timer') {
    var timerElement = document.getElementById(timerId);
    if (!timerElement) return;
    if (timerElement.dataset.timerInitialized === 'true') return;
    timerElement.dataset.timerInitialized = 'true';

    var storage = null;
    try {
        storage = window.sessionStorage;
    } catch (e) {
        storage = null;
    }

    var storageKeyAttr = timerElement.getAttribute('data-timer-storage-key');
    var storageKey = storageKeyAttr || ('countdown:' + window.location.pathname + ':' + timerId);
    var totalSeconds = parseInt(timerElement.getAttribute('data-timer-seconds')) || 300;
    var expiredLabel = timerElement.getAttribute('data-expired-label') || '0:00';
    var expiredMessage = timerElement.getAttribute('data-expired-message');
    var expiredMessageTargetId = timerElement.getAttribute('data-expired-message-target');
    var expiredMessageTarget = expiredMessageTargetId ? document.getElementById(expiredMessageTargetId) : null;
    var persistedEndTime = storage ? parseInt(storage.getItem(storageKey), 10) : NaN;
    var endTime = Number.isFinite(persistedEndTime) && persistedEndTime > 0
        ? persistedEndTime
        : Date.now() + totalSeconds * 1000;

    if (storage && (!Number.isFinite(persistedEndTime) || persistedEndTime <= 0)) {
        storage.setItem(storageKey, String(endTime));
    }

    function updateTimer() {
        var now = Date.now();
        var remaining = Math.ceil((endTime - now) / 1000);
        if (remaining < 0) remaining = 0;
        var minutes = Math.floor(remaining / 60);
        var seconds = remaining % 60;
        timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        if (remaining <= 0) {
            clearInterval(interval);
            timerElement.textContent = expiredLabel;

            if (storage) {
                storage.setItem(storageKey, String(endTime));
            }

            if (expiredMessage && expiredMessageTarget) {
                expiredMessageTarget.textContent = expiredMessage;
            }
        }
    }

    updateTimer();
    var interval = setInterval(updateTimer, 1000);
};

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('timer')) {
        window.startCountdownTimer('timer');
    }
});
