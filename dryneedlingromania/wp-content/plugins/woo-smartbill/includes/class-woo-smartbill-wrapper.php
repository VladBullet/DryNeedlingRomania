<?php

/**
 * 
 */
class Woo_SmartBill_Wrapper {

	function __construct() {
		add_filter( 'woo_smartbill_order_after_product', array( $this, 'add_sale' ), 10, 3 );

		add_filter( 'woo_smartbill_order_products', array( $this, 'add_cupons' ), 10, 3 );
		add_filter( 'woo_smartbill_order_products', array( $this, 'add_fees' ), 30, 3 );
		add_filter( 'woo_smartbill_order_products', array( $this, 'add_shipping' ), 50, 3 );

		// Trimite email
		add_action( 'woo_smartbill_dupa_genereaza_factura', array( $this, 'trimite_email' ), 50, 2 );
	}

	public static function get_instance() {
		static $inst;

		if ( ! $inst ) {
			$inst = new Woo_SmartBill_Wrapper();
		}

		return $inst;
	}

	public function genereaza( $order_id ){

		woo_smartbill_add_log( 'Generare factura ' . $order_id );

		$info = get_post_meta( $order_id, 'woo_smartbil_factura', true );

		if ( $info ) {
			woo_smartbill_add_log( 'Exista deja o factura pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Exista deja o factura pentru aceasta comanda.' );
		}

		$data = $this->get_invoice_data( $order_id );

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( '' != $woo_smartbill['mentiuni'] ) {
			$mentions = str_replace( '{order_id}', $order_id, $woo_smartbill['mentiuni'] );
			$data['mentions'] = $mentions;
		}

		if ( '' != $woo_smartbill['observatii'] ) {
			$mentions = str_replace( '{order_id}', $order_id, $woo_smartbill['observatii'] );
			$data['observations'] = $mentions;
		}

		if ( '' != $woo_smartbill['emitent-nume'] ) {
			$data['issuerName'] = $woo_smartbill['emitent-nume'];
		}

		if ( '' != $woo_smartbill['emitent-cnp'] ) {
			$data['issuerCnp'] = $woo_smartbill['emitent-cnp'];
		}

		/*
		* @hooked Woo_Smartbill_Settings::add_usestock - 20
		* @hooked Woo_SmartBill_Proforma::check_proforma - 30
		*/
		$data = apply_filters( 'woo_smartbill_genereaza_data', $data, $order_id );
        $data = apply_filters( 'woo_smartbill_data', $data, $order_id );

		$smartbill = SmartBill_Client::get_instance();
    	$smartbill_data = $smartbill->createInvoiceWithDocumentAddress($data);

    	$log_message = 'Status generare factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
    	woo_smartbill_add_log( $log_message );

		if ( 'ok' == $smartbill_data['status'] ) {
			
			if ( isset( $data['payment'] ) ) {
				$status = 'incasata';
			}else{
				$status = 'emisa';
			}

			
			$info = array(
				'serie' => $smartbill_data['series'],
				'numar' => $smartbill_data['number'],
				'url'   => $smartbill_data['documentViewUrl']
			);

			update_post_meta( $order_id, 'woo_smartbil_status_factura', $status );
			update_post_meta( $order_id, 'woo_smartbil_factura', $info );

			/*
			* @hooked Woo_SmartBill_Proforma::status_proforma - 10
			* @hooked Woo_SmartBill_Wrapper::trimite_email - 20
			*/
			do_action( 'woo_smartbill_dupa_genereaza_factura', $smartbill_data, $order_id );

			return array( 'status' => 'ok' );

		}else{
			return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
		}

	}

