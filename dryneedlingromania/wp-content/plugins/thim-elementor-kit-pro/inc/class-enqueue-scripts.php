<?php
namespace Thim_EL_Kit_Pro;

use Thim_EL_Kit\Custom_Post_Type;

class Enqueue {
	use SingletonTrait;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 100 );
	}

	public function frontend_scripts() {
		$widgets_info = include THIM_EKIT_PRO_PLUGIN_PATH . 'build/widgets.asset.php';

		wp_enqueue_script( 'thim-ekit-pro-widgets', THIM_EKIT_PRO_PLUGIN_URL . 'build/widgets.js', array_merge( array( 'elementor-frontend' ), $widgets_info['dependencies'] ), $widgets_info['version'], true );
		wp_enqueue_style( 'thim-ekit-pro-widgets', THIM_EKIT_PRO_PLUGIN_URL . 'build/widgets.css', array( 'elementor-frontend' ), $widgets_info['version'] );
	}
}

Enqueue::instance();
