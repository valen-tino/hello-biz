<?php 

// Added Custom Functions by Valentino
function add_categories_to_pages() {
	register_taxonomy_for_object_type('category', 'page');
}
add_action('init', 'add_categories_to_pages');

// Secure Blossem Email Shortcode
function secure_blossem_email_shortcode( $atts ) {
    // Setup attributes (allow user to change text and class)
    $args = shortcode_atts( array(
        'text'  => 'info@blossemgroup.nl', // Default
        'class' => 'email-link',         // Optional CSS class (e.g., for buttons)
    ), $atts );

    // Define the link parts separately to hide them from scrapers
    $user = 'info';
    $domain = 'blossemgroup.nl';
    $query = '?subject=Contactaanvraag%20%5Bvia%20website%5D&body=Voer%20hier%20uw%20bericht%20in...';
    
    // Create a unique ID for this link instance
    $link_id = 'secure-link-' . uniqid();

    // Build the output
    // Placeholder Span
    $output = '<span id="' . esc_attr( $link_id ) . '">';
    
    // Add the JavaScript to inject the link
    $output .= '<script type="text/javascript">';
    $output .= '(function() {';
    $output .= 'var u = "' . $user . '";';
    $output .= 'var d = "' . $domain . '";';
    $output .= 'var q = "' . $query . '";';
    $output .= 'var t = "' . esc_js( $args['text'] ) . '";';
    $output .= 'var c = "' . esc_js( $args['class'] ) . '";';
    $output .= 'var element = document.getElementById("' . $link_id . '");';
    
    // Inject the anchor tag into the placeholder
    $output .= 'element.innerHTML = "<a href=\'mailto:" + u + "@" + d + q + "\' class=\'" + c + "\'>" + t + "</a>";';
    $output .= '})();';
    $output .= '</script>';
    
    // Fallback for users without Javascript Running
    $output .= '<noscript>Email: ' . $user . ' [at] ' . $domain . '</noscript>';
    $output .= '</span>';

    return $output;
}
add_shortcode( 'secure_email', 'secure_blossem_email_shortcode' );

// Enable ACF (SCF) Shortcode across entire site
function enable_acf_shortcode() {
    acf_update_setting( 'enable_shortcode', true );
}
add_action( 'acf/init', 'enable_acf_shortcode' );

function display_acf_taxonomy_terms_shortcode($atts) {
    // Get attributes
    $atts = shortcode_atts(
        array(
            'field' => '', // Your ACF field name (e.g., 'product_tags')
            'taxonomy' => '', // Your taxonomy slug (e.g., 'product_tag')
            'list_type' => 'ul', // 'ul', 'ol', or '' for comma-separated
        ),
        $atts,
        'acf_taxonomy_terms' // The shortcode name
    );

    // Check if field and taxonomy are provided
    if (empty($atts['field']) || empty($atts['taxonomy'])) {
        return 'Please specify the ACF field and taxonomy slug.';
    }

    // Get the terms from the current post (or specified post_id if added)
    $terms = get_field($atts['field']); // This gets the array of terms

    // If no terms, return nothing
    if (empty($terms)) {
        return '';
    }

    $output = '';
    // Start the list if specified
    if ($atts['list_type'] == 'ul') {
        $output .= '<ul>';
    } elseif ($atts['list_type'] == 'ol') {
        $output .= '<ol>';
    }

    // Loop through terms and add them to the output
    foreach ($terms as $term) {
        if ($atts['list_type'] == 'ul' || $atts['list_type'] == 'ol') {
            $output .= '<li>' . esc_html($term->name) . '</li>';
        } else {
            // Comma separated
            $output .= esc_html($term->name) . ', ';
        }
    }

    // Close the list if specified
    if ($atts['list_type'] == 'ul') {
        $output .= '</ul>';
    } elseif ($atts['list_type'] == 'ol') {
        $output .= '</ol>';
    }

    // Remove trailing comma for comma-separated lists
    if ($atts['list_type'] == '') {
        $output = rtrim($output, ', ');
    }

    return $output;
}
add_shortcode('acf_taxonomy_terms', 'display_acf_taxonomy_terms_shortcode');

