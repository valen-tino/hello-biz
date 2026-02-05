<?php
/**
 * Custom "Magic" Cursor Carousel
 * 
 * Adds a custom cursor with link detection for carousel navigation.
 * CSS and JS are loaded as separate files for better caching and maintainability.
 *
 * @package HelloBiz
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue custom cursor carousel assets
 */
function hello_biz_enqueue_cursor_carousel_assets() {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_version = wp_get_theme()->get( 'Version' );

    // Enqueue CSS
    wp_enqueue_style(
        'hello-biz-cursor-carousel',
        $theme_uri . '/assets/css/custom-cursor-carousel.css',
        array(),
        $theme_version
    );

    // Enqueue JavaScript
    wp_enqueue_script(
        'hello-biz-cursor-carousel',
        $theme_uri . '/assets/js/custom-cursor-carousel.js',
        array(),
        $theme_version,
        true // Load in footer
    );
}
add_action( 'wp_enqueue_scripts', 'hello_biz_enqueue_cursor_carousel_assets' );