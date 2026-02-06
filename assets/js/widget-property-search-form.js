/**
 * Widget: Property Search Form
 * JavaScript for the Elementor Property Search Form widget
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.property-search-form-wrapper').each(function () {
            var $wrapper = $(this);
            // Get data from data attributes instead of inline script variables
            var queryId = $wrapper.data('query-id');
            var $form = $('#search-form-' + queryId);
            var $btn = $('#btn-' + queryId);
            var $toggle = $('#toggle-' + queryId);
            var scopeIds = $wrapper.data('scope-ids');
            var widgetMode = $wrapper.data('widget-mode');
            var resultsPageUrl = $wrapper.data('results-url');
            var targetQueryIdSetting = $wrapper.data('target-query-id');

            // Read URL parameters and pre-fill form fields
            function prefillFromUrl() {
                var urlParams = new URLSearchParams(window.location.search);

                // Pre-fill keyword field
                if (urlParams.has('keyword')) {
                    $form.find('input[name="keyword"]').val(urlParams.get('keyword'));
                }

                // Pre-fill hidden inputs (for hidden filter defaults passed via URL)
                $form.find('input[type="hidden"]').each(function () {
                    var name = $(this).attr('name');
                    if (name && urlParams.has(name)) {
                        $(this).val(urlParams.get(name));
                    }
                });

                // Pre-fill single select fields (meta_range, taxonomy_single)
                $form.find('select').not('[multiple]').each(function () {
                    var name = $(this).attr('name');
                    if (urlParams.has(name)) {
                        $(this).val(urlParams.get(name));
                    }
                });

                // Pre-fill multi-select fields
                $form.find('.custom-multiselect').each(function () {
                    var $wrap = $(this);
                    var name = $wrap.data('name');
                    var paramName = name + '[]';
                    var values = urlParams.getAll(paramName);

                    if (values.length > 0) {
                        var $real = $wrap.find('select');
                        var $trig = $wrap.find('.multi-trigger');
                        var $list = $wrap.find('.multi-list li');
                        var defText = $trig.find('.trigger-text').data('default') || $trig.find('.trigger-text').text();

                        // Store default text
                        if (!$trig.find('.trigger-text').data('default')) {
                            $trig.find('.trigger-text').data('default', defText);
                        }

                        var labs = [];
                        $list.each(function () {
                            var val = String($(this).data('value'));
                            if (values.includes(val)) {
                                $(this).addClass('selected');
                                labs.push($(this).find('.item-label').text());
                            }
                        });

                        $real.val(values);
                        if (labs.length === 1) {
                            $trig.find('.trigger-text').text(labs[0]);
                        } else if (labs.length > 1) {
                            $trig.find('.trigger-text').text(labs.length + ' Selected');
                        }
                    }
                });
            }

            $toggle.on('click', function () {
                $form.find('.form-fields-wrapper').slideToggle(300);
                $(this).toggleClass('active');
            });

            $form.find('.custom-multiselect').each(function () {
                var $wrap = $(this), $real = $wrap.find('select'), $trig = $wrap.find('.multi-trigger'), $drop = $wrap.find('.multi-dropdown'), $search = $wrap.find('.multi-search-input'), $list = $wrap.find('.multi-list li'), defText = $trig.find('.trigger-text').text();

                // Store default text for later use
                $trig.find('.trigger-text').data('default', defText);

                $trig.on('click', function (e) { e.stopPropagation(); $('.multi-dropdown').not($drop).removeClass('active'); $drop.toggleClass('active'); });
                $list.on('click', function (e) {
                    e.stopPropagation(); $(this).toggleClass('selected');
                    var vals = [], labs = [];
                    $wrap.find('.multi-list li.selected').each(function () { vals.push(String($(this).data('value'))); labs.push($(this).find('.item-label').text()); });

                    // Fix: Properly sync hidden select options for serialize() to work
                    $real.find('option').each(function () {
                        $(this).prop('selected', vals.indexOf(String($(this).val())) > -1);
                    });
                    $real.trigger('change');

                    if (labs.length === 0) $trig.find('.trigger-text').text(defText); else if (labs.length === 1) $trig.find('.trigger-text').text(labs[0]); else $trig.find('.trigger-text').text(labs.length + ' Selected');
                });
                $search.on('input', function () { var t = $(this).val().toLowerCase(); $list.each(function () { $(this).toggle($(this).text().toLowerCase().indexOf(t) > -1); }); });
                $(document).on('click', function () { $drop.removeClass('active'); }); $drop.on('click', function (e) { e.stopPropagation(); });
            });

            function trigger_search() {
                $(document).trigger('elementor/property_search/trigger', [queryId, $form.serialize(), scopeIds]);
            }

            function handleStandaloneSearch() {
                if (!resultsPageUrl) {
                    console.warn('No results page URL configured for standalone mode.');
                    return;
                }

                // Build query string from form
                var params = new URLSearchParams();

                // Add target query ID for tab selection
                var targetQueryId = targetQueryIdSetting ? targetQueryIdSetting : queryId;
                params.append('target_query_id', targetQueryId);

                // Get keyword
                var keyword = $form.find('input[name="keyword"]').val();
                if (keyword) {
                    params.append('keyword', keyword);
                }

                // Get hidden inputs (for hidden filters with default values)
                $form.find('input[type="hidden"]').each(function () {
                    var name = $(this).attr('name');
                    var val = $(this).val();
                    if (name && val) {
                        params.append(name, val);
                    }
                });

                // Get single selects (meta_range, taxonomy_single)
                $form.find('select').not('[multiple]').each(function () {
                    var val = $(this).val();
                    if (val) {
                        params.append($(this).attr('name'), val);
                    }
                });

                // Get multi-selects
                $form.find('.custom-multiselect').each(function () {
                    var $wrap = $(this);
                    var name = $wrap.data('name');
                    $wrap.find('.multi-list li.selected').each(function () {
                        params.append(name + '[]', $(this).data('value'));
                    });
                });

                // Build full URL and redirect
                var separator = resultsPageUrl.indexOf('?') > -1 ? '&' : '?';
                var queryString = params.toString();
                var fullUrl = queryString ? resultsPageUrl + separator + queryString : resultsPageUrl;

                window.location.href = fullUrl;
            }

            $form.on('keydown', '.no-enter', function (e) { if (e.keyCode === 13) { e.preventDefault(); return false; } });

            if (widgetMode === 'standalone') {
                // Standalone mode: redirect on search
                $btn.on('click', handleStandaloneSearch);
            } else {
                // Results grid mode: live search
                $form.find('select').not('[multiple]').on('change', trigger_search);
                $form.find('select[multiple]').on('change', trigger_search);
                $btn.on('click', trigger_search);

                // Check if this widget is the target from URL and activate its tab
                var urlParams = new URLSearchParams(window.location.search);
                var targetQueryId = urlParams.get('target_query_id');

                if (targetQueryId && targetQueryId === queryId) {
                    // This is the target widget - activate its tab
                    var $widgetWrapper = $form.closest('.property-search-form-wrapper');

                    // Find if widget is inside Elementor tabs
                    var $tabContent = $widgetWrapper.closest('.e-n-tabs-content > .e-n-tab-content, .elementor-tab-content, [data-tab]');

                    if ($tabContent.length) {
                        // Get tab index
                        var tabIndex = $tabContent.index();
                        var $tabsContainer = $tabContent.closest('.e-n-tabs, .elementor-tabs');

                        // Try Elementor nested tabs (new structure)
                        var $tabTitle = $tabsContainer.find('.e-n-tabs-heading .e-n-tab-title').eq(tabIndex);
                        if ($tabTitle.length) {
                            $tabTitle.trigger('click');
                        } else {
                            // Try classic Elementor tabs
                            var $classicTab = $tabsContainer.find('.elementor-tab-title[data-tab="' + (tabIndex + 1) + '"]');
                            if ($classicTab.length) {
                                $classicTab.trigger('click');
                            }
                        }
                    }

                    // Pre-fill form and trigger search for this widget
                    prefillFromUrl();
                    setTimeout(trigger_search, 150);
                } else if (!targetQueryId) {
                    // No target specified - normal behavior
                    prefillFromUrl();
                    setTimeout(trigger_search, 100);
                }
                // If targetQueryId exists but doesn't match, don't pre-fill or trigger search
            }
        });
    });

})(jQuery);
