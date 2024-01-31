<?php
/**
 * Plugin Name: Thim Elementor Kit Pro
 * Description: It is page builder for the Elementor page builder.
 * Author: ThimPress
 * Version: 1.0.2
 * Author URI: http://thimpress.com
 * Requires at least: 5.2
 * Tested up to: 6.0
 * Requires PHP: 7.0
 * Text Domain: thim-elementor-kit-pro
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

define( 'THIM_EKIT_PRO_VERSION', '1.0.2' );
define( 'THIM_EKIT_PRO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'THIM_EKIT_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'THIM_EKIT_PRO_PLUGIN_FILE', __FILE__ );
define( 'THIM_EKIT_PRO_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'THIM_EKIT_PRO_DEV', false );

/**
 * Class Thim Elementor Kit Pro Plugin
 *
 * @author Nhamdv from Thimpress <daonham95@gmail.com>
 */
if ( ! class_exists( 'Thim_EL_Kit_Pro' ) ) {
	final class Thim_EL_Kit_Pro {
		protected static $_instance = null;

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 99 );

			if ( ! $this->thim_kit_is_active() ) {
				add_action( 'admin_notices', array( $this, 'required_plugins_notice' ) );
				return;
			}

			if ( ! $this->elementor_is_active() ) {
				return;
			}

			$this->includes();
		}

		protected function includes() {
			// Utilities
			require_once THIM_EKIT_PRO_PLUGIN_PATH . 'inc/utilities/singleton-trait.php';

			// Inc
			require_once THIM_EKIT_PRO_PLUGIN_PATH . 'inc/class-enqueue-scripts.php';

			// Elementor
			require_once THIM_EKIT_PRO_PLUGIN_PATH . 'inc/elementor/class-elementor.php';

			// Modules
			require_once THIM_EKIT_PRO_PLUGIN_PATH . 'inc/modules/class-init.php';
		}

		public function required_plugins_notice() {
			$screen = get_current_screen();

			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
				return;
			}

			$plugin = 'thim-elementor-kit/thim-elementor-kit.php';

			$installed_plugins     = get_plugins();
			$is_thim_kit_installed = isset( $installed_plugins[ $plugin ] );

			if ( $is_thim_kit_installed ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

				$message  = sprintf( '<p>%s</p>', esc_html__( 'Thim Elementor Kit Pro requires Thim Elementor Kit to be activated.', 'thim-elementor-kit-pro' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, esc_html__( 'Activate Thim Elementor Kit Now', 'thim-elementor-kit-pro' ) );
			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}

				$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=thim-elementor-kit' ), 'install-plugin_thim-elementor-kit' );

				$message  = sprintf( '<p>%s</p>', esc_html__( 'Thim Elementor Kit requires Thim Elementor Kit to be installed.', 'thim-elementor-kit-pro' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, esc_html__( 'Install Thim Elementor Kit Now', 'thim-elementor-kit' ) );
			}

			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'thim-elementor-kit-pro', false, basename( THIM_EKIT_PRO_PLUGIN_PATH ) . '/languages' );
		}

		public function elementor_is_active() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		public function thim_kit_is_active() {
			return defined( 'THIM_EKIT_VERSION' );
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

function thim_el_kit_pro() {
	return Thim_EL_Kit_Pro::instance();
}

add_action( 'plugins_loaded', 'thim_el_kit_pro', 99 );
