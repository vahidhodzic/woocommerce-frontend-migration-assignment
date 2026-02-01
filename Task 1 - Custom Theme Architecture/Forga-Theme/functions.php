<?php
/**
 * Theme functions
 *
 * @package Forga
 */

defined( 'ABSPATH' ) || exit;

/**
 * -------------------------------------------------
 * Theme constants
 * -------------------------------------------------
 */
define( 'FORGA_VERSION', '1.0.0' );
define( 'FORGA_PATH', get_template_directory() );
define( 'FORGA_URI', get_template_directory_uri() );

/**
 * -------------------------------------------------
 * Load core files
 * -------------------------------------------------
 */

/**
 * Setup
 */
require_once FORGA_PATH . '/inc/setup/theme-setup.php';
require_once FORGA_PATH . '/inc/setup/enqueue.php';
require_once FORGA_PATH . '/inc/setup/woocommerce-support.php';

/**
 * Helpers
 */
require_once FORGA_PATH . '/inc/helpers/formatting.php';
require_once FORGA_PATH . '/inc/helpers/utilities.php';

/**
 * Hooks
 */
require_once FORGA_PATH . '/inc/hooks/global-hooks.php';
require_once FORGA_PATH . '/inc/hooks/woocommerce-hooks.php';

/**
 * WooCommerce logic
 */
if ( class_exists( 'WooCommerce' ) ) {
    require_once FORGA_PATH . '/inc/woocommerce/product-functions.php';
    require_once FORGA_PATH . '/inc/woocommerce/cart-functions.php';
    require_once FORGA_PATH . '/inc/woocommerce/checkout-functions.php';
}

