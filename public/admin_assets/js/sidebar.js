/**
 * Sidebar Controller - v3.1
 * Enhanced with instant active state updates (no page reload needed)
 */

(function() {
    'use strict';

    const CONFIG = {
        storageKey: 'sidebarPinned',
        hoverDelay: 100,
        collapseDelay: 100,
        classes: {
            sidebar: '.sidebar',
            pinBtn: '#sidebarPinBtn',
            mainContent: '.main-content',
            navLink: '.sidebar .nav-link',
            subNav: '.sidebar .sub-nav',
            mini: 'sidebar-mini',
            hover: 'sidebar-hover',
            pinned: 'sidebar-pinned',
            active: 'active',
            sectionActive: 'section-active'
        }
    };

    let sidebar, pinBtn, mainContent;
    let isPinned = false;
    let hoverTimeout = null;

    /**
     * Initialize
     */
    function init() {
        sidebar = document.querySelector(CONFIG.classes.sidebar);
        pinBtn = document.querySelector(CONFIG.classes.pinBtn);
        mainContent = document.querySelector(CONFIG.classes.mainContent);

        if (!sidebar || !pinBtn) {
            console.warn('Sidebar elements not found');
            return;
        }

        loadState();
        setupEventListeners();
        updateUI();
        setupTooltips();
        handleActiveStates();
        setupDynamicActiveStates();
    }

    /**
     * Load state from localStorage
     */
    function loadState() {
        const saved = localStorage.getItem(CONFIG.storageKey);
        isPinned = saved === 'true';
    }

    /**
     * Save state to localStorage
     */
    function saveState() {
        localStorage.setItem(CONFIG.storageKey, isPinned.toString());
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Pin button
        pinBtn.addEventListener('click', handlePinClick);

        // Hover events
        sidebar.addEventListener('mouseenter', handleMouseEnter);
        sidebar.addEventListener('mouseleave', handleMouseLeave);

        // Keep expanded on click
        sidebar.addEventListener('click', handleSidebarClick);

        // Window resize
        window.addEventListener('resize', debounce(handleResize, 250));

        // Collapse events
        const collapses = sidebar.querySelectorAll('.collapse');
        collapses.forEach(collapse => {
            collapse.addEventListener('show.bs.collapse', handleCollapseShow);
            collapse.addEventListener('hide.bs.collapse', handleCollapseHide);
        });

        // Nav link clicks - ENHANCED for instant active state
        const navLinks = sidebar.querySelectorAll(CONFIG.classes.navLink);
        navLinks.forEach(link => {
            link.addEventListener('click', handleNavLinkClick);
        });
    }

    /**
     * Handle pin button click
     */
    function handlePinClick(e) {
        e.stopPropagation();
        e.preventDefault();

        isPinned = !isPinned;
        saveState();
        updateUI();

        // Visual feedback
        pinBtn.style.transform = 'scale(0.85)';
        setTimeout(() => {
            pinBtn.style.transform = '';
        }, 150);
    }

    /**
     * Handle mouse enter
     */
    function handleMouseEnter() {
        if (isPinned) return;

        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            sidebar.classList.remove(CONFIG.classes.mini);
        }, CONFIG.hoverDelay);
    }

    /**
     * Handle mouse leave
     */
    function handleMouseLeave() {
        if (isPinned) return;

        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            sidebar.classList.add(CONFIG.classes.mini);
        }, CONFIG.collapseDelay);
    }

    /**
     * Handle sidebar click
     */
    function handleSidebarClick() {
        if (isPinned) return;
        clearTimeout(hoverTimeout);
    }

    /**
     * Handle window resize
     */
    function handleResize() {
        if (window.innerWidth < 768) {
            sidebar.classList.add(CONFIG.classes.mini);
            sidebar.classList.remove(CONFIG.classes.pinned);
        } else {
            updateUI();
        }
    }

    /**
     * Handle collapse show
     */
    function handleCollapseShow(e) {
        const trigger = sidebar.querySelector(`[href="#${e.target.id}"]`);
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }
    }

    /**
     * Handle collapse hide
     */
    function handleCollapseHide(e) {
        const trigger = sidebar.querySelector(`[href="#${e.target.id}"]`);
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
    }

    /**
     * Handle nav link click - ENHANCED
     */
    function handleNavLinkClick(e) {
        const link = e.currentTarget;

        // Skip if it's a collapse toggle
        if (link.hasAttribute('data-bs-toggle')) {
            return;
        }

        // Only process if it's an actual navigation link with href
        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#sidebar-')) {
            return;
        }

        // Click animation
        link.style.transform = 'scale(0.96)';
        setTimeout(() => {
            link.style.transform = '';
        }, 100);

        // Update active states BEFORE navigation
        updateActiveStatesInstantly(link);
    }

    /**
     * Update UI based on state
     */
    function updateUI() {
        if (isPinned) {
            sidebar.classList.add(CONFIG.classes.pinned);
            sidebar.classList.remove(CONFIG.classes.mini);
            sidebar.classList.remove(CONFIG.classes.hover);

            pinBtn.setAttribute('title', 'Unpin Sidebar');
            pinBtn.setAttribute('aria-label', 'Unpin Sidebar');
        } else {
            sidebar.classList.remove(CONFIG.classes.pinned);
            sidebar.classList.add(CONFIG.classes.mini);
            sidebar.classList.add(CONFIG.classes.hover);

            pinBtn.setAttribute('title', 'Pin Sidebar');
            pinBtn.setAttribute('aria-label', 'Pin Sidebar');
        }
    }

    /**
     * Setup tooltips for mini mode
     */
    function setupTooltips() {
        const navLinks = sidebar.querySelectorAll(CONFIG.classes.navLink);
        navLinks.forEach(link => {
            const itemName = link.querySelector('.item-name');
            if (itemName) {
                const text = itemName.textContent.trim();
                link.setAttribute('title', text);
            }
        });
    }

    /**
     * Handle active states properly on page load
     */
    function handleActiveStates() {
        // Find all active and section-active links
        const activeLinks = sidebar.querySelectorAll('.nav-link.active');
        const sectionActiveLinks = sidebar.querySelectorAll('.nav-link.section-active');

        // Auto-expand parent sections of active items
        activeLinks.forEach(link => {
            expandParentSections(link);
        });

        // Auto-expand section-active items
        sectionActiveLinks.forEach(link => {
            expandParentSections(link);

            // If it's a collapsible parent, ensure it's expanded
            if (link.hasAttribute('data-bs-toggle')) {
                const targetId = link.getAttribute('href');
                const target = sidebar.querySelector(targetId);
                if (target && !target.classList.contains('show')) {
                    target.classList.add('show');
                    link.setAttribute('aria-expanded', 'true');
                }
            }
        });
    }

    /**
     * Setup dynamic active state updates (NEW)
     */
    function setupDynamicActiveStates() {
        // Watch for URL changes (for SPAs)
        window.addEventListener('popstate', updateActiveStatesFromUrl);

        // Watch for hash changes
        window.addEventListener('hashchange', updateActiveStatesFromUrl);
    }

    /**
     * Update active states instantly when clicking a link (NEW)
     */
    function updateActiveStatesInstantly(clickedLink) {
        // Remove all active states
        const allLinks = sidebar.querySelectorAll('.nav-link');
        allLinks.forEach(link => {
            link.classList.remove(CONFIG.classes.active);
        });

        // Remove all section-active states
        allLinks.forEach(link => {
            if (link.hasAttribute('data-bs-toggle')) {
                link.classList.remove(CONFIG.classes.sectionActive);
            }
        });

        // Add active class to clicked link
        clickedLink.classList.add(CONFIG.classes.active);

        // Find parent sections and mark them as section-active
        let parent = clickedLink.closest('.collapse');
        while (parent) {
            const trigger = sidebar.querySelector(`[href="#${parent.id}"]`);
            if (trigger) {
                trigger.classList.add(CONFIG.classes.sectionActive);
                trigger.classList.remove(CONFIG.classes.active);
            }
            parent = parent.parentElement.closest('.collapse');
        }

        // Expand parent sections
        expandParentSections(clickedLink);

        // Store current active link for persistence
        const href = clickedLink.getAttribute('href');
        if (href) {
            localStorage.setItem('sidebarActiveLink', href);
        }
    }

    /**
     * Update active states based on current URL (NEW)
     */
    function updateActiveStatesFromUrl() {
        const currentPath = window.location.pathname;

        // Find matching link
        const allLinks = sidebar.querySelectorAll('.nav-link[href]');
        let matchedLink = null;

        allLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && !href.startsWith('#sidebar-')) {
                // Create full URL to compare
                const linkUrl = new URL(href, window.location.origin);
                if (linkUrl.pathname === currentPath) {
                    matchedLink = link;
                }
            }
        });

        if (matchedLink) {
            updateActiveStatesInstantly(matchedLink);
        }
    }

    /**
     * Expand all parent sections of a link
     */
    function expandParentSections(link) {
        let parent = link.closest('.collapse');

        while (parent) {
            // Add show class
            parent.classList.add('show');

            // Find the trigger
            const trigger = sidebar.querySelector(`[href="#${parent.id}"]`);
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'true');
            }

            // Move to next parent
            parent = parent.parentElement.closest('.collapse');
        }
    }

    /**
     * Restore active state on page load (NEW)
     */
    function restoreActiveState() {
        const savedHref = localStorage.getItem('sidebarActiveLink');
        if (savedHref) {
            const savedLink = sidebar.querySelector(`.nav-link[href="${savedHref}"]`);
            if (savedLink && !savedLink.classList.contains('active')) {
                // Only restore if no active link is already set by server
                const hasActiveLink = sidebar.querySelector('.nav-link.active');
                if (!hasActiveLink) {
                    updateActiveStatesInstantly(savedLink);
                }
            }
        }
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    /**
     * Public API
     */
    window.SidebarController = {
        pin() {
            if (!isPinned) {
                isPinned = true;
                saveState();
                updateUI();
            }
        },
        unpin() {
            if (isPinned) {
                isPinned = false;
                saveState();
                updateUI();
            }
        },
        toggle() {
            isPinned = !isPinned;
            saveState();
            updateUI();
        },
        isPinned() {
            return isPinned;
        },
        expand() {
            sidebar.classList.remove(CONFIG.classes.mini);
        },
        collapse() {
            if (!isPinned) {
                sidebar.classList.add(CONFIG.classes.mini);
            }
        },
        /**
         * Manually set active link (NEW)
         */
        setActive(linkElement) {
            if (linkElement && linkElement.classList.contains('nav-link')) {
                updateActiveStatesInstantly(linkElement);
            }
        },
        /**
         * Set active by href (NEW)
         */
        setActiveByHref(href) {
            const link = sidebar.querySelector(`.nav-link[href="${href}"]`);
            if (link) {
                updateActiveStatesInstantly(link);
            }
        },
        /**
         * Refresh active states from current URL (NEW)
         */
        refresh() {
            updateActiveStatesFromUrl();
        }
    };

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            init();
            restoreActiveState();
        });
    } else {
        init();
        restoreActiveState();
    }

})();
