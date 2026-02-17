<?php
/**
 * Performance optimizations
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Optimize LCP Background Images for .main-hero containers
 * 
 * Problem: CSS background images are not discoverable until the CSS is parsed,
 * causing a significant resource load delay (3+ seconds).
 * 
 * Solution: Add a <link rel="preload"> tag in the <head> to make the 
 * background image discoverable immediately in the initial HTML document.
 * 
 * Usage: Add 'main-hero' class to your Elementor container with background image.
 */

add_action('wp_head', 'preload_main_hero_background_image', 1);
function preload_main_hero_background_image() {
    // Only run on frontend
    if (is_admin() || wp_doing_ajax()) return;
    
    global $post;
    if (!$post) return;
    
    // Get the Elementor data for the current page
    $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
    
    if (empty($elementor_data)) return;
    
    // Decode the JSON data
    $elements = json_decode($elementor_data, true);
    
    if (!is_array($elements)) return;
    
    // Search for the main-hero element and get its background image
    $hero_bg_url = find_main_hero_background_recursive($elements);
    
    if ($hero_bg_url) {
        // Output preload link with fetchpriority high
        echo '<link rel="preload" as="image" href="' . esc_url($hero_bg_url) . '" fetchpriority="high">' . "\n";
    }
}

/**
 * Recursively search Elementor elements for .main-hero with background image
 */
function find_main_hero_background_recursive($elements) {
    if (!is_array($elements)) return null;
    
    foreach ($elements as $element) {
        if (!is_array($element)) continue;
        
        // Check if this element has 'main-hero' in its CSS classes
        $css_classes = '';
        
        if (isset($element['settings']['css_classes'])) {
            $css_classes = $element['settings']['css_classes'];
        }
        // Also check _css_classes (used by some Elementor versions)
        if (isset($element['settings']['_css_classes'])) {
            $css_classes .= ' ' . $element['settings']['_css_classes'];
        }
        
        // Check if main-hero class exists
        if (strpos($css_classes, 'main-hero') !== false) {
            // Found the hero element, get background image
            if (isset($element['settings']['background_image']['url']) && !empty($element['settings']['background_image']['url'])) {
                return $element['settings']['background_image']['url'];
            }
        }
        
        // Recursively search child elements
        if (!empty($element['elements']) && is_array($element['elements'])) {
            $found = find_main_hero_background_recursive($element['elements']);
            if ($found) {
                return $found;
            }
        }
    }
    
    return null;
}

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

/**
 * Cache Invalidation for Property Search Options
 * Updates the version timestamp whenever a property or project is saved.
 */
function invalidate_property_search_cache($post_id, $post, $update) {
    // Only proceed for specific post types
    if (!in_array($post->post_type, ['property', 'project'])) {
        return;
    }
    
    // Convert current time to float for precision or simple time()
    update_option('hello_biz_property_data_version', time());
}
add_action('save_post', 'invalidate_property_search_cache', 10, 3);

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
