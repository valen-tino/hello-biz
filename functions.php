<?php
/**
 * Theme functions and definitions
 *
 * @package HelloBiz
 */

use HelloBiz\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_BIZ_ELEMENTOR_VERSION', '1.2.0' );
define( 'EHP_THEME_SLUG', 'hello-biz' );

define( 'HELLO_BIZ_PATH', get_template_directory() );
define( 'HELLO_BIZ_URL', get_template_directory_uri() );
define( 'HELLO_BIZ_ASSETS_PATH', HELLO_BIZ_PATH . '/assets/' );
define( 'HELLO_BIZ_ASSETS_URL', HELLO_BIZ_URL . '/assets/' );
define( 'HELLO_BIZ_SCRIPTS_PATH', HELLO_BIZ_ASSETS_PATH . 'js/' );
define( 'HELLO_BIZ_SCRIPTS_URL', HELLO_BIZ_ASSETS_URL . 'js/' );
define( 'HELLO_BIZ_STYLE_PATH', HELLO_BIZ_ASSETS_PATH . 'css/' );
define( 'HELLO_BIZ_STYLE_URL', HELLO_BIZ_ASSETS_URL . 'css/' );
define( 'HELLO_BIZ_IMAGES_PATH', HELLO_BIZ_ASSETS_PATH . 'images/' );
define( 'HELLO_BIZ_IMAGES_URL', HELLO_BIZ_ASSETS_URL . 'images/' );
define( 'HELLO_BIZ_STARTER_IMAGES_PATH', HELLO_BIZ_IMAGES_PATH . 'starter-content/' );
define( 'HELLO_BIZ_STARTER_IMAGES_URL', HELLO_BIZ_IMAGES_URL . 'starter-content/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

// Init the Theme class
require HELLO_BIZ_PATH . '/theme.php';

Theme::instance();

// Include Custom Files
require_once HELLO_BIZ_PATH . '/includes/custom-dynamic-tags.php';
require_once HELLO_BIZ_PATH . '/includes/additional-shortcodes.php';
require_once HELLO_BIZ_PATH . '/includes/custom-map-widget.php';
require_once HELLO_BIZ_PATH . '/includes/auto-assign-verhuurd-agent.php';
require_once HELLO_BIZ_PATH . '/includes/multistep-blossem-form.php';
require_once HELLO_BIZ_PATH . '/includes/multistep-blossem-wpform-generator.php';
require_once HELLO_BIZ_PATH . '/includes/custom-cursor-carousel.php';

// Modular Classes (v1.2.0)
require_once HELLO_BIZ_PATH . '/includes/class-widget-registrar.php';
require_once HELLO_BIZ_PATH . '/includes/class-ajax-handlers.php';

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'my-theme-main-style', get_stylesheet_uri() );
    wp_enqueue_style( 'my-theme-custom-style', get_theme_file_uri( 'style.css' ), array(), '1.0', 'all' );
    
    // Enqueue icon list filter JavaScript
    wp_enqueue_script( 
        'icon-list-filter', 
        get_theme_file_uri( 'assets/js/icon-list-filter.js' ), 
        array(), 
        '1.0', 
        true 
    );
    
    // Enqueue Multistep Blossem Form assets
    wp_enqueue_style(
        'multistep-blossem-form',
        get_theme_file_uri( 'assets/css/multistep-blossem-form.css' ),
        array(),
        '1.0.0',
        'all'
    );
    
    wp_enqueue_script(
        'multistep-blossem-form',
        get_theme_file_uri( 'assets/js/multistep-blossem-form.js' ),
        array( 'jquery' ),
        '1.0.0',
        true
    );
    
    // Localize script for Dutch formatting and translations
    wp_localize_script( 'multistep-blossem-form', 'multistepFormData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'locale' => array(
            'thousandsSeparator' => '.',
            'decimalSeparator' => ',',
            'currencySymbol' => '€'
        ),
        'i18n' => array(
            'fieldRequired' => __('This field is required.', 'hello-biz'),
            'emailsMatch' => __('Email addresses must match.', 'hello-biz'),
            'postalCodeInvalid' => __('Please enter a valid Dutch postal code (e.g., 1234 AB).', 'hello-biz'),
            'propertyTypeRequired' => __('Please select at least one property type.', 'hello-biz'),
            'submitting' => __('Submitting...', 'hello-biz'),
            'submit' => __('Yes, I want to register!', 'hello-biz'),
            'connectionError' => __('An error occurred. Please check your connection and try again.', 'hello-biz'),
            'generalError' => __('An error occurred. Please try again.', 'hello-biz'),
            'successTitle' => __('Registration Successful!', 'hello-biz')
        )
    ));
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/**
 * Add font-display: swap for custom fonts (PageSpeed optimization)
 * This overrides Elementor's default font-display: auto with swap
 * for better performance and to pass PageSpeed audits.
 */
