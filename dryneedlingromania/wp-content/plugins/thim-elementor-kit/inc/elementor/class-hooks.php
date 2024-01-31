<?php
namespace Thim_EL_Kit\Elementor;

use Thim_EL_Kit\SingletonTrait;
use Thim_EL_Kit\LoginRegisterTrait;

class Hooks {
	use SingletonTrait;
	use LoginRegisterTrait;

	public function __construct() {
		$this->hook_login_register_from();
		$this->init_ajax_hooks();
	}

	public function hook_login_register_from() {
		// end
		// redirect after login success
		add_filter( 'login_redirect', array( $this, 'login_success_redirect' ), 99999, 3 );

		// redirect if login false
		add_filter( 'authenticate', array( $this, 'login_authenticate' ), 99999, 2 );
		/*** End login user */

		/*** Register user */
		// Check extra register if set auto login when register
		add_action( 'register_post', array( $this, 'check_extra_register_fields' ), 10, 3 );

		// Update password if set auto login when register
		add_action( 'user_register', array( $this, 'register_update_pass_and_login' ), 99999 );

		// redirect if register false
		add_action( 'registration_errors', array( $this, 'register_failed' ), 99999, 3 );

		// redirect if register success if not set auto login when register
		add_action( 'register_new_user', array( $this, 'register_verify_mail_success_redirect' ), 999999 );

		add_filter( 'wp_new_user_notification_email', array( $this, 'message_set_password_when_not_auto_login' ), 999999, 2 );
		/*** End register user */

		/*** Reset password */
		add_action( 'lostpassword_post', array( $this, 'check_field_to_reset_password' ), 99999, 1 );
		add_filter( 'login_form_rp', array( $this, 'validate_password_reset' ), 99999 );
		add_filter( 'login_form_resetpass', array( $this, 'validate_password_reset' ), 99999 );

		/*** Override message send mail with case auto-login */
		add_filter( 'password_change_email', array( $this, 'message_when_user_register_auto_login' ), 999999, 1 );
	}

	public function init_ajax_hooks() {
		// if ( class_exists( 'WooCommerce' ) ) {
		add_action( 'wp_ajax_thim_load_content', array( $this, 'ajax_load_content_product' ) );
		add_action( 'wp_ajax_nopriv_thim_load_content', array( $this, 'ajax_load_content_product' ) );
		// }
	}

	public function ajax_load_content_product() {
		ob_start();
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$params = htmlspecialchars_decode( $_POST['params'] );
		$params = json_decode( str_replace( '\\', '', $params ), true );
		$cat_id = $_POST['category'];

		if ( ! class_exists( '\Elementor\Thim_Ekit_Products_Base' ) ) {
			include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/global/product-base.php';
		}
		if ( ! class_exists( '\Elementor\Thim_Ekit_Widget_List_Product' ) ) {
			include THIM_EKIT_PLUGIN_PATH . 'inc/elementor/widgets/global/list-product.php';
		}

		$list_product = new \Elementor\Thim_Ekit_Widget_List_Product();

		$settings = $this->get_widget_settings( intval( $params['page_id'] ), sanitize_text_field( $params['widget_id'] ) );

		$list_product->render_data_content_tab( $settings, $cat_id );

		$html = ob_get_contents();

		ob_end_clean();

		wp_send_json_success( $html );

		wp_die();
	}

	public static function get_widget_settings( $page_id, $widget_id ) {
		$document = Plugin::$instance->documents->get( $page_id );
		$settings = array();
		if ( $document ) {
			$elements    = Plugin::instance()->documents->get( $page_id )->get_elements_data();
			$widget_data = self::element_recursive( $elements, $widget_id );
			if ( ! empty( $widget_data ) && is_array( $widget_data ) ) {
				$widget = Plugin::instance()->elements_manager->create_element_instance( $widget_data );
			}
			if ( ! empty( $widget ) ) {
				$settings = $widget->get_settings_for_display();
			}
		}

		return $settings;
	}

	/**
	 * Get Widget data.
	 *
	 * @param array  $elements Element array.
	 * @param string $form_id  Element ID.
	 *
	 * @return bool|array
	 */
	public static function element_recursive( $elements, $form_id ) {

		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}
}
Hooks::instance();
