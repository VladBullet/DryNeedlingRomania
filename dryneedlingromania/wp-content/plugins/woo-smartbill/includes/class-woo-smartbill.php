<?php

class Woo_Smartbill {
	
	function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies(){

		// Helpers
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-option-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'lib/class-anaf-api.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-wrapper.php';
		require_once plugin_dir_path( __FILE__ ) . 'lib/class-smartbill-client.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-cloud-options.php';

		// Main admin class
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-admin.php';

		// Gestiune class
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-gestiune.php';

		// Proforma class
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-proforma.php';

		// Integrari class
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-integrari.php';
		// YITH Bundle integration
		require_once plugin_dir_path( __FILE__ ) . 'integrari/class-woo-smartbill-yith-bundle.php';

		// Main front class
		require_once plugin_dir_path( __FILE__ ) . 'class-woo-smartbill-public.php';

		// 
		add_filter( 'option_woo_smartbill_options', array( $this, 'backwards_compatibility' ) );

	}

	private function set_locale(){
		
	}

	private function define_admin_hooks(){
		$admin = new Woo_Smartbill_Admin();

		add_filter( 'woocommerce_get_settings_pages', array( $admin, 'setting_page_class' ) );
		add_filter( 'wc_admin_page_tab_sections', array( $admin, 'register_wc_admin_tabs' ) );
		add_action( 'admin_menu', array( $admin, 'wc_admin_connect_page' ), 15 );

		// Custom statuses
		add_action( 'init', array( $admin, 'register_custom_order_status' ) );
		add_filter( 'wc_order_statuses', array( $admin, 'add_custom_statuses_to_order_statuses' ) );

		// Bulk actions
		add_filter( 'bulk_actions-edit-shop_order', array( $admin, 'define_bulk_actions' ) );

		// Handle bulk actions
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $admin, 'handle_bulk_actions' ), 10, 3 );

		// Status change
		add_action( 'woocommerce_order_status_changed', array( $admin, 'order_status_change' ), 10, 3 );

		// Columns for orders
		add_filter('manage_edit-shop_order_columns', array( $admin, 'add_column' ), 11 );
    	add_action('manage_shop_order_posts_custom_column', array( $admin, 'show_column_content' ), 11, 2 );

		// Add metabox
		add_action( 'add_meta_boxes', array( $admin, 'order_metabox' ) );

		// Javascript for orders
		add_action( 'admin_enqueue_scripts', array( $admin, 'admin_scripts' ) );

		// Ajax for admin actions
		add_action( 'wp_ajax_woo_smartbill', array( $admin, 'prelucrare_factura' ) );

		// Ajax for smartbill cloud
		add_action( 'wp_ajax_woo_smartbill_cloud', array( $admin, 'refresh_cloud' ) );

	}

	private function define_public_hooks(){
		$public = new Woo_Smartbill_Public();

		add_action( 'init', array( $public, 'check_cron' ) );

		add_action( 'woo_smartbill_verificare_facturi', array( $public, 'verifica_facturi' ) );

		// WooCommerce emails
		add_action( 'woocommerce_email_order_meta', array( $public, 'adauga_factura_la_email' ), 70, 4 );

		// Add My order table action
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $public, 'adauga_factura_la_my_acc' ), 10, 2 );
		
	}

	// Backwards compatibility for versions previous 1.0.0
	public function backwards_compatibility( $options ){

		if ( is_array( $options ) ) {
			if ( isset( $options['discount'] ) ) {
				
				if ( ! isset( $options['sale'] ) ) {
					$options['sale'] = $options['discount'];
				}

				if ( ! isset( $options['fee'] ) ) {
					$options['fee'] = $options['discount'];
				}

			}
		}

		return $options;

	}


}