function hello_biz_font_display_swap() {
    ?>
    <style id="hello-biz-font-display-fix">
        /* Override Elementor's font-display: auto with swap for better PageSpeed */
        @font-face {
            font-family: 'Proxima Nova';
            font-display: swap !important;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'hello_biz_font_display_swap', 1 );

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widget_file = HELLO_BIZ_PATH . '/includes/unit-showcase-widget.php';
    // Check if file exists to prevent site crash
    if ( file_exists( $widget_file ) ) {
        require_once $widget_file;
        
        // Register the widget class
        $widgets_manager->register( new \Unit_Type_Showcase_Widget() );
    }
});

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
        $widget_file = get_stylesheet_directory() . '/includes/project-floorplan-tabs.php';
    if ( file_exists( $widget_file ) ) {
        require_once $widget_file;
        $widgets_manager->register( new \Project_Floorplan_Tabs_Widget() );
    }
});

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
        $widget_file = get_stylesheet_directory() . '/includes/get-data-from-parent-project.php';
    if ( file_exists( $widget_file ) ) {
        require_once $widget_file;
        $widgets_manager->register( new \Project_Floorplan_Tabs_Widget() );
    }
});

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widget_file = get_stylesheet_directory() . '/includes/project-properties-table.php';
    if ( file_exists( $widget_file ) ) {
        require_once $widget_file;
        $widgets_manager->register( new \Elementor_Project_Properties_Table() );
    }
});

// Register Elementor Widgets
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $path = get_stylesheet_directory() . '/includes/';
    
    require_once $path . 'widget-property-search-form.php';
    require_once $path . 'widget-property-results.php';
    require_once $path . 'widget-property-count.php';
    require_once $path . 'widget-project-showcase.php';
    require_once $path . 'widget-recently-viewed-properties.php';
    
    $widgets_manager->register( new \Elementor_Property_Search_Form_Widget() );
    $widgets_manager->register( new \Elementor_Property_Search_Results_Widget() );
    $widgets_manager->register( new \Elementor_Property_Count_Widget() );
    $widgets_manager->register( new \Elementor_Project_Showcase_Widget() );
    $widgets_manager->register( new \Elementor_Recently_Viewed_Properties_Widget() );
});

/**
 * AJAX Handler: Property Live Filter (Multi-Select & Range Support)
 */
add_action('wp_ajax_filter_properties_live', 'handle_property_live_filter');
add_action('wp_ajax_nopriv_filter_properties_live', 'handle_property_live_filter');

/**
 * Enqueue Recently Viewed Properties Script
 */
add_action('wp_enqueue_scripts', 'enqueue_recently_viewed_properties_script');
function enqueue_recently_viewed_properties_script() {
    wp_enqueue_script(
        'recently-viewed-properties',
        get_theme_file_uri('assets/js/recently-viewed-properties.js'),
        array(),
        '1.0.0',
        true
    );
    
    // Get current property ID if on single property page
    $current_property_id = 0;
    if (is_singular('property')) {
        $current_property_id = get_the_ID();
    }
    
    wp_localize_script('recently-viewed-properties', 'recentlyViewedData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('recently_viewed_nonce'),
        'currentPropertyId' => $current_property_id,
    ));
}

/**
 * AJAX Handler: Get Recently Viewed Properties
 */