	public function incaseaza( $order_id ){

		woo_smartbill_add_log( 'Incasare factura ' . $order_id );
		
		$info = get_post_meta( $order_id, 'woo_smartbil_factura', true );

		if ( $info ) {
			
			$option_helper = Woo_SmartBill_Option_Helper::get_instance();
			$woo_smartbill = $option_helper->get_setting();

			$order = wc_get_order( $order_id );
			$data = array(
				'issueDate' => date( 'Y-m-d' ),
				'type'      => 'Card',
				'isCash'    => 0,
				'precision' => wc_get_price_decimals(),
				'currency'  => $woo_smartbill['moneda'],
				'useInvoiceDetails' => true,
				'invoicesList' => array(
					array(
						'seriesName' => $info['serie'],
						'number'     => $info['numar'],
					)
				)
				
			);

			$data['client'] = apply_filters( 'woo_smartbill_info_client', array(
	        	'name'       => $order->get_billing_last_name() . ' ' . $order->get_billing_first_name(),
	        	'address'    => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
	        	'city'       => $order->get_billing_city(),
	        	'county'     => '' != $order->get_billing_state() ? $order->get_billing_state() : $order->get_billing_city(),
	        	'country'    => $order->get_billing_country(),
	        	'email'      => $order->get_billing_email(),
	        	'phone'      => $order->get_billing_phone(),
	        	'saveToDb'   => boolval( $woo_smartbill['salvareclient'] ),
	        	'isTaxPayer' => 0,
	        ), $order_id, $order );

	        $data = apply_filters( 'woo_smartbill_incaseaza_data', $data, $order_id );
	        $data = apply_filters( 'woo_smartbill_data', $data, $order_id );

	        $smartbill = SmartBill_Client::get_instance();
    		$smartbill_data = $smartbill->createPayment($data);

    		$log_message = 'Status incasare factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
    		woo_smartbill_add_log( $log_message );

    		if ( 'ok' == $smartbill_data['status'] ) {
    			$status = 'incasata';
				update_post_meta( $order_id, 'woo_smartbil_status_factura', $status );
				return array( 'status' => 'ok' );
    		}else{
    			return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
    		}

		}

		return array( 'status' => 'nok', 'message' => 'Nu exista nicio factura pentru aceasta comanda.' );

	}

	public function storneaza( $order_id ){

		woo_smartbill_add_log( 'Stornare factura ' . $order_id );

		$info = get_post_meta( $order_id, 'woo_smartbil_factura', true );

		if ( ! $info ) {
			woo_smartbill_add_log( 'Nu exista nicio factura pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Nu exista nicio factura pentru aceasta comanda.' );
		}

		$data = $this->get_invoice_data( $order_id, true );

		$data = apply_filters( 'woo_smartbill_storneaza_data', $data, $order_id );
        $data = apply_filters( 'woo_smartbill_data', $data, $order_id );

        $data['mentions'] = 'Factura storno pentru ' . $info['serie'] . $info['numar'];

		$smartbill = SmartBill_Client::get_instance();
    	$smartbill_data = $smartbill->createInvoiceWithDocumentAddress($data);

    	$log_message = 'Status stornare factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
    	woo_smartbill_add_log( $log_message );

		if ( 'ok' == $smartbill_data['status'] ) {
			
			$status = 'stornata';
			
			$info = array(
				'serie' => $smartbill_data['series'],
				'numar' => $smartbill_data['number'],
				'url'   => $smartbill_data['documentViewUrl']
			);

			update_post_meta( $order_id, 'woo_smartbil_factura_storno', $info );
			update_post_meta( $order_id, 'woo_smartbil_status_factura', $status );

			return array( 'status' => 'ok' );

		}else{
			return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
		}

	}

