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
 * Server-side check: queries if any 'property' post is linked to the current project
 * via the 'parent_project' ACF relationship field.
 * If no linked properties found → hides the section via CSS in <head>.
 */
add_action( 'wp_head', function() {
    if ( ! is_singular( 'project' ) ) {
        return;
    }

    $project_id = get_the_ID();

    // Optimized query: only need to know if at least 1 property is linked
    // ACF relationship fields store post IDs in serialized format: "123"
    $query = new WP_Query( [
        'post_type'      => 'property',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true, // Skip counting total rows for performance
        'meta_query'     => [
            [
                'key'     => 'parent_project',
                'value'   => '"' . intval( $project_id ) . '"',
                'compare' => 'LIKE',
            ],
        ],
    ] );

    if ( ! $query->have_posts() ) {
        echo '<style>.elementor-21327 .elementor-element.elementor-element-58916b2 { display: none !important; }</style>';
    }

    wp_reset_postdata();
} );