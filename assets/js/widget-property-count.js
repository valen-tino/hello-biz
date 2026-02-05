/**
 * Widget: Property Count
 * JavaScript for the Elementor Property Result Count widget
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.property-count-wrapper').each(function () {
            var $wrapper = $(this);
            var myQueryId = $wrapper.attr('id').replace('count-', '');
            var $number = $wrapper.find('.pc-number');

            $(document).on('elementor/property_search/updated', function (event, targetId, data) {
                if (targetId === myQueryId && data.total !== undefined) {
                    var total = parseInt(data.total);

                    if (total === 0) {
                        // If count is 0, hide the whole widget
                        $wrapper.fadeOut(200);
                    } else {
                        // If count > 0, show widget and update number
                        if ($wrapper.is(':hidden')) {
                            $wrapper.fadeIn(200);
                        }
                        $number.text(total);
                    }
                }
            });
        });
    });

})(jQuery);
