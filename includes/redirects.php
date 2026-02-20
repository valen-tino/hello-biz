<?php
/**
 * Redirects
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle all URL-based redirects in a single hook for efficiency.
add_action( 'template_redirect', function() {

	// Sanitize the raw server input before use.
	$raw_uri     = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_uri = esc_url_raw( $raw_uri );
	$path        = parse_url( $request_uri, PHP_URL_PATH );
	$path        = $path ? untrailingslashit( $path ) : '/';

	// Redirect old taxonomy URLs to /project/.
	if ( str_contains( $request_uri, '/type-of-project/' )
		|| str_contains( $request_uri, '/location/' )
		|| str_contains( $request_uri, '/zorg/' )
		|| str_contains( $request_uri, '/woningen/' )
		|| str_contains( $request_uri, '/commercieel/' )
	) {
		wp_safe_redirect( home_url( '/project/' ), 302 );
		exit;
	}

	// Redirect /property/ archive only — not single property pages.
	$property_archive_path = untrailingslashit( parse_url( home_url( '/property/' ), PHP_URL_PATH ) );
	if ( $path === $property_archive_path ) {
		wp_safe_redirect( home_url( '/aanbod/' ), 302 );
		exit;
	}

	$slug_redirects = [
		'/de-molenist'                     => '/project/de-molenist/',
		'/grotekerksbuurt'                 => '/project/grotekerksbuurt/',
		'/ceder-gym'                       => '/project/ceder-gym/',
		'/slikveld'                        => '/project/slikveld/',
		'/zorgcentrum-hofplein-middelburg' => '/project/zorgcentrum-hofplein/',
		'/zwaluwlaan'                      => '/project/zwaluwlaan-hargplein/',
	];

	foreach ( $slug_redirects as $source => $destination ) {
		$source_path = untrailingslashit( parse_url( home_url( $source ), PHP_URL_PATH ) );

		if ( $path === $source_path ) {
			wp_safe_redirect( home_url( $destination ), 301 );
			exit;
		}
	}

}, 1 ); // Priority 1 — run before most plugins.

// Redirect hidden projects to the /project/ archive.
add_action( 'template_redirect', function() {

	if ( ! is_singular( 'project' ) ) {
		return;
	}

	if ( get_post_meta( get_the_ID(), 'hide_on_project_page', true ) === '1' ) {
		wp_safe_redirect( home_url( '/project/' ), 302 );
		exit;
	}

} );