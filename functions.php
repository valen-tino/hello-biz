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
require_once HELLO_BIZ_PATH . '/includes/contact-formulier.php';
require_once HELLO_BIZ_PATH . '/includes/job-application-formulier.php';
require_once HELLO_BIZ_PATH . '/includes/custom-cursor-carousel.php';

// Modular Classes (v1.2.0)
require_once HELLO_BIZ_PATH . '/includes/class-widget-registrar.php';
require_once HELLO_BIZ_PATH . '/includes/class-ajax-handlers.php';

// Refactored Includes (v1.2.2)
require_once HELLO_BIZ_PATH . '/includes/enqueue.php';
require_once HELLO_BIZ_PATH . '/includes/performance.php';
require_once HELLO_BIZ_PATH . '/includes/helpers.php';
require_once HELLO_BIZ_PATH . '/includes/get-data-from-parent-project.php';
require_once HELLO_BIZ_PATH . '/includes/reset-cache-after-commit.php';
require_once HELLO_BIZ_PATH . '/includes/redirects.php';
require_once HELLO_BIZ_PATH . '/includes/load-when-necessary.php';

