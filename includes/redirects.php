<?php
/**
 * Redirects
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Redirect hidden projects to /project/ page
add_action( 'template_redirect', function() {
	if ( ! is_singular( 'project' ) ) {
		return;
	}

	$hide = get_post_meta( get_the_ID(), 'hide_on_project_page', true );

	if ( $hide === '1' ) {
		wp_redirect( home_url( '/project/' ), 302 );
		exit;
	}
} );

// Redirect if there are /type-of-project/ or /location/ in the url, redirect to /project/
add_action( 'template_redirect', function() {
	$url = $_SERVER['REQUEST_URI'];
	if ( strpos( $url, '/type-of-project/' ) !== false || strpos( $url, '/location/' ) !== false ) {
		wp_redirect( home_url( '/project/' ), 302 );
		exit;
	}
} );