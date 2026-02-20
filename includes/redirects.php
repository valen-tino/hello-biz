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
// Also redirect /property/ archive to /aanbod/
add_action( 'template_redirect', function() {

	$url = $_SERVER['REQUEST_URI'];
	$path = parse_url( $url, PHP_URL_PATH );

	if ( strpos( $url, '/type-of-project/' ) !== false || strpos( $url, '/location/' ) !== false ) {
		wp_redirect( home_url( '/project/' ), 302 );
		exit;
	}

	// Redirect property archive only, not single properties
	if ( untrailingslashit( $path ) === untrailingslashit( parse_url( home_url( '/property/' ), PHP_URL_PATH ) ) ) {
		wp_redirect( home_url( '/aanbod/' ), 302 );
		exit;
	}

	// Redirect old Zorg, Woningen & Commercieel to /project/
	if ( strpos( $url, '/zorg/' ) !== false || strpos( $url, '/woningen/' ) !== false || strpos( $url, '/commercieel/' ) !== false ) {
		wp_redirect( home_url( '/project/' ), 302 );
		exit;
	}
} );

// Specific Project Redirects
add_action( 'template_redirect', function() {
	$request_path = untrailingslashit( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

	$redirects = [
		'/de-molenist'                      => '/project/de-molenist/',
		'/grotekerksbuurt'                  => '/project/grotekerksbuurt/',
		'/ceder-gym'                        => '/project/ceder-gym/',
		'/slikveld'                         => '/project/slikveld/',
		'/zorgcentrum-hofplein-middelburg'  => '/project/zorgcentrum-hofplein/',
		'/zwaluwlaan'                       => '/project/zwaluwlaan-hargplein/',
	];

	foreach ( $redirects as $source => $destination ) {
		// Compare path component to handle subdirectory installs correctly
		$source_path = untrailingslashit( parse_url( home_url( $source ), PHP_URL_PATH ) );
		
		if ( $request_path === $source_path ) {
			wp_redirect( home_url( $destination ), 301 );
			exit;
		}
	}
} );