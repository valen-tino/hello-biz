<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Universal Shortcode to get data from a Parent Project (via SCF Relationship)
 * * Usage for Taxonomy: [parent_data taxonomy="locations"]
 * Usage for Meta Field: [parent_data key="price_range"]
 * Usage for Title: [parent_data standard="title"]
 * Usage for Link: [parent_data standard="permalink"]
 */
add_shortcode( 'parent_data', function( $atts ) {
    $atts = shortcode_atts( [
        'relation_field' => 'parent_project', // Your SCF Relationship Field Name
        'key'            => '',                // For Custom Fields (SCF)
        'taxonomy'       => '',                // For Taxonomies (e.g., 'locations', 'project_type')
        'standard'       => '',                // For standard data (title, permalink)
    ], $atts );

    // 1. Get the Parent ID
    // We use get_post_meta for speed to find the ID stored in the relationship field
    $parent_id = get_post_meta( get_the_ID(), $atts['relation_field'], true );

    // Handle array returns (if SCF returns Array instead of ID)
    if ( is_array( $parent_id ) ) {
        $parent_id = $parent_id[0] ?? false;
    }

    if ( ! $parent_id ) return ''; // Stop if no parent found

    // --- CASE A: Get Taxonomy Terms (Location, Type, etc.) ---
    if ( ! empty( $atts['taxonomy'] ) ) {
        $terms = get_the_terms( $parent_id, $atts['taxonomy'] );
        
        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            // Get just the names and join them with a comma (e.g., "Amsterdam, Center")
            $term_names = wp_list_pluck( $terms, 'name' );
            return implode( ', ', $term_names );
        }
        return '';
    }

    // --- CASE B: Get Standard Post Data (Title, Link) ---
    if ( ! empty( $atts['standard'] ) ) {
        if ( $atts['standard'] === 'title' ) return get_the_title( $parent_id );
        if ( $atts['standard'] === 'permalink' ) return get_permalink( $parent_id );
        if ( $atts['standard'] === 'id' ) return $parent_id;
    }

    // --- CASE C: Get Custom Field (SCF/ACF) ---
    if ( ! empty( $atts['key'] ) ) {
        if ( function_exists( 'get_field' ) ) {
            return get_field( $atts['key'], $parent_id );
        } else {
            return get_post_meta( $parent_id, $atts['key'], true );
        }
    }

    return '';
} );