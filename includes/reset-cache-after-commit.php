<?php

/**
 * Auto-clear Hummingbird Cache after WP Pusher updates the theme.
 */
add_action( 'wppusher_theme_was_updated', 'clear_hummingbird_cache_on_deploy', 10, 1 );

function clear_hummingbird_cache_on_deploy( $theme_slug ) {
    // 1. Clear Page Cache (HTML)
    if ( function_exists( 'wphb_clear_page_cache' ) ) {
        wphb_clear_page_cache();
    }

    // 2. Clear Minification/Asset Cache (CSS/JS)
    // Important if you are pushing style.css or script changes
    if ( function_exists( 'wphb_clear_minification_cache' ) ) {
        wphb_clear_minification_cache();
    }

    // Optional: Log to error_log to verify it's working
    error_log( "WP Pusher updated theme: {$theme_slug}. Hummingbird cache cleared." );
}