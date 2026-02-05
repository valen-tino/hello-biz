/**
 * Recently Viewed Properties Tracker
 * Tracks property views in localStorage and loads them in the widget
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'recently_viewed_properties';
    const MAX_ITEMS = 10;

    /**
     * Get viewed properties from localStorage
     */
    function getViewedProperties() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        } catch (e) {
            console.error('Error reading recently viewed properties:', e);
            return [];
        }
    }

    /**
     * Save viewed properties to localStorage and Cookie
     */
    function saveViewedProperties(properties) {
        try {
            const jsonStr = JSON.stringify(properties);
            localStorage.setItem(STORAGE_KEY, jsonStr);

            // Also set cookie for server-side rendering
            const d = new Date();
            d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30 days
            const expires = "expires=" + d.toUTCString();
            document.cookie = STORAGE_KEY + "=" + encodeURIComponent(jsonStr) + ";" + expires + ";path=/";

        } catch (e) {
            console.error('Error saving recently viewed properties:', e);
        }
    }

    /**
     * Add a property to the viewed list
     */
    function addViewedProperty(propertyId) {
        if (!propertyId) return;

        let properties = getViewedProperties();

        // Remove if already exists (to move to front)
        properties = properties.filter(id => id !== propertyId);

        // Add to front
        properties.unshift(propertyId);

        // Limit to max items
        if (properties.length > MAX_ITEMS) {
            properties = properties.slice(0, MAX_ITEMS);
        }

        saveViewedProperties(properties);
    }

    /**
     * Track current property if on single property page
     */
    function trackCurrentProperty() {
        // Check if we have property data from WordPress
        if (typeof recentlyViewedData !== 'undefined' && recentlyViewedData.currentPropertyId) {
            addViewedProperty(parseInt(recentlyViewedData.currentPropertyId, 10));
        }
    }

    /**
     * Load recently viewed properties via AJAX
     */
    function loadRecentlyViewedWidget() {
        const widgets = document.querySelectorAll('.recently-viewed-properties-widget');

        widgets.forEach(function (widget) {
            const propertyIds = getViewedProperties();
            const maxProperties = parseInt(widget.dataset.maxProperties, 10) || 5;
            const noResultsMessage = widget.dataset.noResults || 'No recently viewed properties.';

            // Show loading state
            widget.innerHTML = '<div class="rv-loading"><span class="rv-spinner"></span></div>';

            if (propertyIds.length === 0) {
                widget.innerHTML = '<div class="rv-no-results">' + noResultsMessage + '</div>';
                return;
            }

            // Get limited IDs
            const limitedIds = propertyIds.slice(0, maxProperties);

            // AJAX request to get property data
            const formData = new FormData();
            formData.append('action', 'get_recently_viewed_properties');
            formData.append('property_ids', JSON.stringify(limitedIds));
            formData.append('nonce', recentlyViewedData.nonce);

            fetch(recentlyViewedData.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.html) {
                        widget.innerHTML = data.data.html;
                    } else {
                        widget.innerHTML = '<div class="rv-no-results">' + noResultsMessage + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading recently viewed properties:', error);
                    widget.innerHTML = '<div class="rv-no-results">' + noResultsMessage + '</div>';
                });
        });
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        // Sync localStorage to cookie (in case user has history but no cookie yet)
        saveViewedProperties(getViewedProperties());

        // Track current property view
        trackCurrentProperty();

        // Load widget content - DISABLED for server-side rendering
        // loadRecentlyViewedWidget();
    });

    // Expose for external use
    window.RecentlyViewedProperties = {
        getViewed: getViewedProperties,
        addViewed: addViewedProperty,
        // reload: loadRecentlyViewedWidget // Disabled
    };

})();
