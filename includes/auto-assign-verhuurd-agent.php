<?php
/**
 * Auto-assign Verhuurd Rental Agent to Properties
 * 
 * When a property's availability-status is set to "Verhuurd" or "verhuurd",
 * this automatically assigns the property to the Verhuurd rental agent.
 * 
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Hook into term assignment to detect when availability-status changes
 * 
 * This fires whenever taxonomy terms are assigned to a post
 */
add_action( 'set_object_terms', 'auto_assign_verhuurd_rental_agent', 10, 6 );

function auto_assign_verhuurd_rental_agent( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
    // Only process if this is the avaliability-status taxonomy
    if ( $taxonomy !== 'avaliability-status' ) {
        return;
    }

    // Only process if this is a property post type
    $post_type = get_post_type( $object_id );
    if ( $post_type !== 'property' ) {
        return;
    }

    // Get the term objects to check their slugs
    $term_objects = array();
    if ( ! empty( $tt_ids ) ) {
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', $tt_id, $taxonomy );
            if ( $term && ! is_wp_error( $term ) ) {
                $term_objects[] = $term;
            }
        }
    }

    // Check if any of the terms is "verhuurd" (case-insensitive)
    $is_verhuurd = false;
    foreach ( $term_objects as $term ) {
        if ( strtolower( $term->slug ) === 'verhuurd' ) {
            $is_verhuurd = true;
            break;
        }
    }

    // If the status is not Verhuurd, we don't need to do anything
    if ( ! $is_verhuurd ) {
        return;
    }

    // Find the Verhuurd rental agent post by slug
    $verhuurd_agent = get_posts( array(
        'post_type'   => 'rental-agent',
        'name'        => 'verhuurd',
        'post_status' => 'publish',
        'numberposts' => 1,
    ) );

    if ( empty( $verhuurd_agent ) ) {
        // Log error if Verhuurd rental agent doesn't exist (only in debug mode)
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'Auto-assign Verhuurd Agent: Rental agent with slug "verhuurd" not found.' );
        }
        return;
    }

    $verhuurd_agent_id = $verhuurd_agent[0]->ID;
    
    // For ACF:
    if ( function_exists( 'update_field' ) ) {
        $result = update_field( 'rental_agent', $verhuurd_agent_id, $object_id );
        
        if ( $result ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf(
                    'Auto-assign Verhuurd Agent: Successfully assigned rental agent ID %d to property ID %d',
                    $verhuurd_agent_id,
                    $object_id
                ) );
            }
        } else {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf(
                    'Auto-assign Verhuurd Agent: Failed to assign rental agent to property ID %d',
                    $object_id
                ) );
            }
        }
    }
    
    // For SCF (Smart Custom Fields):
    // The field is stored as a serialized array in post meta
    else {
        $result = update_post_meta( $object_id, 'rental_agent', $verhuurd_agent_id );
        
        if ( $result !== false ) {
            error_log( sprintf(
                'Auto-assign Verhuurd Agent: Successfully assigned rental agent ID %d to property ID %d via SCF',
                $verhuurd_agent_id,
                $object_id
            ) );
        } else {
            error_log( sprintf(
                'Auto-assign Verhuurd Agent: Failed to assign rental agent to property ID %d via SCF',
                $object_id
            ) );
        }
    }
}

/**
 * Add a filter to prioritize property-level rental agent over project-level
 * 
 * This filter can be used in your templates to get the correct rental agent
 * Usage: $agent_id = apply_filters( 'get_property_rental_agent', 0, $property_id );
 */
add_filter( 'get_property_rental_agent', 'get_property_rental_agent_with_override', 10, 2 );

function get_property_rental_agent_with_override( $default_agent_id, $property_id ) {
    // First check if property has its own rental agent (direct override)
    $property_agent = get_field( 'rental_agent', $property_id );
    
    if ( ! empty( $property_agent ) ) {
        // Property has a direct rental agent, use it
        return is_array( $property_agent ) ? $property_agent[0] : $property_agent;
    }
    
    // Otherwise, fall back to the project's rental agent
    $parent_project = get_field( 'parent_project', $property_id );
    
    if ( ! empty( $parent_project ) ) {
        $project_id = is_array( $parent_project ) ? $parent_project[0] : $parent_project;
        $project_agent = get_field( 'rental_agent', $project_id );
        
        if ( ! empty( $project_agent ) ) {
            return is_array( $project_agent ) ? $project_agent[0] : $project_agent;
        }
    }
    
    return $default_agent_id;
}