add_filter('acf/format_value/name=price', 'custom_format_european_number', 10, 3);
function custom_format_european_number($value, $post_id, $field) {
    if ($field['name'] === 'price') {
        if (is_numeric($value)) {
            $formatted = '€' . number_format($value, 2, ',', '.');
            
            // Check if property is Verhuurd - add strikethrough
            $availability_terms = get_the_terms($post_id, 'avaliability-status');
            $is_verhuurd = false;
            
            if (!empty($availability_terms) && !is_wp_error($availability_terms)) {
                foreach ($availability_terms as $term) {
                    if (strtolower($term->slug) === 'verhuurd') {
                        $is_verhuurd = true;
                        break;
                    }
                }
            }
            
            if ($is_verhuurd) {
                return '<span style="text-decoration: line-through; opacity: 0.7;">' . $formatted . '</span>';
            }
            
            return $formatted;
        }
    }
    return $value;
}

/**
 * Property Status Tags Shortcode
 * 
 * Displays availability-status badge with dynamic colors and housing-type tag from parent project.
 * 
 * Usage: [property_status_tags]
 * 
 * Colors:
 * - beschikbaar: green bg (#22c55e) + white text
 * - coming-soon: yellow bg (#eab308) + black text
 * - verhuurd: red bg (#ef4444) + white text
 * - housing-type: black bg (#000) + white text
 */
