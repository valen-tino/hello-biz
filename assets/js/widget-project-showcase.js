/**
 * Widget: Project Showcase
 * JavaScript for the Elementor Project Showcase widget
 * Handles random project selection on page load
 */
(function () {
    'use strict';

    /**
     * Initialize a Project Showcase widget
     * @param {string} uid - Unique widget ID
     * @param {Array} data - Array of project data objects
     */
    function initProjectShowcase(uid, data) {
        var container = document.getElementById(uid);
        if (!container || !data || data.length === 0) return;

        // Pick random project
        var idx = Math.floor(Math.random() * data.length);
        var p = data[idx];

        // Update image - replace entire HTML to avoid browser caching issues
        var imgContainer = container.querySelector('.project-showcase-image');
        if (imgContainer) {
            imgContainer.innerHTML = '';
            var img = document.createElement('img');
            img.src = p.image;
            img.alt = p.title;
            // Set explicit width and height to prevent layout shifts (CLS)
            if (p.image_width) img.setAttribute('width', p.image_width);
            if (p.image_height) img.setAttribute('height', p.image_height);
            imgContainer.appendChild(img);
        }

        // Update subtitle
        var subEl = document.getElementById(uid + '_sub');
        if (subEl) {
            if (p.subtitle) {
                subEl.textContent = p.subtitle;
                subEl.style.display = '';
            } else {
                subEl.style.display = 'none';
            }
        }

        // Update title
        var titleEl = document.getElementById(uid + '_title');
        if (titleEl) titleEl.textContent = p.title;

        // Update description
        var descEl = document.getElementById(uid + '_desc');
        if (descEl) {
            if (p.description) {
                descEl.textContent = p.description;
                descEl.style.display = '';
            } else {
                descEl.style.display = 'none';
            }
        }

        // Update info boxes
        var boxHtml = '';
        if (p.ib1_val) {
            boxHtml += '<div class="info-box"><span class="info-value">' + p.ib1_val + '</span>';
            if (p.ib1_lbl) boxHtml += '<span class="info-label">' + p.ib1_lbl + '</span>';
            boxHtml += '</div>';
        }
        if (p.ib2_val) {
            boxHtml += '<div class="info-box"><span class="info-value">' + p.ib2_val + '</span>';
            if (p.ib2_lbl) boxHtml += '<span class="info-label">' + p.ib2_lbl + '</span>';
            boxHtml += '</div>';
        }
        var boxEl = document.getElementById(uid + '_boxes');
        if (boxEl) boxEl.innerHTML = boxHtml;

        // Update button
        var btnEl = document.getElementById(uid + '_btn');
        if (btnEl) {
            btnEl.href = p.btn_link;
            var btnTxt = btnEl.querySelector('.txt');
            if (btnTxt) btnTxt.textContent = p.btn_text;
        }

        // Reveal content with fade-in
        container.style.opacity = 1;
    }

    // Expose to global scope for inline initialization
    window.HelloBizProjectShowcase = {
        init: initProjectShowcase
    };

})();
