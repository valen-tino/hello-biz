/**
 * Widget: Property Results Grid
 * JavaScript for the Elementor Property Search Results widget
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.property-results-wrapper').each(function () {
            var $wrapper = $(this);
            var myQueryId = $wrapper.attr('id').replace('results-wrapper-', '');
            var $grid = $wrapper.find('.property-grid-results');
            var $pag = $wrapper.find('.property-pagination');
            var $loader = $wrapper.find('.search-loader');

            var config = {
                template_id: $wrapper.data('template'),
                ppp: $wrapper.data('ppp'),
                nonce: $wrapper.data('nonce')
            };

            var currentFilters = '';
            var currentScope = '';

            // Listen for Filter Trigger
            $(document).on('elementor/property_search/trigger', function (event, targetId, filterData, scopeId) {
                if (targetId === myQueryId) {
                    currentFilters = filterData;
                    currentScope = scopeId;
                    run_ajax(1);
                }
            });

            // AJAX Call
            function run_ajax(page) {
                $loader.fadeIn();
                $grid.css('opacity', '0.2');

                var payload = currentFilters +
                    '&action=filter_properties_live' +
                    '&security=' + config.nonce +
                    '&term_id=' + encodeURIComponent(JSON.stringify(currentScope)) +
                    '&template_id=' + config.template_id +
                    '&ppp=' + config.ppp +
                    '&paged=' + page;

                $.post(window.ajaxurl || '/wp-admin/admin-ajax.php', payload, function (res) {
                    if (res.success) {
                        $grid.html(res.data.html);
                        $pag.html(res.data.pagination);
                        $(document).trigger('elementor/property_search/updated', [myQueryId, res.data]);
                    } else {
                        $grid.html('<div class="no-results">Error loading.</div>');
                    }
                    $loader.fadeOut();
                    $grid.css('opacity', '1');
                });
            }

            // Pagination Click Logic
            $pag.on('click', '.page-numbers', function (e) {
                e.preventDefault();
                var $link = $(this);
                var newPage = 1;

                // 1. Check if it's "Next"
                if ($link.hasClass('next')) {
                    var current = parseInt($pag.find('.current').text());
                    newPage = current + 1;
                }
                // 2. Check if it's "Prev"
                else if ($link.hasClass('prev')) {
                    var current = parseInt($pag.find('.current').text());
                    newPage = current - 1;
                }
                // 3. Regular Number
                else {
                    var textVal = $link.text().replace(/\D/g, ''); // Extract number only
                    newPage = parseInt(textVal);
                }

                if (newPage && !isNaN(newPage)) {
                    run_ajax(newPage);
                    // Smooth scroll to top of grid
                    $('html, body').animate({ scrollTop: $wrapper.offset().top - 100 }, 500);
                }
            });
        });
    });

})(jQuery);