add_shortcode('property_status_tags', 'property_status_tags_shortcode');
function property_status_tags_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_housing_type' => 'yes',
        'show_availability' => 'yes',
    ), $atts);
    
    $current_post_id = get_the_ID();
    $output = '<div class="property-status-tags" style="display: flex; flex-wrap: wrap; gap: 8px;">';
    
    // 1. Get Housing Type from Parent Project
    if ($atts['show_housing_type'] === 'yes') {
        // Get parent project
        $parent_project = get_field('parent_project', $current_post_id);
        
        if (!empty($parent_project)) {
            $project_id = is_array($parent_project) ? $parent_project[0] : $parent_project;
            if (is_object($project_id)) {
                $project_id = $project_id->ID;
            }
            
            // Get housing-type taxonomy from project
            $housing_types = get_the_terms($project_id, 'housing-type');
            
            if (!empty($housing_types) && !is_wp_error($housing_types)) {
                foreach ($housing_types as $housing_type) {
                    $output .= '<span class="status-tag housing-type-tag" style="
                        display: inline-block;
                        padding: 6px 12px;
                        border-radius: 4px;
                        font-size: 12px;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        background-color: #000000;
                        color: #ffffff;
                    ">' . esc_html($housing_type->name) . '</span>';
                }
            }
        }
    }
    
    // 2. Get Availability Status
    if ($atts['show_availability'] === 'yes') {
        $availability_terms = get_the_terms($current_post_id, 'avaliability-status');
        
        if (!empty($availability_terms) && !is_wp_error($availability_terms)) {
            foreach ($availability_terms as $term) {
                $slug = strtolower($term->slug);
                
                // Determine colors based on status
                switch ($slug) {
                    case 'beschikbaar':
                        $bg_color = '#15803d'; // Green (darker for contrast ratio 7.3:1)
                        $text_color = '#ffffff';
                        break;
                    case 'coming-soon':
                        $bg_color = '#a16207'; // Yellow/Amber (darker for contrast ratio 5.5:1)
                        $text_color = '#ffffff';
                        break;
                    case 'verhuurd':
                        $bg_color = '#dc2626'; // Red (slightly darker for better contrast)
                        $text_color = '#ffffff';
                        break;
                    default:
                        $bg_color = '#6b7280'; // Gray fallback
                        $text_color = '#ffffff';
                }
                
                $output .= '<span class="status-tag availability-tag availability-' . esc_attr($slug) . '" style="
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 500;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    background-color: ' . $bg_color . ';
                    color: ' . $text_color . ';
                ">' . esc_html($term->name) . '</span>';
            }
        }
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Individual Availability Status Tag Shortcode
 * Usage: [availability_status_tag]
 */
add_shortcode('availability_status_tag', 'availability_status_tag_shortcode');
function availability_status_tag_shortcode($atts) {
    $current_post_id = get_the_ID();
    $availability_terms = get_the_terms($current_post_id, 'avaliability-status');
    
    if (empty($availability_terms) || is_wp_error($availability_terms)) {
        return '';
    }
    
    $term = $availability_terms[0];
    $slug = strtolower($term->slug);
    
    switch ($slug) {
        case 'beschikbaar':
            $bg_color = '#15803d'; // Green (darker for contrast ratio 7.3:1)
            $text_color = '#ffffff';
            break;
        case 'coming-soon':
            $bg_color = '#a16207'; // Yellow/Amber (darker for contrast ratio 5.5:1)
            $text_color = '#ffffff';
            break;
        case 'verhuurd':
            $bg_color = '#dc2626'; // Red (slightly darker for better contrast)
            $text_color = '#ffffff';
            break;
        default:
            $bg_color = '#6b7280';
            $text_color = '#ffffff';
    }
    
    return '<span class="availability-tag availability-' . esc_attr($slug) . '" style="
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: ' . $bg_color . ';
        color: ' . $text_color . ';
    ">' . esc_html($term->name) . '</span>';
}

/**
 * Housing Type Tag Shortcode (from parent project)
 * Usage: [housing_type_tag]
 */
add_shortcode('housing_type_tag', 'housing_type_tag_shortcode');
function housing_type_tag_shortcode($atts) {
    $current_post_id = get_the_ID();
    
    // Get parent project
    $parent_project = get_field('parent_project', $current_post_id);
    
    if (empty($parent_project)) {
        return '';
    }
    
    $project_id = is_array($parent_project) ? $parent_project[0] : $parent_project;
    if (is_object($project_id)) {
        $project_id = $project_id->ID;
    }
    
    // Get housing-type taxonomy from project
    $housing_types = get_the_terms($project_id, 'housing-type');
    
    if (empty($housing_types) || is_wp_error($housing_types)) {
        return '';
    }
    
    $output = '';
    foreach ($housing_types as $housing_type) {
        $output .= '<span class="housing-type-tag" style="
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #000000;
            color: #ffffff;
        ">' . esc_html($housing_type->name) . '</span>';
    }
    
    return $output;
}

/**
 * Location Taxonomy Shortcode
 * Forces retrieval of location taxonomy terms for display in loop templates
 * 
 * Usage: 
 * [location_taxonomy] - Get location from current post
 * [location_taxonomy post_id="123"] - Get location from specific post
 * [location_taxonomy list_type="ul"] - Display as unordered list
 * [location_taxonomy list_type="ol"] - Display as ordered list
 * [location_taxonomy list_type=""] - Display comma-separated (default)
 * [location_taxonomy show_link="yes"] - Display location as clickable link to taxonomy archive
 */
add_shortcode('location_taxonomy', 'location_taxonomy_shortcode');
function location_taxonomy_shortcode($atts) {
    // Get attributes
    $atts = shortcode_atts(
        array(
            'post_id' => null, // Optional post ID
            'list_type' => '', // 'ul', 'ol', or '' for comma-separated
            'show_link' => 'no', // 'yes' to show as clickable links
            'separator' => ', ', // Separator for comma-separated list
        ),
        $atts,
        'location_taxonomy'
    );
    
    // Determine which post to get taxonomy from
    $post_id = $atts['post_id'] ? intval($atts['post_id']) : get_the_ID();
    
    if (!$post_id) {
        return '';
    }
    
    // Force get the location taxonomy terms
    $location_terms = get_the_terms($post_id, 'location');
    
    // If no terms found, return empty
    if (empty($location_terms) || is_wp_error($location_terms)) {
        return '';
    }
    
    $output = '';
    
    // Start the list if specified
    if ($atts['list_type'] == 'ul') {
        $output .= '<ul class="location-taxonomy-list">';
    } elseif ($atts['list_type'] == 'ol') {
        $output .= '<ol class="location-taxonomy-list">';
    }
    
    // Loop through terms and build output
    $terms_array = array();
    foreach ($location_terms as $term) {
        $term_output = '';
        
        // Create link if requested
        if ($atts['show_link'] === 'yes') {
            $term_link = get_term_link($term);
            if (!is_wp_error($term_link)) {
                $term_output = '<a href="' . esc_url($term_link) . '" class="location-taxonomy-link">' . esc_html($term->name) . '</a>';
            } else {
                $term_output = esc_html($term->name);
            }
        } else {
            $term_output = esc_html($term->name);
        }
        
        // Add to output based on list type
        if ($atts['list_type'] == 'ul' || $atts['list_type'] == 'ol') {
            $output .= '<li>' . $term_output . '</li>';
        } else {
            $terms_array[] = $term_output;
        }
    }
    
    // Close the list if specified
    if ($atts['list_type'] == 'ul') {
        $output .= '</ul>';
    } elseif ($atts['list_type'] == 'ol') {
        $output .= '</ol>';
    } else {
        // Join terms with separator
        $output = implode($atts['separator'], $terms_array);
    }
    
    return $output;
}

/**
 * Custom Excerpt Shortcode
 * 
 * Usage: 
 * [custom_excerpt length="20" post_id="123" more="..."]
 * [custom_excerpt length="20"]...dynamic content...[/custom_excerpt]
 */
add_shortcode('custom_excerpt', 'custom_excerpt_shortcode');
function custom_excerpt_shortcode($atts, $content = null) {
    // Attributes
    $atts = shortcode_atts(
        array(
            'length' => 20,
            'post_id' => null,
            'more' => '...',
        ),
        $atts,
        'custom_excerpt'
    );
    
    $text_to_truncate = '';

    // Check if content is provided (enclosing shortcode)
    if ( ! is_null( $content ) && $content !== '' ) {
        // Process nested shortcodes/dynamic tags
        $text_to_truncate = do_shortcode( $content );
    } else {
        // Fallback to post excerpt
        $post_id = $atts['post_id'] ? intval($atts['post_id']) : get_the_ID();
        
        if ( ! $post_id ) {
            return '';
        }
        
        $text_to_truncate = get_the_excerpt( $post_id );
    }
    
    if ( empty( $text_to_truncate ) ) {
        return '';
    }
    
    // Truncate
    $truncated = wp_trim_words( $text_to_truncate, intval( $atts['length'] ), $atts['more'] );
    
    return $truncated;
}

// Custom Shortcode to display ACF Taxonomy Terms
add_shortcode('scf_tax_display', function($atts) {
    $atts = shortcode_atts(array(
        'field' => '', // The field name
    ), $atts);

    // Get the field value
    $term = get_field($atts['field']);

    // Check if we have data
    if( ! $term ) return '';

    // Case 1: It returns a single Term Object
    if( is_object($term) ) {
        return $term->name;
    }

    // Case 2: It returns an Array of Objects (Multiple selections)
    if( is_array($term) ) {
        $names = array();
        foreach( $term as $t ) {
            if( is_object($t) ) {
                $names[] = $t->name;
            }
        }
        return implode(', ', $names); // Output as comma separated list
    }

    return '';
});

// Show all Custom Fields & Add Search Function at Display Conditions
function custom_custom_fields_meta_limit( $limit ) {
$new_limit = 100; // Change this to your desired limit
return $new_limit; }

add_filter('elementor_pro/display_conditions/dynamic_tags/custom_fields_meta_limit', 'custom_custom_fields_meta_limit');

function enqueue_custom_script_for_elementor_editor() {
    if ( did_action( 'elementor/loaded' ) && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
        ?>
        <script type="text/javascript">
        // Function to add the search input to the menu list
        function addSearchInput() {
            const menuList = document.querySelector('.e-conditions-select-menu .MuiMenu-list');
            if (menuList) {
                const firstItem = menuList.querySelector('li:first-child');
                if (!firstItem || !firstItem.classList.contains('search-input-item')) {
                    const searchInputItem = document.createElement('li');
                    searchInputItem.classList.add('search-input-item');
                    searchInputItem.innerHTML = '<input type="text" placeholder="Search..." oninput="filterMenu(this)" />';
                    
                    // Prevent default keydown behavior
                    const searchInput = searchInputItem.querySelector('input');
                    searchInput.addEventListener('keydown', function(event) {
                        event.stopPropagation();
                    });
                    
                    menuList.insertBefore(searchInputItem, menuList.firstChild);
                }
            }
        }

        // Function to filter menu items based on the search input
        function filterMenu(input) {
            const filter = input.value.toLowerCase();
            const menuItems = input.parentElement.parentElement.querySelectorAll('li:not(.search-input-item)');
            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        // Mutation observer callback
        const observerCallback = function(mutationsList) {
            for (let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => {
                        if (node.classList && node.classList.contains('MuiPopover-root')) {
                            addSearchInput();
                        }
                    });
                }
            }
        };

        // Set up the mutation observer
        const observer = new MutationObserver(observerCallback);
        observer.observe(document.body, { childList: true });

        // Adding CSS for the search input item
        const style = document.createElement('style');
        style.textContent = `
          .search-input-item {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding: 5px;
          }
          .search-input-item input {
            width: 100%;
            box-sizing: border-box;
            padding: 5px;
          }

         /* prevent word cropping */
        .e-conditions-select-menu .css-ivsn3r {
            max-width: initial;
        }
        `;
        document.head.appendChild(style);
        </script>
        <?php
    }
}
add_action( 'elementor/editor/footer', 'enqueue_custom_script_for_elementor_editor' );
