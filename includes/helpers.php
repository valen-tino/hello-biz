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
 * Conditionally show/hide the availability table section on single project pages.
 * If 'has_any_properties' ACF field is checked → show the section.
 * If not checked → hide the section via Elementor element selector.
 */
add_action( 'wp_head', function() {
    if ( ! is_singular( 'project' ) ) {
        return;
    }

    $post_id        = get_the_ID();
    $has_properties = get_field( 'has_any_properties', $post_id );

    if ( ! $has_properties ) {
        echo '<style>.elementor-element.elementor-element-58916b2.avaliable-table { display: none !important; }</style>';
    }
} );