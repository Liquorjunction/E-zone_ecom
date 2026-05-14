function initAnnouncementBar(text, url) {
    const PX_PER_SEC = 55;

    function makeItem(spaced, hidden) {
        const item = document.createElement('div');
        item.className = 'inline-flex items-center gap-1.75 flex-shrink-0' + (spaced ? ' pr-18' : '');
        if (hidden) item.setAttribute('aria-hidden', 'true');

        // Dot (animated pulse using Tailwind)
        const dot = document.createElement('span');
        dot.className = 'w-1.5 h-1.5 rounded-full bg-green-400 flex-shrink-0 animate-pulse';
        dot.setAttribute('aria-hidden', 'true');
        item.appendChild(dot);

        // Text
        const textSpan = document.createElement('span');
        textSpan.className = 'text-xs font-medium text-white text-opacity-90 tracking-widest';
        textSpan.textContent = text;
        item.appendChild(textSpan);

        // Button (if URL exists)
        if (url) {
            const btn = document.createElement('a');
            btn.href = url;
            btn.target = '_blank';
            btn.rel = 'noopener noreferrer';
            btn.className = 'inline-flex items-center gap-1 text-xs font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-yellow-300 text-blue-900 no-underline hover:bg-yellow-400 hover:scale-105 transition-all duration-150 flex-shrink-0 whitespace-nowrap';
            btn.innerHTML = 'Click Here <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
            item.appendChild(btn);
        }

        return item;
    }

    function init() {
        const track = document.getElementById('ann-track');
        const inner = document.getElementById('ann-inner');
        if (!track || !inner) return;

        track.innerHTML = '';
        track.classList.remove('running');
        track.style.removeProperty('--ann-dur');

        // Probe width with one unstyled item
        const probe = makeItem(false, false);
        track.appendChild(probe);

        requestAnimationFrame(function () {
            const itemW = probe.offsetWidth;
            const barW = inner.offsetWidth;

            if (itemW + 32 <= barW) {
                // Content fits → stay centered, no animation
                track.innerHTML = '';
                track.appendChild(makeItem(false, false));
            } else {
                // Overflow → marquee mode
                track.innerHTML = '';
                track.appendChild(makeItem(true, false));
                track.appendChild(makeItem(true, true)); // one clone

                const dur = ((track.offsetWidth / 2) / PX_PER_SEC).toFixed(2);
                track.style.setProperty('--ann-dur', dur + 's');
                track.classList.add('running');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.addEventListener('resize', init);
}

// Export for module and global usage
export { initAnnouncementBar };
window.initAnnouncementBar = initAnnouncementBar;

// Auto-initialize if data is available
if (window.__announcementData) {
    initAnnouncementBar(window.__announcementData.text, window.__announcementData.url);
}

