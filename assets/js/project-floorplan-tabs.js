/**
 * Widget: Project Floorplan Tabs
 * JavaScript for the Elementor Project Floorplan Tabs widget
 */
(function ($) {
    'use strict';

    /**
     * Initialize Floorplan Tabs widget
     * @param {string} widgetId - The widget's unique ID
     */
    function initFloorplanTabs(widgetId) {
        var $container = $('#fp-widget-' + widgetId);
        if (!$container.length) return;

        $container.find('.fp-tab-btn').on('click', function () {
            // Update buttons
            $container.find('.fp-tab-btn').removeClass('active');
            $(this).addClass('active');

            // Update panes
            var target = $(this).data('target');
            $container.find('.fp-pane').removeClass('active');
            $container.find('#' + target).addClass('active');
        });
    }

    // Expose to global scope for inline initialization
    window.HelloBizFloorplanTabs = {
        init: initFloorplanTabs
    };

    // Auto-initialize on document ready
    $(document).ready(function () {
        $('.fp-tabs-widget').each(function () {
            var widgetId = $(this).attr('id').replace('fp-widget-', '');
            initFloorplanTabs(widgetId);
        });
    });

})(jQuery);
