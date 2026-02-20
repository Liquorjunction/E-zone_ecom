/**
 * product-image-gallery.js
 * ─────────────────────────────────────────────
 * Full-featured image gallery for product detail pages.
 *
 * Features:
 *  ✓ Prev / Next arrow buttons (auto-injected)
 *  ✓ Keyboard ← → arrow navigation
 *  ✓ Thumbnail click with active highlight & scroll-into-view
 *  ✓ Smooth opacity fade on image change
 *  ✓ Image counter badge  "2 / 5"
 *  ✓ Lightbox / zoom modal
 *      – Arrows inside lightbox
 *      – Keyboard: ← → to navigate, Escape to close
 *      – Mobile swipe left / right
 *      – Close on backdrop click or × button
 *  ✓ window.ProductGallery.setImages() for variant swaps
 *
 * Load order in @push('scripts'):
 *   1. product-image-gallery.js   ← this file
 *   2. product-detail.js
 *   3. product-reviews.js
 * ─────────────────────────────────────────────
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', initGallery);

    function initGallery() {
        const mainImage    = document.getElementById('main-product-image');
        const thumbButtons = Array.from(document.querySelectorAll('.thumb-button'));

        if (!mainImage || thumbButtons.length === 0) return;

        // Build image list from thumbnails
        const images = thumbButtons.map(btn => ({
            src: btn.getAttribute('data-image') || mainImage.src,
            alt: btn.querySelector('img')?.alt || mainImage.alt || '',
        }));

        let currentIndex = 0;
        let lightbox     = null;
        let lightboxImg  = null;
        let touchStartX  = 0;

        // ── Activate first thumb on load ──────────────────────────────────
        setActiveThumb(0);

        // ─────────────────────────────────────────────────────────────────
        // Core navigate function
        // ─────────────────────────────────────────────────────────────────
        function goTo(rawIndex) {
            if (images.length === 0) return;
            const index   = ((rawIndex % images.length) + images.length) % images.length;
            currentIndex  = index;
            const { src, alt } = images[index];

            // Fade main image
            mainImage.style.transition = 'opacity 0.18s ease';
            mainImage.style.opacity    = '0';
            setTimeout(() => {
                mainImage.src           = src;
                mainImage.alt           = alt;
                mainImage.style.opacity = '1';
            }, 180);

            // Keep lightbox in sync
            if (lightboxImg && lightbox && lightbox.classList.contains('pdlb-open')) {
                lightboxImg.style.opacity = '0';
                setTimeout(() => {
                    lightboxImg.src           = src;
                    lightboxImg.alt           = alt;
                    lightboxImg.style.opacity = '1';
                }, 180);
            }

            setActiveThumb(index);
            updateCounter(index);
        }

        function prev() { goTo(currentIndex - 1); }
        function next() { goTo(currentIndex + 1); }

        // ─────────────────────────────────────────────────────────────────
        // Thumbnail active state
        // ─────────────────────────────────────────────────────────────────
        function setActiveThumb(index) {
            thumbButtons.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('ring-2', 'ring-primary/30', 'border-primary');
                    btn.classList.remove('border-gray-200');
                    btn.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
                } else {
                    btn.classList.remove('ring-2', 'ring-primary/30', 'border-primary');
                    btn.classList.add('border-gray-200');
                }
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // Counter badge
        // ─────────────────────────────────────────────────────────────────
        function updateCounter(index) {
            const c = document.getElementById('pdg-counter');
            if (c) c.textContent = `${index + 1} / ${images.length}`;
        }

        // ─────────────────────────────────────────────────────────────────
        // Thumbnail click listeners
        // ─────────────────────────────────────────────────────────────────
        thumbButtons.forEach((btn, i) => {
            btn.addEventListener('click', () => goTo(i));
        });

        // ─────────────────────────────────────────────────────────────────
        // Inject arrows + counter into the main image wrapper
        // ─────────────────────────────────────────────────────────────────
        const imageWrapper = mainImage.closest('.aspect-square');
        if (imageWrapper) {
            mainImage.style.cursor = 'zoom-in';

            // Inject only once; skip if already present
            if (!imageWrapper.querySelector('.pdg-arrow') && images.length > 1) {
                // Prev arrow
                const prevBtn = createArrow('left');
                prevBtn.addEventListener('click', e => { e.stopPropagation(); prev(); });
                imageWrapper.appendChild(prevBtn);

                // Next arrow
                const nextBtn = createArrow('right');
                nextBtn.addEventListener('click', e => { e.stopPropagation(); next(); });
                imageWrapper.appendChild(nextBtn);

                // Counter
                const counter = document.createElement('div');
                counter.id        = 'pdg-counter';
                counter.className = 'absolute bottom-4 left-4 z-10 pointer-events-none bg-black/55 text-white text-xs font-semibold px-2.5 py-1 rounded-lg backdrop-blur-sm';
                counter.textContent = `1 / ${images.length}`;
                imageWrapper.appendChild(counter);
            }

            // Update the "Click to zoom" hint
            const hint = imageWrapper.querySelector('.absolute.bottom-4.right-4');
            if (hint) hint.textContent = 'Click to zoom';

            // Click main image → open lightbox
            imageWrapper.addEventListener('click', function (e) {
                if (e.target.closest('.pdg-arrow') || e.target.closest('button:not(#main-product-image)')) return;
                openLightbox();
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // Keyboard navigation (page level)
        // ─────────────────────────────────────────────────────────────────
        document.addEventListener('keydown', function (e) {
            const tag = document.activeElement?.tagName?.toLowerCase();
            if (['input', 'textarea', 'select'].includes(tag)) return;

            if (e.key === 'ArrowLeft')  { e.preventDefault(); prev(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); next(); }
            if (e.key === 'Escape' && lightbox && lightbox.classList.contains('pdlb-open')) {
                e.preventDefault();
                closeLightbox();
            }
        });

        // ─────────────────────────────────────────────────────────────────
        // Lightbox
        // ─────────────────────────────────────────────────────────────────
        function buildLightbox() {
            if (document.getElementById('pdlb-overlay')) {
                lightbox    = document.getElementById('pdlb-overlay');
                lightboxImg = document.getElementById('pdlb-img');
                return;
            }

            // Inject styles
            const style = document.createElement('style');
            style.textContent = `
                #pdlb-overlay {
                    position:fixed; inset:0; z-index:9990;
                    display:none; align-items:center; justify-content:center;
                    background:rgba(0,0,0,0);
                    transition:background 0.25s ease;
                    padding: 1rem;
                }
                #pdlb-overlay.pdlb-open {
                    background:rgba(0,0,0,0.9);
                }
                #pdlb-overlay.pdlb-open #pdlb-card {
                    opacity:1; transform:scale(1);
                }
                #pdlb-card {
                    position:relative;
                    max-width:min(88vw,860px);
                    max-height:90vh;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    opacity:0;
                    transform:scale(0.94);
                    transition:opacity 0.22s ease, transform 0.22s ease;
                }
                #pdlb-img {
                    max-width:100%;
                    max-height:85vh;
                    object-fit:contain;
                    border-radius:12px;
                    box-shadow:0 30px 80px rgba(0,0,0,0.55);
                    transition:opacity 0.18s ease;
                    user-select:none;
                    -webkit-user-drag:none;
                    display:block;
                }
                .pdlb-nav {
                    position:absolute; top:50%; transform:translateY(-50%);
                    width:3rem; height:3rem; border-radius:9999px;
                    background:rgba(255,255,255,0.13);
                    border:1px solid rgba(255,255,255,0.22);
                    color:#fff; cursor:pointer;
                    display:flex; align-items:center; justify-content:center;
                    transition:background 0.15s, transform 0.15s;
                    backdrop-filter:blur(6px);
                    z-index:2;
                }
                .pdlb-nav:hover {
                    background:rgba(255,255,255,0.28);
                    transform:translateY(-50%) scale(1.1);
                }
                #pdlb-close {
                    position:absolute; top:-0.75rem; right:-0.75rem;
                    width:2.25rem; height:2.25rem; border-radius:9999px;
                    background:rgba(255,255,255,0.15);
                    border:1px solid rgba(255,255,255,0.25);
                    color:#fff; cursor:pointer;
                    display:flex; align-items:center; justify-content:center;
                    transition:background 0.15s;
                    backdrop-filter:blur(6px);
                    z-index:3;
                }
                #pdlb-close:hover { background:rgba(239,68,68,0.8); }
                #pdlb-counter {
                    position:absolute;
                    bottom:-2.5rem; left:50%; transform:translateX(-50%);
                    color:rgba(255,255,255,0.65);
                    font-size:0.8rem;
                    white-space:nowrap;
                    pointer-events:none;
                }
            `;
            document.head.appendChild(style);

            const el = document.createElement('div');
            el.id = 'pdlb-overlay';
            el.innerHTML = `
                <div id="pdlb-backdrop" style="position:absolute;inset:0;"></div>
                <div id="pdlb-card">
                    <button id="pdlb-close" type="button" aria-label="Close">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                    <button id="pdlb-prev" type="button" class="pdlb-nav" aria-label="Previous" style="left:-1.75rem">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <img id="pdlb-img" src="" alt="" draggable="false"/>
                    <button id="pdlb-next" type="button" class="pdlb-nav" aria-label="Next" style="right:-1.75rem">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <div id="pdlb-counter"></div>
                </div>
            `;
            document.body.appendChild(el);

            lightbox    = el;
            lightboxImg = el.querySelector('#pdlb-img');

            el.querySelector('#pdlb-backdrop').addEventListener('click', closeLightbox);
            el.querySelector('#pdlb-close').addEventListener('click', closeLightbox);
            el.querySelector('#pdlb-prev').addEventListener('click', e => { e.stopPropagation(); prev(); });
            el.querySelector('#pdlb-next').addEventListener('click', e => { e.stopPropagation(); next(); });

            // Swipe support
            el.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
            el.addEventListener('touchend',   e => {
                const diff = touchStartX - e.changedTouches[0].screenX;
                if (Math.abs(diff) > 48) diff > 0 ? next() : prev();
            });
        }

        function updateLightboxCounter() {
            const c = document.getElementById('pdlb-counter');
            if (c) c.textContent = images.length > 1 ? `${currentIndex + 1} / ${images.length}` : '';
        }

        function openLightbox() {
            buildLightbox();
            lightboxImg.src           = images[currentIndex].src;
            lightboxImg.alt           = images[currentIndex].alt;
            lightboxImg.style.opacity = '1';
            lightbox.style.display    = 'flex';
            updateLightboxCounter();
            requestAnimationFrame(() => lightbox.classList.add('pdlb-open'));
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            if (!lightbox) return;
            lightbox.classList.remove('pdlb-open');
            setTimeout(() => { lightbox.style.display = 'none'; }, 260);
            document.body.style.overflow = '';
        }

        // Also update lightbox counter when goTo is called
        const _origGoTo = goTo;
        // Already defined above; we patch via the lightbox counter call
        // The lightboxImg fade already references lightbox.classList.contains so we just add counter update:
        const origGoTo = window.__pdg_goTo_noop = function (idx) {
            _origGoTo(idx);
            updateLightboxCounter();
        };

        // ─────────────────────────────────────────────────────────────────
        // Public API
        // ─────────────────────────────────────────────────────────────────
        window.ProductGallery = {
            goTo,
            prev,
            next,
            /**
             * Replace gallery images — call when a variant is selected.
             * @param {Array<{src: string, alt: string}>} newImages
             */
            setImages(newImages) {
                if (!Array.isArray(newImages) || newImages.length === 0) return;

                // Splice in-place so the closure still references the same array
                images.length = 0;
                newImages.forEach(img => images.push(img));

                // Update thumb buttons
                thumbButtons.forEach((btn, i) => {
                    const img = images[i];
                    if (img) {
                        btn.setAttribute('data-image', img.src);
                        const thumbImg = btn.querySelector('img');
                        if (thumbImg) { thumbImg.src = img.src; thumbImg.alt = img.alt; }
                        btn.style.display = '';
                    } else {
                        btn.style.display = 'none';
                    }
                });

                goTo(0);

                // Update counter total
                const c = document.getElementById('pdg-counter');
                if (c) c.textContent = `1 / ${images.length}`;
            },
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Arrow button factory
    // ─────────────────────────────────────────────────────────────────────────
    function createArrow(direction) {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'pdg-arrow';
        btn.setAttribute('aria-label', direction === 'left' ? 'Previous image' : 'Next image');

        Object.assign(btn.style, {
            position:      'absolute',
            top:           '50%',
            transform:     'translateY(-50%)',
            [direction === 'left' ? 'left' : 'right']: '0.75rem',
            zIndex:        '20',
            display:       'flex',
            alignItems:    'center',
            justifyContent:'center',
            width:         '2.5rem',
            height:        '2.5rem',
            borderRadius:  '9999px',
            background:    'rgba(255,255,255,0.92)',
            boxShadow:     '0 2px 12px rgba(0,0,0,0.14)',
            border:        '1px solid rgba(0,0,0,0.07)',
            cursor:        'pointer',
            transition:    'background 0.15s, transform 0.15s, box-shadow 0.15s',
            backdropFilter:'blur(4px)',
        });

        btn.innerHTML = direction === 'left'
            ? `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>`
            : `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>`;

        btn.addEventListener('mouseenter', () => {
            btn.style.background  = 'rgba(255,255,255,1)';
            btn.style.boxShadow   = '0 4px 18px rgba(0,0,0,0.18)';
            btn.style.transform   = 'translateY(-50%) scale(1.09)';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.background  = 'rgba(255,255,255,0.92)';
            btn.style.boxShadow   = '0 2px 12px rgba(0,0,0,0.14)';
            btn.style.transform   = 'translateY(-50%) scale(1)';
        });

        return btn;
    }

})();
