/**
 * Custom "Magic" Cursor with Link Detection
 * Handles carousel navigation with custom cursor icons
 */
(function () {
    'use strict';

    /* ==========================================================================
       CONFIGURATION
       ========================================================================== */
    const CONFIG = {
        carouselIds: ['magic-cursor-carousel', 'magic-cursor-carousel-2'],
        breakPoint: 1024,
        cursorSize: 50
    };

    /* ==========================================================================
       SVG ICONS
       ========================================================================== */
    const ICONS = {
        // Left Arrow
        prev: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'><defs><filter id='shadow' x='-50%' y='-50%' width='200%' height='200%'><feDropShadow dx='0' dy='0' stdDeviation='2.5' flood-color='black' flood-opacity='0.15'/></filter></defs><circle cx='25' cy='25' r='20' fill='white' filter='url(%23shadow)'/><path d='M29 17 L21 25 L29 33' stroke='black' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>",

        // Right Arrow
        next: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'><defs><filter id='shadow' x='-50%' y='-50%' width='200%' height='200%'><feDropShadow dx='0' dy='0' stdDeviation='2.5' flood-color='black' flood-opacity='0.15'/></filter></defs><circle cx='25' cy='25' r='20' fill='white' filter='url(%23shadow)'/><path d='M21 17 L29 25 L21 33' stroke='black' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>",

        // Pointer Icon (Rotated -45deg to point Top-Left)
        pointer: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'><defs><filter id='shadow' x='-50%' y='-50%' width='200%' height='200%'><feDropShadow dx='0' dy='0' stdDeviation='2.5' flood-color='black' flood-opacity='0.15'/></filter></defs><circle cx='25' cy='25' r='20' fill='white' filter='url(%23shadow)'/><path d='M25 18 L25 32 M25 18 L30 23 M25 18 L20 23' stroke='black' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round' transform='rotate(-45 25 25)'/></svg>"
    };

    /* ==========================================================================
       UTILITY FUNCTIONS
       ========================================================================== */

    /**
     * Check if viewport is desktop size
     */
    function isDesktop() {
        return window.innerWidth > CONFIG.breakPoint;
    }

    /**
     * Check if element is an interactive element (link, button, etc.)
     */
    function isInteractiveElement(target) {
        return target.closest('a') ||
            target.closest('button') ||
            target.closest('.elementor-widget-image') ||
            target.closest('.elementor-widget-heading');
    }

    /* ==========================================================================
       CURSOR FOLLOWER
       ========================================================================== */

    /**
     * Create and append the follower element to the DOM
     */
    function createFollower() {
        const follower = document.createElement('div');
        follower.classList.add('magic-cursor-follower');
        document.body.appendChild(follower);
        return follower;
    }

    /**
     * Update follower position based on mouse coordinates
     */
    function updateFollowerPosition(follower, clientX, clientY) {
        const offset = CONFIG.cursorSize / 2;
        follower.style.top = (clientY - offset) + 'px';
        follower.style.left = (clientX - offset) + 'px';
    }

    /**
     * Show the follower cursor
     */
    function showFollower(follower) {
        follower.style.opacity = '1';
        follower.style.transform = 'scale(1)';
    }

    /**
     * Hide the follower cursor
     */
    function hideFollower(follower) {
        follower.style.opacity = '0';
        follower.style.transform = 'scale(0)';
    }

    /**
     * Set follower icon
     */
    function setFollowerIcon(follower, iconKey) {
        follower.style.backgroundImage = `url("${ICONS[iconKey]}")`;
    }

    /* ==========================================================================
       EVENT HANDLERS
       ========================================================================== */

    /**
     * Handle mouse enter event
     */
    function handleMouseEnter(follower) {
        if (!isDesktop()) return;
        showFollower(follower);
    }

    /**
     * Handle mouse leave event
     */
    function handleMouseLeave(follower, container) {
        hideFollower(follower);
        container.removeAttribute('data-cursor-action');
    }

    /**
     * Handle mouse move event
     */
    function handleMouseMove(e, follower, container) {
        if (!isDesktop()) return;

        // Update position
        updateFollowerPosition(follower, e.clientX, e.clientY);

        // Check if hovering over interactive element
        if (isInteractiveElement(e.target)) {
            setFollowerIcon(follower, 'pointer');
            follower.style.transform = 'scale(1.1)';
            container.setAttribute('data-cursor-action', 'link');
            return;
        }

        // Default: Show left/right arrows based on position
        follower.style.transform = 'scale(1)';
        const rect = container.getBoundingClientRect();
        const relX = e.clientX - rect.left;

        if (relX < rect.width / 2) {
            setFollowerIcon(follower, 'prev');
            container.setAttribute('data-cursor-action', 'prev');
        } else {
            setFollowerIcon(follower, 'next');
            container.setAttribute('data-cursor-action', 'next');
        }
    }

    /**
     * Handle click event
     */
    function handleClick(e, follower, container) {
        if (!isDesktop()) return;

        // Animation feedback
        follower.style.transform = 'scale(0.8)';
        setTimeout(() => { follower.style.transform = 'scale(1)'; }, 100);

        // Don't navigate if clicking on interactive element
        if (isInteractiveElement(e.target)) return;

        // Trigger navigation
        const action = container.getAttribute('data-cursor-action');
        const prevBtn = container.querySelector('.elementor-swiper-button-prev');
        const nextBtn = container.querySelector('.elementor-swiper-button-next');

        if (action === 'prev' && prevBtn) {
            prevBtn.click();
        } else if (action === 'next' && nextBtn) {
            nextBtn.click();
        }
    }

    /* ==========================================================================
       INITIALIZATION
       ========================================================================== */

    /**
     * Initialize magic cursor for a specific carousel container
     */
    function initMagicCursor(id, follower) {
        const container = document.getElementById(id);
        if (!container) return;

        container.addEventListener('mouseenter', () => handleMouseEnter(follower));
        container.addEventListener('mouseleave', () => handleMouseLeave(follower, container));
        container.addEventListener('mousemove', (e) => handleMouseMove(e, follower, container));
        container.addEventListener('click', (e) => handleClick(e, follower, container));
    }

    /**
     * Main initialization
     */
    function init() {
        const follower = createFollower();

        CONFIG.carouselIds.forEach(id => {
            initMagicCursor(id, follower);
        });
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', init);

})();
