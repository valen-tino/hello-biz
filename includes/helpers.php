<?php
/**
 * Helper functions
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper function to check if an icon list value should be hidden
 * Returns true if value is 0, '0', empty, or 'none'
 * Note: The actual filtering is now done by assets/js/icon-list-filter.js
 */
function should_hide_icon_value($value) {
    // Trim whitespace
    $value = trim($value);
    
    // Check for empty or whitespace
    if (empty($value)) {
        return true;
    }
    
    // Check for numeric zero at the start
    if (preg_match('/^0(\s|$)/', $value)) {
        return true;
    }
    
    // Check for 'none' (case-insensitive) at the start
    if (preg_match('/^none(\s|$)/i', $value)) {
        return true;
    }
    
    return false;
}

// Filter Elementor Query to only show projects where 'hide_on_project_page' is NOT checked
add_action( 'elementor/query/filter_visible_projects', function( $query ) {
    // Get existing meta queries
    $meta_query = $query->get( 'meta_query' );
    
    // Ensure it's an array if empty
    if ( ! $meta_query ) {
        $meta_query = [];
    }

    // Add the "Reverse" Logic
    $meta_query[] = array(
        'relation' => 'OR',
        array(
            'key'     => 'hide_on_project_page',
            'compare' => 'NOT EXISTS',           // Show if field never saved
        ),
        array(
            'key'     => 'hide_on_project_page',
            'value'   => '1',
            'compare' => '!=',                   // Show if field exists but is NOT '1'
        ),
    );

    // Set the query
    $query->set( 'meta_query', $meta_query );
} );

/**
 * Conditionally hide the availability table section on single project pages.
 * If a <table class="property-table"> exists inside the section, show it.
 * If no property table is found, hide the availability table section.
 * Uses window load + delay to ensure Elementor widgets have fully rendered.
 */
add_action( 'wp_footer', function() {
    if ( ! is_singular( 'project' ) ) {
        return;
    } 
    ?>
    <script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            var section = document.querySelector('.elementor-21327 .elementor-element.elementor-element-58916b2');
            if (section && !section.querySelector('table.property-table')) {
                section.style.display = 'none';
            }
        }, 5500);
    });
    </script>
    <?php
} );