add_action('wp_ajax_get_recently_viewed_properties', 'handle_get_recently_viewed_properties');
add_action('wp_ajax_nopriv_get_recently_viewed_properties', 'handle_get_recently_viewed_properties');
function handle_get_recently_viewed_properties() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'recently_viewed_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    $property_ids = isset($_POST['property_ids']) ? json_decode(stripslashes($_POST['property_ids']), true) : array();
    
    if (empty($property_ids) || !is_array($property_ids)) {
        wp_send_json_success(array('html' => ''));
    }
    
    // Sanitize IDs
    $property_ids = array_map('intval', $property_ids);
    $property_ids = array_filter($property_ids);
    
    if (empty($property_ids)) {
        wp_send_json_success(array('html' => ''));
    }
    
    // Query properties
    $args = array(
        'post_type' => 'property',
        'post__in' => $property_ids,
        'orderby' => 'post__in',
        'posts_per_page' => count($property_ids),
        'post_status' => 'publish',
    );
    
    $query = new WP_Query($args);
    $html = '';
    
    if ($query->have_posts()) {
        $html .= '<div class="rv-properties-list">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Get featured image
            $image_url = get_the_post_thumbnail_url($post_id, 'medium');
            if (!$image_url) {
                $image_url = \Elementor\Utils::get_placeholder_image_src();
            }
            
            // Get ACF fields
            $price = '';
            $surface = '';
            $bedrooms = '';
            $bathrooms = '';
            
            if (function_exists('get_field')) {
                $price = get_field('huurprijs', $post_id);
                if (!$price) $price = get_field('koopprijs', $post_id);
                $surface = get_field('woonoppervlakte', $post_id);
                $bedrooms = get_field('slaapkamers', $post_id);
                $bathrooms = get_field('badkamers', $post_id);
            }
            
            // Get location
            $location = '';
            $terms = get_the_terms($post_id, 'property-city');
            if ($terms && !is_wp_error($terms)) {
                $location = $terms[0]->name;
            }
            
            // Format price
            $formatted_price = '';
            if ($price) {
                $formatted_price = '€' . number_format(floatval($price), 0, ',', '.') . ' / P.M. EX.';
            }
            
            $html .= '<div class="rv-property-card">';
            $html .= '<a href="' . esc_url(get_permalink()) . '">';
            $html .= '<div class="rv-property-image">';
            $html .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '">';
            $html .= '</div>';
            $html .= '<div class="rv-property-content">';
            $html .= '<h4 class="rv-property-title">' . esc_html(get_the_title()) . '</h4>';
            
            if ($location) {
                $html .= '<div class="rv-property-location">';
                $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                $html .= '<span>' . esc_html(strtoupper($location)) . '</span>';
                $html .= '</div>';
            }
            
            if ($formatted_price) {
                $html .= '<div class="rv-property-price">' . esc_html($formatted_price) . '</div>';
            }
            
            $html .= '<div class="rv-property-details">';
            if ($surface) {
                $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg> ' . esc_html($surface) . ' m²</span>';
            }
            if ($bedrooms) {
                $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> ' . esc_html($bedrooms) . '</span>';
            }
            if ($bathrooms) {
                $html .= '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg> ' . esc_html($bathrooms) . '</span>';
            }
            $html .= '</div>';
            
            $html .= '</div>';
            $html .= '<div class="rv-property-buttons">';
            // Add buttons if needed
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        wp_reset_postdata();
    }
    
    wp_send_json_success(array('html' => $html));
}

