<?php
/**
 * Conditionally dequeue scripts and styles that are not needed on certain pages.
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dequeue unnecessary scripts/styles on single project and property pages.
 */
function hello_biz_dequeue_unnecessary_assets() {
	if ( is_singular( 'project' ) || is_front_page() ) {

		// Custom Cursor Carousel
		wp_dequeue_script( 'hello-biz-cursor-carousel' );
		wp_dequeue_style( 'hello-biz-cursor-carousel' );

		// Icon List Filter
		wp_dequeue_script( 'icon-list-filter' );

		// Multistep Blossem Form
		wp_dequeue_script( 'multistep-blossem-form' );
		wp_dequeue_style( 'multistep-blossem-form' );

		// Job Application Formulier
		wp_dequeue_script( 'job-application-formulier' );
		wp_dequeue_style( 'job-application-formulier' );
	}
    
    if ( is_singular( 'property' ) ) {
        // Custom Cursor Carousel
		wp_dequeue_script( 'hello-biz-cursor-carousel' );
		wp_dequeue_style( 'hello-biz-cursor-carousel' );

        // Multistep Blossem Form
		wp_dequeue_script( 'multistep-blossem-form' );
		wp_dequeue_style( 'multistep-blossem-form' );

		// Job Application Formulier
		wp_dequeue_script( 'job-application-formulier' );
		wp_dequeue_style( 'job-application-formulier' );
    }
}
add_action( 'wp_enqueue_scripts', 'hello_biz_dequeue_unnecessary_assets', 999 );
