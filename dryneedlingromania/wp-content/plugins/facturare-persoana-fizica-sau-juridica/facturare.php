<?php

/**
 * Plugin Name:       Facturare - Persoana Fizica sau Juridica
 * Plugin URI:        https://facturare.georgeciobanu.com
 * Description:       Adaugă câmpurile necesare facturării persoanelor fizice sau juridice conform legislației din Romania în vigoare.
 * Version:           1.1.3
 * Author:            Avian Studio
 * Author URI:        https://avianstudio.com/
 * Text Domain:       woo-facturare
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WOOFACTURARE_VERSION', '1.1.3' );
define( 'WOOFACTURARE_SLUG', plugin_basename( __FILE__ ) );
define( 'WOOFACTURARE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOFACTURARE_ASSETS', plugin_dir_url( __FILE__ ) . '/assets/' );

require plugin_dir_path( __FILE__ ) . 'includes/class-facturare.php';

function run_woo_facturare() {
		
	$plugin = new Woo_Facturare();
	$plugin->run();

}
run_woo_facturare();
