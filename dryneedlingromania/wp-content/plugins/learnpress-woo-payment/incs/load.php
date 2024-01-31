<?php

/**
 * Class LP_Addon_Woo_Payment
 */
class LP_Addon_Woo_Payment extends LP_Addon {
	/**
	 * @var string
	 */
	public $version = LP_ADDON_WOO_PAYMENT_VER;

	/**
	 * @var string
	 */
	public $require_version = LP_ADDON_WOO_PAYMENT_REQUIRE_VER;

	/**
	 * @var string
	 */
	public $plugin_file = LP_ADDON_WOO_PAYMENT_FILE;

	/**
	 * @var LP_Addon_Woo_Payment|null
	 *
	 * Hold the singleton of LP_Woo_Payment_Preload object
	 */
	protected static $_instance = null;

	/**
	 * LP_Woo_Payment_Preload constructor.
	 */

	public function __construct() {
		parent::__construct();
		$this->includes();
	}

	/**
	 * Include files needed
	 */
	protected function includes() {
		require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-gateway-woo.php';
		add_filter( 'learn_press_payment_method', [ $this, 'lp_woo_settings' ] );

		if ( ! LP_Gateway_Woo::is_option_enabled() ) {
			return;
		}

		//require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/functions.php';
		//require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/admin/course.php';
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-ajax.php';
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-order.php';
		include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/background-process/class-lp-woo-payment-background-process.php';

		if ( LP_Gateway_Woo::is_by_courses_via_product() ) {
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-woo-assign-course-to-product.php';
		} else {
			// Create type WC_Order_Item_LP_Course for wc order
			include_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-wc-order-item-course.php';

			// Create type WC_Product_LP_Course for wc product
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-wc-product-lp-course.php';

			// WooCommerce checkout
			require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-wc-checkout.php';
		}

		// Hooks
		require_once LP_ADDON_WOO_PAYMENT_PATH . '/incs/class-lp-wc-hooks.php';
		LP_WC_Hooks::instance();
	}

	/**
	 * Show lp woo settings
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function lp_woo_settings( array $methods ): array {
		$methods['woocommerce'] = 'LP_Gateway_Woo';

		return $methods;
	}

	/**
	 * Tests the background handler's connection.
	 *
	 * @since 4.0.8
	 *
	 * @return bool
	 */
	public static function check_background_available(): bool {
		$test_url = add_query_arg( 'action', 'lp_woo_background_process_test', admin_url( 'admin-ajax.php' ) );
		$result   = wp_safe_remote_get( $test_url );
		$body     = ! is_wp_error( $result ) ? wp_remote_retrieve_body( $result ) : null;

		return $body === '[TEST_LOOPBACK]';
	}
}