	public function anuleaza( $order_id ){

		woo_smartbill_add_log( 'Anulare/Stergere factura ' . $order_id );

		$info   = get_post_meta( $order_id, 'woo_smartbil_factura', true );
		$status = get_post_meta( $order_id, 'woo_smartbil_status_factura', true );
		$sterge = false;

		if ( $info ) {
			
			$smartbill = SmartBill_Client::get_instance();

			$option_helper = Woo_SmartBill_Option_Helper::get_instance();
			$woo_smartbill = $option_helper->get_setting();

			if ( 'incasata' != $status && $woo_smartbill['stergere-factura'] ) {
				$sterge = true;
				$smartbill_data = $smartbill->deleteInvoice( $info['serie'], $info['numar'] );

				$log_message = 'Status stergere factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
    			woo_smartbill_add_log( $log_message );

				if ( 'nok' == $smartbill_data['status'] ) {
					$sterge = false;
					$smartbill_data = $smartbill->cancelInvoice( $info['serie'], $info['numar'] );

					$log_message = 'Status anulare factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
    				woo_smartbill_add_log( $log_message );
				}

			}else{
				$smartbill_data = $smartbill->cancelInvoice( $info['serie'], $info['numar'] );

				$log_message = 'Status anulare factura ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
				woo_smartbill_add_log( $log_message );
			}

			if ( 'ok' == $smartbill_data['status'] ) {

				if ( $sterge ) {
					delete_post_meta( $order_id, 'woo_smartbil_factura' );
				}
				update_post_meta( $order_id, 'woo_smartbil_status_factura', 'anulata' );

				return array( 'status' => 'ok' );
			}else{
				return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
			}

		}else{
			woo_smartbill_add_log( 'Nu exista nicio factura pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Nu exista nicio factura pentru aceasta comanda.' );
		}

	}

	public function verifica_incasare( $order_id ){

		woo_smartbill_add_log( 'Verificare incasare factura ' . $order_id );

		$info   = get_post_meta( $order_id, 'woo_smartbil_factura', true );
		$status = get_post_meta( $order_id, 'woo_smartbil_status_factura', true );

		if ( $info ) {
			
			$smartbill = SmartBill_Client::get_instance();
			$smartbill_data = $smartbill->statusInvoicePayments( $info['serie'], $info['numar'] );

			$log_message = 'Status verificare incasare ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
			woo_smartbill_add_log( $log_message );

			if ( 'ok' == $smartbill_data['status'] ) {

				if ( $smartbill_data['paid'] && 0 == $smartbill_data['unpaidAmount'] && 'incasata' != $status ) {
					update_post_meta( $order_id, 'woo_smartbil_status_factura', 'incasata' );
				}

				return array( 'status' => 'ok' );

			}else{
				return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
			}

		}else{
			woo_smartbill_add_log( 'Nu exista nicio factura pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Nu exista nicio factura pentru aceasta comanda.' );
		}

	}

	public function anuleaza_proforma( $order_id ){

		woo_smartbill_add_log( 'Anulare proforma ' . $order_id );

		$info   = get_post_meta( $order_id, 'woo_smartbil_proforma', true );
		$status = get_post_meta( $order_id, 'woo_smartbil_status_proforma', true );
		$sterge = false;

		if ( $info ) {
			
			$smartbill = SmartBill_Client::get_instance();

			$option_helper = Woo_SmartBill_Option_Helper::get_instance();
			$woo_smartbill = $option_helper->get_setting();

			if ( $woo_smartbill['stergere-factura'] ) {
				$sterge = true;
				$smartbill_data = $smartbill->deleteProforma( $info['serie'], $info['numar'] );

				if ( 'nok' == $smartbill_data['status'] ) {
					$sterge = false;
					$smartbill_data = $smartbill->cancelProforma( $info['serie'], $info['numar'] );
				}

			}else{
				$smartbill_data = $smartbill->cancelProforma( $info['serie'], $info['numar'] );
			}

			$log_message = 'Status anulare proforma ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
			woo_smartbill_add_log( $log_message );

			if ( 'ok' == $smartbill_data['status'] ) {

				if ( $sterge ) {
					delete_post_meta( $order_id, 'woo_smartbil_proforma' );
				}
				update_post_meta( $order_id, 'woo_smartbil_status_proforma', 'anulata' );

				return array( 'status' => 'ok' );
			}else{
				return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
			}

		}else{
			woo_smartbill_add_log( 'Nu exista nicio factura pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Nu exista nicio factura pentru aceasta comanda.' );
		}

	}