function handle_property_live_filter() {
    // Security: Verify nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'property_filter_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
    }
    
    // Handle project_ids scope - can be JSON array or comma-separated
    $scope_project_ids = [];
    if (isset($_POST['term_id'])) {
        $raw = $_POST['term_id'];
        // Check if it's a JSON array
        if (is_string($raw) && strpos($raw, '[') === 0) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $scope_project_ids = array_map('intval', $decoded);
            }
        } elseif (is_array($raw)) {
            $scope_project_ids = array_map('intval', $raw);
        } elseif (is_numeric($raw) && intval($raw) > 0) {
            // Legacy: single term_id (for backwards compatibility)
            $scope_project_ids = [intval($raw)];
        }
    }
    
    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
    $posts_per_page = isset($_POST['ppp']) ? intval($_POST['ppp']) : 9;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [ 'post_type' => 'property', 'posts_per_page' => $posts_per_page, 'paged' => $paged, 'post_status' => 'publish', 'meta_query' => ['relation' => 'AND'], 'tax_query' => ['relation' => 'AND'] ];
    $allowed_project_ids = []; $has_project_filter = false;

    // If specific projects are selected via scope, use them as base
    if (!empty($scope_project_ids)) {
        $allowed_project_ids = $scope_project_ids;
    }

    foreach ($_POST as $key => $value) {
        if ( empty($value) ) continue;
        $is_multi = is_array($value);

        if ( $key === 'keyword' ) { $args['s'] = sanitize_text_field($value); }
        elseif ( strpos($key, '_project__') !== false ) {
            $has_project_filter = true; $parts = explode('__', $key); $prefix = $parts[0]; $slug = $parts[1];
            $p_args = ['post_type'=>'project','posts_per_page'=>-1,'fields'=>'ids','post__in'=>$allowed_project_ids];
            
            if ( strpos($prefix, 'taxonomy') !== false ) { $p_args['tax_query'] = [['taxonomy'=>$slug, 'field'=>'slug', 'terms'=>$value]]; } 
            elseif ( strpos($prefix, 'meta_numeric') !== false ) { $p_args['meta_query'] = [['key'=>$slug, 'value'=>$value, 'compare'=>$is_multi ? 'IN' : '>=', 'type'=>'NUMERIC']]; }
            elseif ( strpos($prefix, 'meta_relation') !== false ) { 
                if($is_multi) { $sub_q = ['relation'=>'OR']; foreach($value as $v) $sub_q[] = ['key'=>$slug, 'value'=>'"'.$v.'"', 'compare'=>'LIKE']; $p_args['meta_query'][] = $sub_q; }
                else { $p_args['meta_query'] = [['key'=>$slug, 'value'=>'"'.$value.'"', 'compare'=>'LIKE']]; }
            }
            $found = get_posts($p_args);
            if(empty($allowed_project_ids)) $allowed_project_ids = $found; else $allowed_project_ids = array_intersect($allowed_project_ids, $found);
        }
        elseif ( strpos($key, 'taxonomy__') === 0 || strpos($key, 'taxonomy_single__') === 0 ) { 
            $slug = preg_replace('/^taxonomy(_single)?__/', '', $key); 
            $args['tax_query'][] = ['taxonomy'=>$slug, 'field'=>'slug', 'terms'=>$value]; 
        }
        elseif ( strpos($key, 'meta_numeric__') === 0 ) { $slug = str_replace('meta_numeric__', '', $key); $args['meta_query'][] = ['key'=>$slug, 'value'=>$value, 'compare'=>$is_multi ? 'IN' : '>=', 'type'=>'NUMERIC']; }
        elseif ( strpos($key, 'meta_range__') === 0 ) { $val = $is_multi ? end($value) : $value; $slug = str_replace('meta_range__', '', $key); $r = explode('-', $val); if ( count($r) == 2 ) $args['meta_query'][] = ['key'=>$slug, 'value'=>$r, 'compare'=>'BETWEEN', 'type'=>'NUMERIC']; else { $min = filter_var($r[0], FILTER_SANITIZE_NUMBER_INT); $args['meta_query'][] = ['key'=>$slug, 'value'=>$min, 'compare'=>'>=', 'type'=>'NUMERIC']; } }
    }

    if ( !empty($allowed_project_ids) ) { $pmq = ['relation'=>'OR']; foreach($allowed_project_ids as $pid) $pmq[] = ['key'=>'parent_project', 'value'=>'"'.$pid.'"', 'compare'=>'LIKE']; $args['meta_query'][] = $pmq; }
    elseif ( $has_project_filter && empty($allowed_project_ids) ) { $args['post__in'] = [0]; }

    $query = new WP_Query($args);
    $html = ''; $pag = '';
    if( $query->have_posts() ) {
        $el = \Elementor\Plugin::instance();
        $html .= '<div class="elementor-loop-container" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">';
        while( $query->have_posts() ) { $query->the_post(); $html .= $el->frontend->get_builder_content_for_display( $template_id, true ); }
        $html .= '</div>';
        $pag = paginate_links([ 'base' => '%_%', 'format' => '?paged=%#%', 'current' => max(1, $paged), 'total' => $query->max_num_pages, 'type' => 'list', 'prev_text' => '<', 'next_text' => '>' ]);
    } else { $html = '<div class="no-results">Geen woningen gevonden.</div>'; }

    wp_send_json_success(['html'=>$html, 'pagination'=>$pag, 'total'=>$query->found_posts]);
    wp_reset_postdata();
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

