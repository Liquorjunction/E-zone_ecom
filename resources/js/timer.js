// resources/js/timer.js
// Reusable countdown timer for Blade templates
// Usage: <span id="timer" data-timer-seconds="300">5:00</span>
//        window.startCountdownTimer('timer');


window.startCountdownTimer = function(timerId = 'timer') {
    var timerElement = document.getElementById(timerId);
    if (!timerElement) return;
    var totalSeconds = parseInt(timerElement.getAttribute('data-timer-seconds')) || 300;
    var endTime = Date.now() + totalSeconds * 1000;
    function updateTimer() {
        var now = Date.now();
        var remaining = Math.round((endTime - now) / 1000);
        if (remaining < 0) remaining = 0;
        var minutes = Math.floor(remaining / 60);
        var seconds = remaining % 60;
        timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        if (remaining <= 0) {
            clearInterval(interval);
            timerElement.textContent = 'expired';
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