	public function genereaza_proforma( $order_id ){

		woo_smartbill_add_log( 'Generare proforma ' . $order_id );

		$info = get_post_meta( $order_id, 'woo_smartbil_proforma', true );

		if ( $info ) {
			woo_smartbill_add_log( 'Exista deja o proforma pentru comanda ' . $order_id );
			return array( 'status' => 'nok', 'message' => 'Exista deja o proforma pentru aceasta comanda.' );
		}

		$data = $this->get_invoice_data( $order_id );

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( '' != $woo_smartbill['mentiuni'] ) {
			$mentions = str_replace( '{order_id}', $order_id, $woo_smartbill['mentiuni'] );
			$data['mentions'] = $mentions;
		}

		/*
		* @hooked Woo_SmartBill_Proforma::handle_estimate_series - 10
		*/
		$data = apply_filters( 'woo_smartbill_genereaza_proforma_data', $data, $order_id );
        $data = apply_filters( 'woo_smartbill_proforma_data', $data, $order_id );

		$smartbill = SmartBill_Client::get_instance();
    	$smartbill_data = $smartbill->createProforma($data);

    	$log_message = 'Status generare proforma ' . $smartbill_data['status'] . ', mesaj ' . $smartbill_data['message'] . ' pentru comanda ' . $order_id;
		woo_smartbill_add_log( $log_message );

		if ( 'ok' == $smartbill_data['status'] ) {
			
			$info = array(
				'serie' => $smartbill_data['series'],
				'numar' => $smartbill_data['number'],
				'url'   => $smartbill_data['documentViewUrl']
			);

			update_post_meta( $order_id, 'woo_smartbil_status_proforma', 'emisa' );
			update_post_meta( $order_id, 'woo_smartbil_proforma', $info );

			return array( 'status' => 'ok' );

		}else{
			return array( 'status' => 'nok', 'message' => $smartbill_data['message'] );
		}

	}

