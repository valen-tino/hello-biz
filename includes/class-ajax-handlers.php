<?php
/**
 * AJAX Handlers Class
 * 
 * Centralizes all AJAX handlers for the Hello Biz theme.
 * Improves security and maintainability by organizing related functionality.
 * 
 * @package HelloBiz
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Hello_Biz_Ajax_Handlers {
    
    /**
     * Initialize AJAX handlers
     */
    public static function init() {
        // Property Live Filter
        add_action( 'wp_ajax_filter_properties_live', array( __CLASS__, 'handle_property_live_filter' ) );
        add_action( 'wp_ajax_nopriv_filter_properties_live', array( __CLASS__, 'handle_property_live_filter' ) );
        
        // Recently Viewed Properties
        add_action( 'wp_ajax_get_recently_viewed_properties', array( __CLASS__, 'handle_get_recently_viewed_properties' ) );
        add_action( 'wp_ajax_nopriv_get_recently_viewed_properties', array( __CLASS__, 'handle_get_recently_viewed_properties' ) );
    }
    
    /**
     * Handle property live filter AJAX requests
     */
    public static function handle_property_live_filter() {
        // Security: Verify nonce
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'property_filter_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed' ) );
        }
        
        // Handle project_ids scope
        $scope_project_ids = self::parse_project_ids( $_POST['term_id'] ?? '' );
        
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        $posts_per_page = isset( $_POST['ppp'] ) ? intval( $_POST['ppp'] ) : 9;
        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
        
        $args = array(
            'post_type'      => 'property',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'post_status'    => 'publish',
            'meta_query'     => array( 'relation' => 'AND' ),
            'tax_query'      => array( 'relation' => 'AND' ),
        );
        
        $allowed_project_ids = array();
        $has_project_filter = false;
        
        // If specific projects are selected via scope, use them as base
        if ( ! empty( $scope_project_ids ) ) {
            $allowed_project_ids = $scope_project_ids;
        }
        
        // Process filter parameters
        foreach ( $_POST as $key => $value ) {
            if ( empty( $value ) ) {
                continue;
            }
            
            $is_multi = is_array( $value );
            $result = self::process_filter_param( $key, $value, $is_multi, $allowed_project_ids );
            
            if ( isset( $result['type'] ) ) {
                if ( $result['type'] === 'search' ) {
                    $args['s'] = $result['value'];
                } elseif ( $result['type'] === 'tax_query' ) {
                    $args['tax_query'][] = $result['value'];
                } elseif ( $result['type'] === 'meta_query' ) {
                    $args['meta_query'][] = $result['value'];
                } elseif ( $result['type'] === 'project_filter' ) {
                    $has_project_filter = true;
                    $allowed_project_ids = $result['value'];
                }
            }
        }
        
        // Add project constraint to query
        if ( ! empty( $allowed_project_ids ) ) {
            $pmq = array( 'relation' => 'OR' );
            foreach ( $allowed_project_ids as $pid ) {
                $pmq[] = array(
                    'key'     => 'parent_project',
                    'value'   => '"' . $pid . '"',
                    'compare' => 'LIKE',
                );
            }
            $args['meta_query'][] = $pmq;
        } elseif ( $has_project_filter && empty( $allowed_project_ids ) ) {
            $args['post__in'] = array( 0 );
        }
        
        // Execute query and build response
        $query = new WP_Query( $args );
        $html = '';
        $pag = '';
        
        if ( $query->have_posts() ) {
            $el = \Elementor\Plugin::instance();
            $html .= '<div class="elementor-loop-container" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">';
            
            while ( $query->have_posts() ) {
                $query->the_post();
                $html .= $el->frontend->get_builder_content_for_display( $template_id, true );
            }
            
            $html .= '</div>';
            
            $pag = paginate_links( array(
                'base'      => '%_%',
                'format'    => '?paged=%#%',
                'current'   => max( 1, $paged ),
                'total'     => $query->max_num_pages,
                'type'      => 'list',
                'prev_text' => '<',
                'next_text' => '>',
            ) );
        } else {
            $html = '<div class="no-results">Geen woningen gevonden.</div>';
        }
        
        wp_send_json_success( array(
            'html'       => $html,
            'pagination' => $pag,
            'total'      => $query->found_posts,
        ) );
        
        wp_reset_postdata();
    }
    
    /**
     * Handle recently viewed properties AJAX requests
     */
    public static function handle_get_recently_viewed_properties() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'recently_viewed_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
        }
        
        $property_ids = isset( $_POST['property_ids'] ) 
            ? json_decode( stripslashes( $_POST['property_ids'] ), true ) 
            : array();
        
        if ( empty( $property_ids ) || ! is_array( $property_ids ) ) {
            wp_send_json_success( array( 'html' => '' ) );
        }
        
        // Sanitize IDs
        $property_ids = array_filter( array_map( 'intval', $property_ids ) );
        
        if ( empty( $property_ids ) ) {
            wp_send_json_success( array( 'html' => '' ) );
        }
        
        // Query properties
        $query = new WP_Query( array(
            'post_type'      => 'property',
            'post__in'       => $property_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => count( $property_ids ),
            'post_status'    => 'publish',
        ) );
        
        $html = self::render_recently_viewed_properties( $query );
        
        wp_send_json_success( array( 'html' => $html ) );
    }
    
    /**
     * Parse project IDs from various input formats
     * 
     * @param mixed $raw Raw input
     * @return array
     */
    private static function parse_project_ids( $raw ) {
        $project_ids = array();
        
        if ( empty( $raw ) ) {
            return $project_ids;
        }
        
        if ( is_string( $raw ) && strpos( $raw, '[' ) === 0 ) {
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                $project_ids = array_map( 'intval', $decoded );
            }
        } elseif ( is_array( $raw ) ) {
            $project_ids = array_map( 'intval', $raw );
        } elseif ( is_numeric( $raw ) && intval( $raw ) > 0 ) {
            $project_ids = array( intval( $raw ) );
        }
        
        return $project_ids;
    }
    
    /**
     * Process a single filter parameter
     * 
     * @param string $key Parameter key
     * @param mixed  $value Parameter value
     * @param bool   $is_multi Whether value is array
     * @param array  $allowed_project_ids Current allowed project IDs
     * @return array|null
     */
    private static function process_filter_param( $key, $value, $is_multi, $allowed_project_ids ) {
        if ( $key === 'keyword' ) {
            return array( 'type' => 'search', 'value' => sanitize_text_field( $value ) );
        }
        
        // Handle taxonomy filters: taxonomy__, taxonomy_project__, taxonomy_single__, taxonomy_single_project__
        if ( preg_match( '/^taxonomy(_single)?(_project)?__(.+)$/', $key, $matches ) ) {
            $is_project_filter = ! empty( $matches[2] ); // _project suffix present
            $slug = $matches[3]; // The taxonomy slug is in capture group 3
            
            if ( $is_project_filter ) {
                // This is a PROJECT taxonomy - query projects first, then filter properties
                $project_args = array(
                    'post_type'      => 'project',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => $slug,
                            'field'    => 'slug',
                            'terms'    => $value,
                        ),
                    ),
                );
                
                // If we already have allowed project IDs, constrain to those
                if ( ! empty( $allowed_project_ids ) ) {
                    $project_args['post__in'] = $allowed_project_ids;
                }
                
                $matching_project_ids = get_posts( $project_args );
                
                return array(
                    'type'  => 'project_filter',
                    'value' => $matching_project_ids,
                );
            } else {
                // Regular property taxonomy
                return array(
                    'type'  => 'tax_query',
                    'value' => array(
                        'taxonomy' => $slug,
                        'field'    => 'slug',
                        'terms'    => $value,
                    ),
                );
            }
        }
        
        if ( strpos( $key, 'meta_numeric__' ) === 0 ) {
            $slug = str_replace( 'meta_numeric__', '', $key );
            return array(
                'type'  => 'meta_query',
                'value' => array(
                    'key'     => $slug,
                    'value'   => $value,
                    'compare' => $is_multi ? 'IN' : '>=',
                    'type'    => 'NUMERIC',
                ),
            );
        }
        
        if ( strpos( $key, 'meta_range__' ) === 0 ) {
            $val = $is_multi ? end( $value ) : $value;
            $slug = str_replace( 'meta_range__', '', $key );
            $r = explode( '-', $val );
            
            if ( count( $r ) == 2 ) {
                return array(
                    'type'  => 'meta_query',
                    'value' => array(
                        'key'     => $slug,
                        'value'   => $r,
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    ),
                );
            } else {
                $min = filter_var( $r[0], FILTER_SANITIZE_NUMBER_INT );
                return array(
                    'type'  => 'meta_query',
                    'value' => array(
                        'key'     => $slug,
                        'value'   => $min,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    ),
                );
            }
        }
        
        return null;
    }
    
    /**
     * Render recently viewed properties HTML
     * 
     * @param WP_Query $query
     * @return string
     */
    private static function render_recently_viewed_properties( $query ) {
        $html = '';
        
        if ( ! $query->have_posts() ) {
            return $html;
        }
        
        $html .= '<div class="rv-properties-list">';
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Get featured image
            $image_url = get_the_post_thumbnail_url( $post_id, 'medium' );
            if ( ! $image_url ) {
                $image_url = \Elementor\Utils::get_placeholder_image_src();
            }
            
            // Get ACF fields
            $price = '';
            $surface = '';
            $bedrooms = '';
            $bathrooms = '';
            
            if ( function_exists( 'get_field' ) ) {
                $price = get_field( 'huurprijs', $post_id );
                if ( ! $price ) {
                    $price = get_field( 'koopprijs', $post_id );
                }
                $surface = get_field( 'woonoppervlakte', $post_id );
                $bedrooms = get_field( 'slaapkamers', $post_id );
                $bathrooms = get_field( 'badkamers', $post_id );
            }
            
            // Get location
            $location = '';
            $terms = get_the_terms( $post_id, 'property-city' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $location = $terms[0]->name;
            }
            
            // Format price
            $formatted_price = '';
            if ( $price ) {
                $formatted_price = '€' . number_format( floatval( $price ), 0, ',', '.' ) . ' / P.M. EX.';
            }
            
            $html .= self::render_property_card( $post_id, $image_url, $location, $formatted_price, $surface, $bedrooms, $bathrooms );
        }
        
        $html .= '</div>';
        wp_reset_postdata();
        
        return $html;
    }
    
    /**
     * Render a single property card
     */
    private static function render_property_card( $post_id, $image_url, $location, $formatted_price, $surface, $bedrooms, $bathrooms ) {
        $html = '<div class="rv-property-card">';
        $html .= '<a href="' . esc_url( get_permalink() ) . '">';
        $html .= '<div class="rv-property-image">';
        $html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '">';
        $html .= '</div>';
        $html .= '<div class="rv-property-content">';
        $html .= '<h4 class="rv-property-title">' . esc_html( get_the_title() ) . '</h4>';
        
        if ( $location ) {
            $html .= '<div class="rv-property-location">';
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
            $html .= '<span>' . esc_html( strtoupper( $location ) ) . '</span>';
            $html .= '</div>';
        }
        
        if ( $formatted_price ) {
            $html .= '<div class="rv-property-price">' . esc_html( $formatted_price ) . '</div>';
        }
        
        $html .= '<div class="rv-property-details">';
        if ( $surface ) {
            $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg> ' . esc_html( $surface ) . ' m²</span>';
        }
        if ( $bedrooms ) {
            $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> ' . esc_html( $bedrooms ) . '</span>';
        }
        if ( $bathrooms ) {
            $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg> ' . esc_html( $bathrooms ) . '</span>';
        }
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '<div class="rv-property-buttons"></div>';
        $html .= '</a>';
        $html .= '</div>';
        
        return $html;
    }
}

// Initialize AJAX handlers
Hello_Biz_Ajax_Handlers::init();
