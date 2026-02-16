/**
 * Widget: Unit Showcase
 * JavaScript for the Elementor Unit Type Showcase widget
 */
(function ($) {
    'use strict';

    /**
     * Initialize Unit Showcase widget
     * @param {string} widgetId - The widget's unique ID
     */
    function initUnitShowcase(widgetId) {
        var container = $('#showcase-' + widgetId);
        if (!container.length) return;

        // Tab Switcher
        container.find('.unit-type-tab-btn').on('click', function () {
            var target = $(this).data('tab');
            container.find('.unit-type-tab-btn').removeClass('active');
            container.find('.unit-type-pane').removeClass('active');
            $(this).addClass('active');
            container.find('#' + target).addClass('active');
        });

        // Floorplan Gallery Switcher
        container.on('click', '.unit-type-nav-item', function () {
            var $btn = $(this);
            var newSrc = $btn.data('src');
            var newSrcAvif = $btn.data('src-avif');
            var targetImgSelector = $btn.data('target');
            var targetLinkSelector = $btn.data('link');

            var $targetImg = $(targetImgSelector);
            var $targetLink = $(targetLinkSelector);

            // Update Active Button
            $btn.closest('.unit-type-nav-scroller').find('.unit-type-nav-item').removeClass('active-nav');
            $btn.addClass('active-nav');

            // Update Image and Lightbox Link with fade effect
            $targetImg.css('opacity', '0.4');
            setTimeout(function () {
                // Update the img element
                $targetImg.attr('src', newSrc);

                // Update the picture source element if it exists
                var $pictureSource = $targetImg.siblings('source[type="image/avif"]');
                if ($pictureSource.length && newSrcAvif) {
                    $pictureSource.attr('srcset', newSrcAvif);
                }

                // Update the lightbox link
                $targetLink.attr('href', newSrc);

                $targetImg.css('opacity', '1');
            }, 150);
        });

        // Close Modal on Outside Click
        $(window).on('click', function (event) {
            if ($(event.target).hasClass('unit-type-modal')) {
                $(event.target).fadeOut(200);
            }
        });
    }

    // Expose to global scope for inline initialization
    window.HelloBizUnitShowcase = {
        init: initUnitShowcase
    };

    // Auto-initialize on document ready if widgets exist
    $(document).ready(function () {
        $('.unit-type-showcase').each(function () {
            var widgetId = $(this).attr('id').replace('showcase-', '');
            initUnitShowcase(widgetId);
        });
    });

})(jQuery);