	public function get_invoice_data( $order_id, $stornare = false ){
		$data = array();

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		$woo_smartbill_taxes = array();
		if ( $woo_smartbill['tva'] ) {
			$woo_smartbil_taxes = get_option( 'woo_smartbill_taxes', array() );
		}

		$order = wc_get_order($order_id);

		$country = $order->get_billing_country();
		$wc_countries = new WC_Countries();
		$wc_counties = $wc_countries->get_states( $country );
		$county = $order->get_billing_state();

		if ( isset( $wc_counties[ $county ] ) ) {
			$county = $wc_counties[$county];
		}

        // Client information
        $data['client'] = apply_filters( 'woo_smartbill_info_client', array(
        	'name'       => $order->get_billing_last_name() . ' ' . $order->get_billing_first_name(),
        	'address'    => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
        	'city'       => $order->get_billing_city(),
        	'county'     => $county,
        	'country'    => $order->get_billing_country(),
        	'email'      => $order->get_billing_email(),
        	'phone'      => $order->get_billing_phone(),
        	'saveToDb'   => boolval( $woo_smartbill['salvareclient'] ),
        	'isTaxPayer' => 0,
        ), $order_id, $order );

        $products = array();
        $discounted_products = array();

        // Products
        $order_items = $order->get_items();


        // subtotal with tax
        $order_sub_wtax = 0;

        // Colectare produse
        foreach ( $order_items as $order_item_id => $order_item ) {
        	$product = $order_item->get_product();
        	$qty     = method_exists( $product, 'get_quantity' ) ? $product->get_quantity() : $order_item['qty'];

        	if ( $stornare ) {
        		$price   = ($order_item->get_total() + $order_item->get_total_tax()) / $qty;
        	}else{
        		
        		if ( ! $woo_smartbill['discount'] ) {
					$price   = ($order_item->get_total() + $order_item->get_total_tax()) / $qty;
				}else{
					$price = ($order_item->get_subtotal() + $order_item->get_subtotal_tax()) / $qty;
				}
        		
        	}

        	$order_sub_wtax += $order_item->get_total() + $order_item->get_total_tax();
        	$price   = round( $price, wc_get_price_decimals() );

        	if ( $stornare ) {
        		$qty = '-' . $qty;
        	}

        	$smartbill_product = array(
        		// 'code'              => $this->get_sku( $product ),
        		'name'              => $order_item['name'],
        		'measuringUnitName' => $woo_smartbill['um'],
        		'currency'          => $woo_smartbill['moneda'],
        		'quantity'          => $qty,
        		'price'             => $price,
        		'saveToDb'          => false,
			);

			if ( $woo_smartbill['tva'] ) {

				$tax_class = $order_item->get_tax_class();
        		if ( 'inherit' == $tax_class ) {
        			$tax_class = '';
        		}
						
				$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
                $rate = array_shift($taxes);

                if ( isset( $woo_smartbil_taxes[ $rate->tax_rate_id ] ) ) {
                	$smartbill_product['isTaxIncluded'] = 1;
					$smartbill_product['taxName'] = $woo_smartbil_taxes[ $rate->tax_rate_id ];
					$smartbill_product['taxPercentage'] = $rate->tax_rate;
                }
				
			}

			/*
			* @hooked Woo_SmartBill_YITH_Bundle::parse_products - 50
			*/
			$smartbill_product = apply_filters( 'woo_smartbill_order_product', $smartbill_product, $order_item, $stornare );
			if ( $smartbill_product ) {
				$products[] = $smartbill_product;
			}

			/*
	        @hooked Woo_SmartBill_Wrapper:add_sale - 10
	        */
			$products = apply_filters( 'woo_smartbill_order_after_product', $products, $order_item, $stornare );

        }

        /*
        @hooked Woo_SmartBill_Wrapper:add_cupons   - 10
        @hooked Woo_SmartBill_Wrapper:add_fees     - 30
        @hooked Woo_SmartBill_Wrapper:add_shipping - 50
        */
        $products = apply_filters( 'woo_smartbill_order_products', $products, $order, $stornare );

        $data['products']   = $products;
        $data['precision']  = wc_get_price_decimals();
        $data['issueDate']  = date( 'Y-m-d' );
        $data['seriesName'] = $woo_smartbill['serie-factura'];

        if ( $woo_smartbill['scadenta'] && ! $stornare ) {
        	$data['dueDate'] = date('Y-m-d', strtotime('+' . absint( $woo_smartbill['nr_zile_scadenta'] ) . ' days'));
        }

        $payment_method = $order->get_payment_method();
        if ( in_array( $payment_method, $woo_smartbill['incasare'] ) ) {
        	$total = $order->get_total();
        	$data['payment'] = array(
        		'value'    => $total,
        		'type'     => 'Card',
        		'isCash'   => 0,
        	);
        }

        if ( $stornare ) {
        	$data['payment'] = array(
        		'value'    => '-' . $order_sub_wtax,
        		'type'     => 'Alta incasare',
        		'isCash'   => 1,
        	);
        }

		return $data;
	}

	public function add_sale( $products, $order_item, $stornare ){
		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( $stornare ) {
			return $products;
		}

		if ( ! $woo_smartbill['sale'] ) {
			return $products;
		}

		$last_index = count( $products ) - 1;

		$product = $order_item->get_product();
		if ( $products[ $last_index ]['price'] < $product->get_regular_price() ) {
			$sale_price = ($product->get_regular_price() - $products[ $last_index ]['price'])*$products[ $last_index ]['quantity'];
			$products[ $last_index ]['price'] = $product->get_regular_price();

			$smartbill_discount = array(
        		'code'              => 'discount-' . $this->get_sku( $product ),
        		'name'              => 'Discount pentru ' . $product->get_name(),
        		'isDiscount'        => 1,
        		'measuringUnitName' => $woo_smartbill['um'],
        		'currency'          => $woo_smartbill['moneda'],
        		'discountValue'     => '-'.$sale_price,
        		'discountType'      => 1,
        		'numberOfItems'     => 1,
			);

			if ( $woo_smartbill['tva'] ) {
				$woo_smartbil_taxes = get_option( 'woo_smartbill_taxes', array() );

				$tax_class = $order_item->get_tax_class();
        		if ( 'inherit' == $tax_class ) {
        			$tax_class = '';
        		}
				
				$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
                $rate = array_shift($taxes);

                if ( isset( $woo_smartbil_taxes[ $rate->tax_rate_id ] ) ) {
                	$smartbill_discount['isTaxIncluded'] = 1;
					$smartbill_discount['taxName'] = $woo_smartbil_taxes[ $rate->tax_rate_id ];
					$smartbill_discount['taxPercentage'] = $rate->tax_rate;
                }
			}

			$smartbill_discount = apply_filters( 'woo_smartbill_order_product_onsale', $smartbill_discount, $order_item, $stornare );
			if ( $smartbill_discount ) {
				$products[] = $smartbill_discount;
			}

		}

		return $products;

	}