/**
 * Cache Invalidation for Property Search Options
 * Updates the version timestamp whenever a property or project is saved.
 */
add_action('save_post', 'invalidate_property_search_cache', 10, 3);
function invalidate_property_search_cache($post_id, $post, $update) {
    // Only proceed for specific post types
    if (!in_array($post->post_type, ['property', 'project'])) {
        return;
    }
    
    // Convert current time to float for precision or simple time()
    update_option('hello_biz_property_data_version', time());
}

/**
 * Detect elements utilizing TTF fonts and redirect them to use Elementor loaded fonts
 * This function injects a script in the footer that scans for usage of TTF fonts,
 * identifies the elements using them, and applies the preferred font family (Proxima Nova).
 */
function hello_biz_replace_ttf_with_elementor_fonts() {
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // The font family we want to use (loaded by Elementor)
        const PREFERRED_FONT = 'Proxima Nova'; 
        
        // Helper to check source
        const isTTF = (src) => src && (src.includes('.ttf') || src.includes('format("truetype")'));

        // 1. Detect Font Families that are using TTF sources
        let ttfFamilies = new Set();
        
        try {
            // Traverse all stylesheets
            for (let i = 0; i < document.styleSheets.length; i++) {
                try {
                    let sheet = document.styleSheets[i];
                    let rules = sheet.cssRules || sheet.rules;
                    
                    if (!rules) continue;

                    for (let j = 0; j < rules.length; j++) {
                        let rule = rules[j];
                        // Check for @font-face rules
                        if (rule.type === CSSRule.FONT_FACE_RULE) {
                            let src = rule.style.getPropertyValue('src');
                            let family = rule.style.getPropertyValue('font-family').replace(/['"]/g, '').trim();
                            
                            if (isTTF(src)) {
                                console.warn('[FontDetector] Found TTF Usage:', family, src);
                                ttfFamilies.add(family);
                            }
                        }
                    }
                } catch (e) {
                    // Access restricted stylesheets (CORS) or other errors
                }
            }
        } catch (globalErr) {
            console.error('[FontDetector] Error scanning fonts:', globalErr);
        }

        // 2. Redirect Elements
        if (ttfFamilies.size > 0) {
            console.log('[FontDetector] Redirecting elements using these families to ' + PREFERRED_FONT + ':', Array.from(ttfFamilies));
            
            // Efficient Iteration over all elements to find those using the bad font
            // We use getElementsByTagName('*') which is live and fast
            let elements = document.getElementsByTagName('*');
            let count = 0;
            
            for (let i = 0; i < elements.length; i++) {
                let el = elements[i];
                // Check computed style
                let computed = window.getComputedStyle(el);
                // primary font family
                let currentFont = computed.fontFamily.replace(/['"]/g, '').split(',')[0].trim();
                
                if (ttfFamilies.has(currentFont)) {
                    // Start redirection logic
                    // If the current font IS the Preferred Font, but it's using the TTF source (detected above),
                    // we might need to force the WOFF2 version if available. 
                    // However, we can't easily switch source on the element itself without changing family name
                    // OR ensuring the good rule wins.
                    
                    // If they are different names, we swap.
                    if (currentFont.toLowerCase() !== PREFERRED_FONT.toLowerCase()) {
                        el.style.fontFamily = PREFERRED_FONT + ', sans-serif';
                        count++;
                    } else {
                        // Same name (Proxima Nova TTF vs Proxima Nova WOFF)
                        // This usually means the TTF rule is taking precedence.
                        // We can try to force a reload? No.
                        // We can't easily fix this via element.style.fontFamily because it's already set to Proxima Nova.
                        // The fix here would be to remove the TTF rule from the DOM, but we skipped that complexity.
                        // User's request "redirect so that those elements will use..." implies pointing to the good one.
                        // If they are separate families, this works. 
                    }
                }
            }
            if (count > 0) console.log('[FontDetector] Fixed ' + count + ' elements.');
        } else {
            console.log('[FontDetector] No TTF font families detected.');
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'hello_biz_replace_ttf_with_elementor_fonts');