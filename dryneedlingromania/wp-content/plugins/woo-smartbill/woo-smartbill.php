<?php

/**
 * WooCommerce SmartBill
 *
 * Plugin Name:       WooCommerce SmartBill
 * Plugin URI:        https://avianstudio.com/plugin/woo-smartbill/
 * Description:       Plugin pentru generarea facturilor Smartbill direct din WooCommerce
 * Version:           1.0.0
 * Author:            Ciobanu George
 * Author URI:        https://avianstudio.com/
 * Text Domain:       woo-smartbill
 * Domain Path:       /languages
 */

// Assets directory
define( "WOO_SMARTBILL_URL", plugin_dir_url( __FILE__ ) );
define( "WOO_SMARTBILL_VERSION", '1.0.0' );
define( "WOO_SMARTBILL_FILE", __FILE__ );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-smartbill.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/lib/class-woo-smartbill-edd-license.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-smartbill-license.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-smartbill-logging.php';

add_action( 'plugins_loaded', 'run_woo_smartbill', 90 );
function run_woo_smartbill() {

	if ( ! is_woocommerce_activated() ) {
		return;
	}

	$plugin = new Woo_Smartbill();

	// Logging class
	$woo_smartbill_logging = Woo_SmartBill_Logging::get_instance();
	$woo_smartbill_logging->setup_log_file();

}

/**
 * Check if WooCommerce is activated
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}