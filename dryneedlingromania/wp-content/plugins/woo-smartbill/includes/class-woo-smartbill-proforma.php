<?php

class Woo_SmartBill_Proforma {

	private $options = array();

	public function __construct() {

		// Sections
		add_filter( 'woocommerce_get_sections_woo-smartbill', array( $this, 'sectiune_proforma' ), 30 );

		// Settings
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_proforma' ), 30, 2 );

		// hooks for proforma
		add_action( 'admin_init', array( $this, 'hooks' ) );

		add_action( 'init', array( $this, 'init' ) );

		// Status change
		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_change' ), 10, 3 );

		// Testare
		// add_action( 'admin_init', array( $this, 'testare' ) );

	}

	public function hooks(){

		if ( 'yes' != $this->get_option( 'enable' ) ) {
			return;
		}

		// Bulk actions
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'define_bulk_actions' ) );

		// Handle bulk actions
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );

		// check if estimate exist
		add_filter( 'woo_smartbill_genereaza_data', array( $this, 'check_proforma' ), 20, 2 );

		// Verifica daca proforma este generata
		add_filter( 'woo_smartbill_metabox_check_invoices', array( $this, 'verifica_status_proforma' ), 10, 2 );

		// afisare info despre proforma
		add_action( 'woo_smartbill_metabox_after_info', array( $this, 'afisare_info_proforma' ), 10 );

		// adauga actiuni pentru proforma
		add_action( 'woo_smartbill_metabox_before_actions', array( $this, 'actiuni_proforma' ), 10 );
		add_action( 'woo_smartbill_metabox_after_actions', array( $this, 'button_anulare_proforma' ), 10 );

		// ajax proforma actions
		add_action( 'woo_smartbill_order_ajax_action', array( $this, 'ajax_proforma' ), 10, 3 );

		// verifica generarea facturilor
		add_action( 'woo_smartbill_dupa_genereaza_factura', array( $this, 'status_proforma' ), 10, 2 );

	}

	public function init(){

		// WooCommerce emails
		add_action( 'woocommerce_email_order_meta', array( $this, 'adauga_proforma_la_email' ), 70, 4 );

		// Add My order table action
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'adauga_proforma_la_my_acc' ), 10, 2 );

		// alterare serie proforma
		add_filter( 'woo_smartbill_genereaza_proforma_data', array( $this, 'handle_estimate_series' ), 10, 2 );

	}

	private function get_option( $key = '' ){

		if ( empty( $this->options ) ) {
			$this->options = get_option( 'woo_smartbill_proforma', array() );
		}

		if ( '' != $key ) {
			return isset( $this->options[ $key ] ) ? $this->options[ $key ] : false;
		}

		return $this->options;

	}

	public function sectiune_proforma( $sections ){
		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		if ( ! $woo_smartbill_license->is_valid() ) {
			return $sections;
		}

		$sections['proforma'] = esc_html__( 'Proforma', 'woo-smartbill' );

		return $sections;

	}

	public function setari_proforma( $settings, $current_section ){

		if ( 'proforma' != $current_section ) {
			return $settings;
		}

		$smartbill_cloud = Woo_SmartBill_Cloud_Options::instance();

		$settings[] = array(
			'title' => esc_html__( 'Setări proforma', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_proforma',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_proforma[enable]',
			'title'    => __( 'Folosesti Proforma ?', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => 'Da',
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_proforma[serie-proforma]',
			'title'    => __( 'Serie proforma', 'woo-smartbill' ),
			'type'     => 'woo_smartbill_cloud',
			'action'   => 'serii-proforma',
			'desc'     => '',
			'options'  => $smartbill_cloud->serii_proforme(),
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_proforma[auto-generare]',
			'title'    => __( 'Generare', 'woo-smartbill' ),
			'type'     => 'multiselect',
			'desc'     => '',
			'options'  => wc_get_order_statuses(),
			'class'    => 'wc-enhanced-select',
			'default'  => array( 'wc-pending' ),
			'autoload' => false,
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_proforma',
		);

		// Setari email
		$settings[] = array(
			'title' => esc_html__( 'Setări Email', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_proforma_email',
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_proforma[email-types]',
			'title'    => __( 'Email-uri', 'woo-smartbill' ),
			'type'     => 'multiselect',
			'desc'     => '',
			'options'  => $this->get_emails(),
			'class'    => 'wc-enhanced-select',
			'default'  => array(),
			'autoload' => false,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_proforma[force-email-creation]',
			'title'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Forțează.', 'woo-smartbill' )
			              . '<br/><div>'
			              . __( 'Dacă proforma nu a fost generată când se trimit emailuri o să forțăm generarea ei.', 'woo-smartbill' )
			              . '</div>',
          	'autoload' => false,
			'default'  => 1,
		);
		$settings[] = array(
			'title'       => __( 'Text proforma', 'woo-smartbill' ),
			'desc'        => __( 'Textul care apare în email pentru a accesa proforma', 'woo-smartbill' ) . ' ' . sprintf( __( 'Placeholderele: %s', 'woo-smartbill' ), '{url_factura}' ),
			'id'          => 'woo_smartbill_proforma[email-text]',
			'css'         => 'width:400px; height: 75px;',
			'type'        => 'textarea',
			'default'     => 'Poți descărca proforma ta <a href="{url_proforma}">aici</a>',
			'autoload'    => false,
			'desc_tip'    => true,
		);
        $settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_proforma_email',
		);

		return $settings;

	}

	// Helper functions
	private function get_payment_methods(){

		$gateways = WC()->payment_gateways->payment_gateways();
		$return = array();

		foreach ( $gateways as $key => $gateway ) {
			$return[ $key ] = $gateway->title;
		}

		return $return;

	}

	public function get_emails(){

		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		$emails = array();

		foreach ( $email_templates as $email_key => $email_template ) {
			
			if ( $email_template->is_customer_email() ) {
				$emails[ $email_template->id ] = $email_template->get_title();
			}

		}

		return $emails;

	}

	// Bulk actions
	public function define_bulk_actions( $actions ) {

		if ( 'yes' != $this->get_option( 'enable' ) ) {
			return $actions;
		}

		$actions['woo_smartbill_proforma_generate'] = __( 'Genereaza proforma', 'woo-smartbill' );
		$actions['woo_smartbill_proforma_anulare']  = __( 'Anuleaza proforma', 'woo-smartbill' );

		return $actions;
	}

	// Handle bulk actions
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {

		if ( ! in_array( $action, array( 'woo_smartbill_proforma_generate', 'woo_smartbill_proforma_anulare' ) ) ) {
			return $redirect_to;
		}

		$ids     = apply_filters( 'woocommerce_bulk_action_ids',  array_map( 'absint', $ids ), $action, 'order' );
		$changed = 0;

		if ( in_array( $action, array( 'woo_smartbill_proforma_generate' ) ) ) {
			$ids = array_reverse( $ids );
		}

		foreach ( $ids as $id ) {

			$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
			switch ( $action ) {
				case 'woo_smartbill_proforma_generate':
					$woo_smartbill_wrapper->genereaza_proforma( $id );
					break;
				case 'woo_smartbill_proforma_anulare':
					$woo_smartbill_wrapper->anuleaza_proforma( $id );
					break;
			}

		}

		return esc_url_raw( $redirect_to );

	}

	public function handle_estimate_series( $data, $order_id ){

		$data['seriesName'] = $this->get_option( 'serie-proforma' );
		return $data;

	}

	public function check_proforma( $data, $order_id ){

		if ( ! $this->get_option( 'enable' ) ) {
			return $data;
		}

		$info   = get_post_meta( $order_id, 'woo_smartbil_proforma', true );
		$status = get_post_meta( $order_id, 'woo_smartbil_status_proforma', true );

		if ( $info && 'anulata' != $status ) {
			$data['useEstimateDetails'] = true;
			$data['estimate'] = array(
				'seriesName' => $info['serie'],
				'number'     => $info['numar'],
			);


			unset( $data['products'] );

		}

		return $data;

	}

	public function testare(){
		if ( ! isset( $_GET['generare'] ) ) {
			return;
		}
		$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
		$woo_smartbill_wrapper->anuleaza_proforma( '188' );
	}

	public function verifica_status_proforma( $check, $order ){

		if ( ! $check ) {
			return $check;
		}

		$status = get_post_meta( $order->ID, 'woo_smartbil_status_proforma', true );
		$info   = get_post_meta( $order->ID, 'woo_smartbil_proforma', true );

		if ( $status || $info ) {
			return false;
		}

		return $check;

	}

	public function afisare_info_proforma( $order ){

		$status = get_post_meta( $order->ID, 'woo_smartbil_status_proforma', true );
		$info   = get_post_meta( $order->ID, 'woo_smartbil_proforma', true );

		if ( $info || $status ) {
			
			echo '<div><span>Status proforma: </span><strong>' . $status . '</strong></div>';

			if ( ! empty( $info ) ) {
				echo '<div><span>Proforma: </span><strong><a href="' . $info['url'] . '" target="_blank">' . $info['serie'] . ' ' . $info['numar'] . '</a></strong></div>';
			}

		}

	}

	public function actiuni_proforma( $order ){

		$status = get_post_meta( $order->ID, 'woo_smartbil_status_proforma', true );
		$info   = get_post_meta( $order->ID, 'woo_smartbil_proforma', true );

		if ( ! $status && ! $info ) {
			echo '<div><a href="#" data-order-id="' . $order->ID . '" data-action="generare_proforma" target="_blank" class="button smartbill-button" style="margin-bottom:10px;">Genereaza proforma</a></div>';
		}

	}

	public function button_anulare_proforma( $order ){

		$status = get_post_meta( $order->ID, 'woo_smartbil_status_proforma', true );
		$info   = get_post_meta( $order->ID, 'woo_smartbil_proforma', true );
		
		if ( $status && 'anulata' != $status ) {
			echo '<div><a href="#" data-order-id="' . $order->ID . '" data-action="anulare_proforma" target="_blank" style="color: #a00;margin-top:10px;display:inline-block;" class="smartbill-button">Anuleaza proforma</a></div>';
		}

	}

	public function ajax_proforma( $action, $order_id, $woo_smartbill_wrapper ){

		if ( 'generare_proforma' == $action ) {
			$woo_smartbill_wrapper->genereaza_proforma( $order_id );
		}

		if ( 'anulare_proforma' == $action ) {
			$woo_smartbill_wrapper->anuleaza_proforma( $order_id );
		}

	}

	public function status_proforma( $smartbill_data, $order_id ){

		if ( 'ok' == $smartbill_data['status'] ) {

			$status = get_post_meta( $order_id, 'woo_smartbil_status_proforma', true );

			if ( 'emisa' == $status ) {
				update_post_meta( $order_id, 'woo_smartbil_status_proforma', 'facturata' );
			}

		}

	}

	// Order status changed
	public function order_status_change( $order_id, $old_status, $new_status ){

		if ( ! $this->get_option( 'enable' ) ) {
			return;
		}

		$status = 'wc-' . $new_status;

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();

		$statusuri_generare = $this->get_option( 'auto-generare' );
		$statusuri_generare = is_array( $statusuri_generare ) ? $statusuri_generare : array( $statusuri_generare );

		if ( in_array( $status, $statusuri_generare ) ) {
			$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
			$woo_smartbill_wrapper->genereaza_proforma( $order_id );
		}

	}

	public function adauga_proforma_la_email( $order, $sent_to_admin, $plain_text, $email ){

		$email_types = $this->get_option( 'email-types' );

		if ( empty( $email_types ) ) {
			return;
		}

		if ( ! in_array( $email->id, $email_types ) ) {
			return;
		}

		$email_text = $this->get_option( 'email-text' );

		if ( '' == $email_text ) {
			return;
		}

		$proforma = get_post_meta( $order->get_id(), 'woo_smartbil_proforma', true );

		if ( ! $proforma || ! isset( $proforma['url'] ) ) {

			if ( 'yes' == $this->get_option( 'force-email-creation' ) ) {
				$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
				$woo_smartbill_wrapper->genereaza_proforma( $order->get_id() );
				$proforma = get_post_meta( $order->get_id(), 'woo_smartbil_proforma', true );
			}else{
				return;
			}
			
		}

		$email_text = str_replace( '{url_proforma}', $proforma['url'], $email_text );
		echo wp_kses_post( $email_text );

	}

	public function adauga_proforma_la_my_acc( $actions, $order ){

		$proforma = get_post_meta( $order->get_id(), 'woo_smartbil_proforma', true );

		if ( $proforma && isset( $proforma['url'] ) ) {
			$actions['woo_smartbill'] = array(
				'url'  => $proforma['url'],
				'name' => 'Proforma',
			);
		}

		return $actions;

	}

}

new Woo_SmartBill_Proforma();