	public function add_cupons( $products, $order, $stornare ){
		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( ! $woo_smartbill['discount'] ) {
			return $products;
		}

		if ( $stornare ) {
			return $products;
		}

		$coupons = $order->get_items( 'coupon' );
		$order_items = $order->get_items();

		$total_discounted = 0;

		foreach ( $coupons as $item_id => $item ){
			$smartbill_cupon = array(
        		'code'              => 'cupon-' . sanitize_title( $item->get_code() ),
        		'name'              => 'Cupon ' . $item->get_code(),
        		'isDiscount'        => 1,
        		'measuringUnitName' => $woo_smartbill['um'],
        		'currency'          => $woo_smartbill['moneda'],
        		'discountValue'     => '-' . $item->get_discount(),
        		'discountType'      => 1,
			);

			if ( $woo_smartbill['tva'] ) {
				$woo_smartbil_taxes = get_option( 'woo_smartbill_taxes', array() );

				$tax_class = $item->get_tax_class();
        		if ( 'inherit' == $tax_class ) {
        			$tax_class = '';
        		}
				
				$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
                $rate = array_shift($taxes);

                if ( isset( $woo_smartbil_taxes[ $rate->tax_rate_id ] ) ) {
                	$smartbill_cupon['isTaxIncluded'] = 0;
					$smartbill_cupon['taxName'] = $woo_smartbil_taxes[ $rate->tax_rate_id ];
					$smartbill_cupon['taxPercentage'] = $rate->tax_rate;
                }
			}

			$total_discounted += $item->get_discount();

			$smartbill_cupon = apply_filters( 'woo_smartbill_order_cupon', $smartbill_cupon, $item, $stornare );
            if ( $smartbill_cupon ) {
            	$products[] = $smartbill_cupon;
            }

		}

		// Verificam daca nu pierdem la rotunjirea zecimalelor.
		$order_discounted = $order->get_total_discount();
		if ( $total_discounted < $order_discounted ) {
			$last_index = count( $products ) - 1;
			$discounted = str_replace( '-', '', $products[ $last_index ]['discountValue'] );
			$discounted = floatval( $discounted );
			$discounted += $order_discounted - $total_discounted;

			$products[ $last_index ]['discountValue'] = '-' . $discounted;
		}

		return $products;
	}

	public function add_fees( $products, $order, $stornare ){
		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( ! $woo_smartbill['fee'] ) {
			return $products;
		}

		// Daca este stornare si nu vrem sa stornam si transportul nu il adaugam pe factura storno
		if ( $stornare && 'yes' != $woo_smartbill['stornare-fee'] ) {
			return $products;
		}

		$fees = $order->get_items('fee');
		if ( empty( $fees ) ) {
			return $products;
		}

		foreach( $fees as $item_id => $item_fee ){

		    $fee = array(
	    		'code'              => sanitize_title( $item_fee->get_name() ),
	    		'name'              => $item_fee->get_name(),
	    		'measuringUnitName' => $woo_smartbill['um'],
	    		'currency'          => $woo_smartbill['moneda'],
	    		'quantity'          => 1,
	    		'price'             => number_format( $item_fee->get_total() + $item_fee->get_total_tax(), 2 ),
	    		'saveToDb'          => boolval( $woo_smartbill['salvareprodus'] ),
	    		'isService'         => true,
			);

		    if ( 'yes' == $woo_smartbill['tva'] ) {
		    	$woo_smartbil_taxes = get_option( 'woo_smartbill_taxes', array() );

				$tax_class = $item_fee->get_tax_class();
				if ( 'inherit' == $tax_class ) {
					$tax_class = '';
				}
				$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
		        $rate = array_shift($taxes);

		        if ( isset( $woo_smartbil_taxes[ $rate->tax_rate_id ] ) ) {
		        	$fee['isTaxIncluded'] = 1;
					$fee['taxName'] = $woo_smartbil_taxes[ $rate->tax_rate_id ];
					$fee['taxPercentage'] = $rate->tax_rate;
		        }

		    }

		    $fee = apply_filters( 'woo_smartbill_order_fee', $fee, $item_fee, $stornare );
            if ( $fee ) {
            	$products[] = $fee;
            }

		}

		return $products;
	}

