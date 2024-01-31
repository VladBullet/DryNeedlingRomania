<?php

class Woo_Smartbill_Admin {
	
	function __construct() {}

	public function setting_page_class( $settings ) {
		$settings[] = include 'class-woo-smartbill-settings.php';
		return $settings;
	}

	public function register_wc_admin_tabs( $tabs_with_sections ){
		$tabs_with_sections['woo-smartbill'] = array( '', 'setari' );
		return $tabs_with_sections;
	}

	public function wc_admin_connect_page(){

		if ( ! function_exists( 'wc_admin_connect_page' ) ) {
			return;
		}

		$admin_page_base    = 'admin.php';

		wc_admin_connect_page(
			array(
				'id'        => 'woocommerce-settings-smartbill',
				'parent'    => 'woocommerce-settings',
				'screen_id' => 'woocommerce_page_wc-settings-woo-smartbill',
				'title'     => array(
					__( 'Smartbill', 'woo-smartbill' ),
				),
				'path'      => add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => 'woo-smartbill',
					),
					$admin_page_base
				),
			)
		);

	}

	// Custom post statuses
	public function register_custom_order_status() {

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		if ( ! $option_helper->get_setting( 'custom-status' ) || 'no' == $option_helper->get_setting( 'custom-status' ) ) {
			return;
		}

	    register_post_status( 'wc-expediat', array(
	        'label'                     => 'Expediat',
	        'public'                    => true,
	        'exclude_from_search'       => false,
	        'show_in_admin_all_list'    => true,
	        'show_in_admin_status_list' => true,
	        'label_count'               => _n_noop( 'Expediat (%s)', 'Expediate (%s)', 'woo-smartbill' )
	    ) );

	}

	// Add to list of WC Order statuses
	public function add_custom_statuses_to_order_statuses( $order_statuses ) {

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		if ( ! $option_helper->get_setting( 'custom-status' ) || 'no' == $option_helper->get_setting( 'custom-status' ) ) {
			return $order_statuses;
		}

		$ordered = array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-expediat', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' );
	 	$label = array(
	 		'wc-expediat' => 'Expediat',
	 	);

	    $new_order_statuses = array();
	 
	    // add new order status after processing
	    foreach ( $ordered as $ordered_status_key ) {
	 		
	 		if ( isset( $order_statuses[ $ordered_status_key ] ) ) {
	 			$new_order_statuses[ $ordered_status_key ] = $order_statuses[ $ordered_status_key ];
	 		}elseif ( isset( $label[ $ordered_status_key ] ) ) {
	 			$new_order_statuses[ $ordered_status_key ] = $label[ $ordered_status_key ];
	 		}

	    }
	 
	    return $new_order_statuses;
	}

	// Bulk actions
	public function define_bulk_actions( $actions ) {

		$actions['woo_smartbill_generate'] = __( 'Genereaza factura', 'woo-smartbill' );
		$actions['woo_smartbill_incasare'] = __( 'Incaseaza factura', 'woo-smartbill' );
		$actions['woo_smartbill_anulare']  = __( 'Anuleaza factura', 'woo-smartbill' );
		$actions['woo_smartbill_stornare'] = __( 'Stornare factura', 'woo-smartbill' );
		$actions['woo_smartbill_verifica'] = __( 'Verifica factura', 'woo-smartbill' );

		return $actions;
	}

	// Handle bulk actions
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {

		if ( ! in_array( $action, array( 'woo_smartbill_generate', 'woo_smartbill_incasare', 'woo_smartbill_stornare', 'woo_smartbill_anulare', 'woo_smartbill_verifica' ) ) ) {
			return $redirect_to;
		}

		$ids     = apply_filters( 'woocommerce_bulk_action_ids',  array_map( 'absint', $ids ), $action, 'order' );
		$changed = 0;

		if ( in_array( $action, array( 'woo_smartbill_generate', 'woo_smartbill_incasare' ) ) ) {
			$ids = array_reverse( $ids );
		}

		foreach ( $ids as $id ) {

			$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
			switch ( $action ) {
				case 'woo_smartbill_generate':
					$woo_smartbill_wrapper->genereaza( $id );
					break;
				case 'woo_smartbill_incasare':
					$woo_smartbill_wrapper->incaseaza( $id );
					break;
				case 'woo_smartbill_anulare':
					$woo_smartbill_wrapper->anuleaza( $id );
					break;
				case 'woo_smartbill_stornare':
					$woo_smartbill_wrapper->storneaza( $id );
					break;
				case 'woo_smartbill_verifica':
					$woo_smartbill_wrapper->verifica_incasare( $id );
					break;
			}

		}

		return esc_url_raw( $redirect_to );

	}

	// Order status changed
	public function order_status_change( $order_id, $old_status, $new_status ){

		$status = 'wc-' . $new_status;

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();

		$statusuri_generare = $option_helper->get_setting( 'auto-generare' );
		$statusuri_stornare = $option_helper->get_setting( 'stornare' );

		// Verificare statusuri generare
		$statusuri_generare = is_array( $statusuri_generare ) ? $statusuri_generare : array( $statusuri_generare );
		if ( in_array( $status, $statusuri_generare ) ) {
			$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
			$woo_smartbill_wrapper->genereaza( $order_id );
		}

		// Verificare statusuri stornare
		$statusuri_stornare = is_array( $statusuri_stornare ) ? $statusuri_stornare : array( $statusuri_stornare );
		if ( in_array( $status, $statusuri_stornare ) ) {
			$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
			$woo_smartbill_wrapper->storneaza( $order_id );
		}

	}

	// Order metabox
	public function order_metabox() {
		add_meta_box( 'woo_smartbill_metabox', 'Facturare SmartBill', array(
			$this,
			'display_order_metabox',
		), 'shop_order', 'side', 'high' );
	}
	public function display_order_metabox( $post ){
		$status       = get_post_meta( $post->ID, 'woo_smartbil_status_factura', true );
		$info_factura = get_post_meta( $post->ID, 'woo_smartbil_factura', true );
		$storno       = get_post_meta( $post->ID, 'woo_smartbil_factura_storno', true );

		

		$check = ( ! $info_factura && 'anulata' != $status );

		// $order = wc_get_order( $post->ID );
		// $order_items = $order->get_items();

		// print_r( $order_items );

		// $woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
		// print_r( $woo_smartbill_wrapper->get_invoice_data( $post->ID ) );


		/*
		* @hooked Woo_SmartBill_Proforma::verifica_status_proforma - 10
		*/
		if ( apply_filters( 'woo_smartbill_metabox_check_invoices', $check, $post ) ) {
			echo '<p>Pentru aceasta comanda nu s-a generat nicio factura sau proforma</p>';
		}
		
		do_action( 'woo_smartbill_metabox_before_info', $post );

		if ( $info_factura || $status ) {

			echo '<div>';
			echo '<div><span>Status: </span><strong>' . $status . '</strong></div>';

			if ( 'anulata' != $status ) {
				echo '<div><span>Numar: </span><strong><a href="' . $info_factura['url'] . '" target="_blank">' . $info_factura['serie'] . ' ' . $info_factura['numar'] . '</a></strong></div>';
			}

			echo '</div>';
		}
		if ( $storno ) {
			echo '<div><span>Factura Storno: </span><strong><a href="' . $storno['url'] . '" target="_blank">' . $storno['serie'] . ' ' . $storno['numar'] . '</a></strong></div>';
		}

		/*
		* @hooked Woo_SmartBill_Proforma::afisare_info_proforma - 10
		*/
		do_action( 'woo_smartbill_metabox_after_info', $post );

		if ( '' == $status || 'emisa' == $status ) {
			echo '<br><br><div><strong>Actiuni</strong></div><br>';
		}

		/*
		* @hooked Woo_SmartBill_Proforma::actiuni_proforma - 10
		*/
		do_action( 'woo_smartbill_metabox_before_actions', $post );
		
		if ( '' == $status ) {
			echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="generare" target="_blank" class="button smartbill-button">Genereaza factura</a></div>';
		}

		if ( 'emisa' == $status ) {
			echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="incasare" target="_blank" class="button smartbill-button">Incaseaza factura</a></div>';
			echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="anulare" target="_blank" style="color: #a00;" class="smartbill-button">Anuleaza factura</a></div>';
		}

		if ( 'emisa' == $status || 'incasata' == $status ) {
			echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="stornare" target="_blank" style="color: #a00;" class="smartbill-button">Storneaza factura</a></div>';
		}

		if ( '' != $status ) {
			echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="stergere" target="_blank" style="color: #a00;" class="smartbill-button">Sterge factura</a></div>';
		}

		/*
		* @hooked Woo_SmartBill_Proforma::button_anulare_proforma - 10
		*/
		do_action( 'woo_smartbill_metabox_after_actions', $post );

	}

	public function add_column( $columns ) {
	    $columns['woo_smartbill'] = "Factura";
	    return $columns;
	}

	public function show_column_content( $column ) {
	    global $post;

	    if ( 'woo_smartbill' == $column ) {
	    	$info   = get_post_meta( $post->ID, 'woo_smartbil_factura', true );
			$status = get_post_meta( $post->ID, 'woo_smartbil_status_factura', true );
			$storno = get_post_meta( $post->ID, 'woo_smartbil_factura_storno', true );

			/*
			* @hooked Woo_SmartBill_Proforma::afisare_info_proforma - 10
			*/
			do_action( 'woo_smartbill_metabox_after_info', $post );

			if ( $status ) {
				echo '<div><strong>Status factura: </strong>' . $status . '</div>';
			}

			if ( $info ) {
				echo '<div><span>Numar: </span><strong><a href="' . $info['url'] . '" target="_blank">' . $info['serie'] . ' ' . $info['numar'] . '</a></strong></div>';
			}

			if ( $storno ) {
				echo '<div><span>Factura Storno: </span><strong><a href="' . $storno['url'] . '" target="_blank">' . $storno['serie'] . ' ' . $storno['numar'] . '</a></strong></div>';
			}

			if ( ! $status && ! $info && ! $storno ) {
				echo '<div><a href="#" data-order-id="' . $post->ID . '" data-action="generare" target="_blank" class="button smartbill-button">Genereaza factura</a></div>';
			}

	    }

	}

	public function admin_scripts( $hook ){

		$screen = get_current_screen();

		if ( 'shop_order' != $screen->post_type && 'woocommerce_page_wc-settings' != $screen->base ) {
			return;
		}

		wp_enqueue_style('woo-smartbill-settings', WOO_SMARTBILL_URL . 'assets/css/settings.css', array(), WOO_SMARTBILL_VERSION, false );

		wp_enqueue_script( 'woo-smartbill', WOO_SMARTBILL_URL . 'assets/js/woo-smartbill.js', array(), WOO_SMARTBILL_VERSION, true );

		$ajax_info = array(
		    'url'    => admin_url( 'admin-ajax.php' ),
		    '_nonce' => wp_create_nonce( 'woo_smartbill' )
		);
		wp_localize_script( 'woo-smartbill', 'woo_smartbill', $ajax_info );
	}

	public function prelucrare_factura(){
		
		if ( ! isset( $_POST['woo_smartbill_nonce'] ) || ! isset( $_POST['smartbill_action'] ) || ! isset( $_POST['order_id'] ) ) {
			$error = new WP_Error( '001', 'A aparut o problema te rugam sa incerci din nou.' );
			wp_send_json_error( $error );
		}

		if ( ! wp_verify_nonce( $_POST['woo_smartbill_nonce'], 'woo_smartbill' ) ){
			$error = new WP_Error( '002', 'A aparut o problema te rugam sa incerci din nou.' );
			wp_send_json_error( $error );
		}

		$order_id = absint( $_POST['order_id'] );
		$action   = sanitize_title( $_POST['smartbill_action'] );

		$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
		switch ( $action ) {
			case 'generare':
				$response = $woo_smartbill_wrapper->genereaza( $order_id );
				break;
			case 'incasare':
				$response = $woo_smartbill_wrapper->incaseaza( $order_id );
				break;
			case 'anulare':
				$response = $woo_smartbill_wrapper->anuleaza( $order_id );
				break;
			case 'stornare':
				$response = $woo_smartbill_wrapper->incaseaza( $order_id );
				$response = $woo_smartbill_wrapper->storneaza( $order_id );
				break;
			case 'stergere':
				delete_post_meta( $order_id, 'woo_smartbil_status_factura' );
				delete_post_meta( $order_id, 'woo_smartbil_factura' );
				delete_post_meta( $order_id, 'woo_smartbil_factura_storno' );
			default:
				do_action( 'woo_smartbill_order_ajax_action', $action, $order_id, $woo_smartbill_wrapper );
				break;
		}

		if ( isset( $response['status'] ) && 'nok' == $response['status'] ) {
			$error = new WP_Error( '111', $response['message'] );
			wp_send_json_error( $error );
		}else{
			wp_send_json_success();
		}
		

	}

	public function refresh_cloud(){

		$action = isset( $_POST['smartbill_action'] ) ? $_POST['smartbill_action'] : '';

		if ( '' == $action ) {
			wp_send_json_error();
		}

		$smartbill_cloud = Woo_SmartBill_Cloud_Options::instance();

		switch ( $action ) {
			case 'serii-proforma':
				$smartbill_cloud->serii_proforme( true );
				break;
			case 'serii-facturi':
				$smartbill_cloud->serii_facturi( true );
				break;
			case 'um':
				$smartbill_cloud->um( true );
				break;
			case 'taxe':
				$smartbill_cloud->taxe( true );
				break;
			case 'depozite':
				$smartbill_cloud->depozite( true );
				break;
			
			default:
				do_action( 'woo_smartbill_refresh_cloud', $action, $smartbill_cloud );
				break;
		}


		wp_send_json_success();

	}

}

