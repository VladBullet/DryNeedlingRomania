<?php

class Woo_Facturare {

	protected $loader;
	private $plugin_name;
	private $version;

	public function __construct() {

		if ( defined( 'WOOFACTURARE_VERSION' ) ) {
			$this->version = WOOFACTURARE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woo-facturare';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-facturare-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-facturare-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-facturare-review.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-facturare-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-facturare-options-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibilities.php';
		$this->loader = new Woo_Facturare_Loader();

	}

	private function set_locale() {

		$this->loader->add_action( 'plugins_loaded', $this, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		// Facturare Review
		new GC_Facturare_Review();

		$facturare_admin = new Woo_Facturare_Admin( $this->plugin_name, $this->version );

		$this->loader->add_filter( 'woocommerce_get_settings_pages', $facturare_admin, 'setting_page_class' );
		$this->loader->add_filter( 'wc_admin_page_tab_sections', $facturare_admin, 'register_wc_admin_tabs' );

		// Edit Plugin Links
		$this->loader->add_filter( 'plugin_action_links', $facturare_admin, 'action_links', 10, 2 );

		// Save Order Meta
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $facturare_admin, 'update_order_meta' );

		// Save extra fields to customer
		$this->loader->add_action( 'woocommerce_checkout_update_user_meta', $facturare_admin, 'update_customer_data', 10, 2 );

		// Filter billing fields
		$this->loader->add_filter( 'woocommerce_order_formatted_billing_address', $facturare_admin, 'filter_billing_fields', 9999999, 2 );
		$this->loader->add_filter( 'woocommerce_my_account_my_address_formatted_address', $facturare_admin, 'myacc_filter_billing_fields', 90, 3 );
		$this->loader->add_filter( 'woocommerce_formatted_address_replacements', $facturare_admin, 'extra_fields_replacements', 9999999, 2 );
		$this->loader->add_filter( 'woocommerce_localisation_address_formats', $facturare_admin, 'localisation_address_formats', 90 );

		// WC Admin
		$this->loader->add_action( 'admin_menu', $facturare_admin, 'wc_admin_connect_page', 15 );

		// Settings link
		$this->loader->add_action( 'plugin_action_links_' . WOOFACTURARE_SLUG, $facturare_admin, 'settings_links', 15 );

		// Add extra div
		$this->loader->add_action( 'woocommerce_page_wc-settings', $facturare_admin, 'start_advertise', 99 );
		$this->loader->add_action( 'woocommerce_page_wc-settings', $facturare_admin, 'feedback_box', 99 );
		$this->loader->add_action( 'woocommerce_page_wc-settings', $facturare_admin, 'contact_box', 99 );
		$this->loader->add_action( 'woocommerce_page_wc-settings', $facturare_admin, 'stop_advertise', 99 );

		// WooCommerce PDF Invoice - https://codecanyon.net/item/woocommerce-pdf-invoice/5951088
		$this->loader->add_filter( 'woo_pdf_macros', $facturare_admin, 'pdf_macros', 10, 2 );

		// Admin edit fields
		$this->loader->add_filter( 'woocommerce_admin_billing_fields', $facturare_admin, 'admin_billing_fields', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_tip_facturare', $facturare_admin, 'admin_billing_get_tip_facturare', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_cnp', $facturare_admin, 'admin_billing_get_cnp', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_cui', $facturare_admin, 'admin_billing_get_cui', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_nume_banca', $facturare_admin, 'admin_billing_get_nume_banca', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_nr_reg_com', $facturare_admin, 'admin_billing_get_nr_reg_com', 10, 2 );
		$this->loader->add_filter( 'woocommerce_order_get__billing_iban', $facturare_admin, 'admin_billing_get_iban', 10, 2 );

		// Save admin fields
		$this->loader->add_action( 'woocommerce_process_shop_order_meta', $facturare_admin, 'save_admin_billing_fields', 30 );

		// Scripts for conditional fields
		$this->loader->add_action( 'admin_enqueue_scripts', $facturare_admin, 'admin_enqueue_scripts' );

		// Add metabox
		add_action( 'add_meta_boxes', array( $facturare_admin, 'order_metabox' ) );

	}

	private function define_public_hooks() {

		$plugin_public = new Woo_Facturare_Public();

		$this->loader->add_action( 'wp_head', $plugin_public, 'hide_fields' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'add_js_to_footer', 99 );

		// Change checkout fields
		$this->loader->add_filter( 'woocommerce_billing_fields', $plugin_public, 'override_checkout_fields', 30 );
		$this->loader->add_filter( 'woocommerce_form_field', $plugin_public, 'override_field_html', 20, 3 );
		$this->loader->add_filter( 'woocommerce_form_field_args', $plugin_public, 'fix_checkout_args', 20, 3 );
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'all_fields_are_optional', 99999 );

		// Validate checkout fields
		$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'validate_checkout' );

		// Add fields to profile
		$this->loader->add_filter( 'woocommerce_address_to_edit', $plugin_public, 'add_profile_fields', 90, 2 );
		// Save fields to profile
		$this->loader->add_filter( 'woocommerce_customer_save_address', $plugin_public, 'save_profile_fields', 90, 2 );

		// Get info from db
		$this->loader->add_filter( 'get_post_metadata', $this, 'get_order_meta', 100, 4 );

		// Woo Smartbill
		$this->loader->add_filter( 'woo_smartbill_data', $this, 'filter_smartbill_data', 10, 2 );

	}

	public function run() {
		$this->loader->run();
	}

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-facturare',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);

	}

	public function get_order_meta( $metadata, $object_id, $meta_key, $single ){
		$options_helper = Facturare_Options_Helper::get_instance();
		$keys = $options_helper->get_keys();

		if ( in_array( $meta_key, $keys ) ) {

			if ( '_billing_facturare_cnp' == $meta_key ) {
				return $options_helper->get_cnp( $object_id );
			}

			if ( '_billing_facturare_nr_reg_com' == $meta_key ) {
				return $options_helper->get_nr_reg_com( $object_id );
			}

			if ( '_billing_facturare_cui' == $meta_key ) {
				return $options_helper->get_cui( $object_id );
			}

			if ( '_billing_facturare_nume_banca' == $meta_key ) {
				return $options_helper->get_nume_banca( $object_id );
			}

			if ( '_billing_facturare_iban' == $meta_key ) {
				return $options_helper->get_iban( $object_id );
			}

		}

		return $metadata;
	}

	public function filter_smartbill_data( $data, $order_id ){
		$options_helper = Facturare_Options_Helper::get_instance();

		if ( isset( $data['client'] ) ) {

			if ( 'pers-jur' == $options_helper->get_tip( $order_id ) ) {
				$order = new WC_Order($order_id);
				$cui = $options_helper->get_cui( $order_id );

				$client = array(
					"name"       => $order->get_billing_company(),
					"vatCode"    => $cui,
					"regCom"     => $options_helper->get_nr_reg_com( $order_id ),
					"address"    => $data['client']['address'],
					"isTaxPayer" => ANAF_API::is_tax_payer( $cui ),
					"city"       => $data['client']['city'],
					"county"     => $data['client']['county'],
					"country"    => $data['client']['country'],
					"saveToDb"   => $data['client']['saveToDb'],
					"email"      => $data['client']['email'],
				);

				$data['client'] = $client;

			}

		}

		return $data;

	}

}
