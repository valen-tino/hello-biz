/**
 * Global Icon List Filter
 * Automatically hides Elementor Icon List items when their value is 0 or none
 */
(function() {
    'use strict';
    
    /**
     * Check if a value should be hidden
     */
    function shouldHideValue(text) {
        if (!text) return true;
        
        text = text.trim();
        
        // Hide if empty
        if (text === '') return true;
        
        // Hide if starts with "0" (covers "0", "0 bedrooms", etc.)
        if (/^0(\s|$)/.test(text)) return true;
        
        // Hide if starts with "none" (case insensitive)
        if (/^none(\s|$)/i.test(text)) return true;
        
        return false;
    }
    
    /**
     * Filter all icon list items on the page
     */
    function filterIconLists() {
        // Find all Elementor icon list items
        const iconListItems = document.querySelectorAll('.elementor-icon-list-item');
        
        iconListItems.forEach(function(item) {
            // Get the text element
            const textElement = item.querySelector('.elementor-icon-list-text');
            
            if (!textElement) return;
            
            const text = textElement.textContent;
            
            // Hide if needed
            if (shouldHideValue(text)) {
                item.style.display = 'none';
            } else {
                // Ensure it's visible (in case of dynamic content updates)
                item.style.display = '';
            }
        });
    }
    
    /**
     * Initialize the filter
     */
    function init() {
        // Run immediately
        filterIconLists();
        
        // Watch for Elementor live edits (optional, for preview mode)
        if (window.elementorFrontend) {
            window.elementorFrontend.hooks.addAction('frontend/element_ready/widget', function() {
                setTimeout(filterIconLists, 100);
            });
        }
        
        // Watch for dynamic content changes using MutationObserver
        const observer = new MutationObserver(function(mutations) {
            let shouldFilter = false;
            
            mutations.forEach(function(mutation) {
                // Check if any icon list elements were added/modified
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.classList && (
                                node.classList.contains('elementor-icon-list-item') ||
                                node.querySelector('.elementor-icon-list-item')
                            )) {
                                shouldFilter = true;
                            }
                        }
                    });
                }
            });
            
            if (shouldFilter) {
                filterIconLists();
            }
        });
        
        // Start observing the document body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM is already loaded
        init();
    }
    
})();