	public function add_shipping( $products, $order, $stornare ){
		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		// Daca nu vrem transportul pe factura nu il adaugam
		if ( ! $woo_smartbill['transport'] ) {
			return $products;
		}

		// Daca este stornare si nu vrem sa stornam si transportul nu il adaugam pe factura storno
		if ( $stornare && 'yes' != $woo_smartbill['stornare-transport'] ) {
			return $products;
		}

		$woo_smartbil_taxes = array();
		if ( 'yes' == $woo_smartbill['tva'] ) {
			$woo_smartbil_taxes = get_option( 'woo_smartbill_taxes', array() );
		}

		$line_items_shipping = $order->get_items( 'shipping' );
        		
		foreach ( $line_items_shipping as $shipping_item_id => $shipping_item ) {

			$price = $shipping_item->get_total() + $shipping_item->get_total_tax();

			if ( 0 == $price ) {
				continue;
			}

			$smartbill_transport = array(
    			'code'              => 'transport',
        		'name'              => $woo_smartbill['text-transport'],
        		'measuringUnitName' => $woo_smartbill['um'],
        		'currency'          => $woo_smartbill['moneda'],
        		'quantity'          => 1,
        		'price'             => $price,
        		'isService'         => 1,
    		);

    		if ( $stornare && $woo_smartbill['stornare-transport'] ) {
    			$smartbill_transport['quantity'] = '-1';
    		}

    		$tax_class = $shipping_item->get_tax_class();
    		if ( 'inherit' == $tax_class ) {
    			$tax_class = '';
    		}
    		$taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
            $rate = array_shift($taxes);

            if ( isset( $woo_smartbil_taxes[ $rate->tax_rate_id ] ) ) {
            	$smartbill_transport['isTaxIncluded'] = 1;
				$smartbill_transport['taxName'] = $woo_smartbil_taxes[ $rate->tax_rate_id ];
				$smartbill_transport['taxPercentage'] = $rate->tax_rate;
            }

            $smartbill_transport = apply_filters( 'woo_smartbill_order_shipping', $smartbill_transport, $shipping_item, $stornare );
            if ( $smartbill_transport ) {
            	$products[] = $smartbill_transport;
            }

		}

		return $products;
	}


	// helper functions
	private function get_sku( $product ){

		$product_sku = $product->get_sku();
        if ( empty( $product_sku ) ) {
            $product_sku = sanitize_title( $product->get_name() );
        }
        return $product_sku;

	}

	public function trimite_email( $smartbill_data, $order_id ){

		$option_helper = Woo_SmartBill_Option_Helper::get_instance();
		$woo_smartbill = $option_helper->get_setting();

		if ( 'yes' != $woo_smartbill['email-smartbill'] ) {
			return;
		}

		$woo_smartbill = get_option( 'woo_smartbill', array() );
		$cui = '';
		if ( isset( $woo_smartbill['codfiscal'] ) && '' != $woo_smartbill['codfiscal'] ){
	        $cui = $woo_smartbill['codfiscal'];
	    }

		$smartbill = SmartBill_Client::get_instance();

		$data = array(
			'companyVatCode' => $cui,
			'seriesName'     => $smartbill_data['series'],
			'number'     	 => $smartbill_data['number'],
			'type'           => 'factura',
		);

		$response = $smartbill->sendDocument( $data );

		$log_message = 'Status emitere email ' . $response['status'] . ', mesaj ' . $response['message'] . ' pentru comanda ' . $order_id;
		woo_smartbill_add_log( $log_message );

	}

}