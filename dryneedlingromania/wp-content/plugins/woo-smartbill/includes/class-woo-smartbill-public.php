<?php

class Woo_Smartbill_Public {
	
	function __construct() {}

	public function check_cron(){
		$woo_smartbill = get_option( 'woo_smartbill_options', array( 'verificare-incasare' => 'no' ) );

		if ( 'yes' == $woo_smartbill['verificare-incasare'] ) {
			if ( !wp_next_scheduled( 'woo_smartbill_verificare_facturi' ) ) {
		        wp_schedule_event(time(), 'daily', 'woo_smartbill_verificare_facturi');
		    }
		}else{
			$timestamp = wp_next_scheduled( 'woo_smartbill_verificare_facturi' );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, 'woo_smartbill_verificare_facturi' );
			}
		}

	}

	public function verifica_facturi(){

		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'meta_query'     => array(
				array(
		            'key'     => 'woo_smartbil_status_factura',
		            'value'   => 'emisa',
		            'compare' => '=',
		        ),
			),
			'fields' => 'ids',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			foreach ($query->posts as $order_id) {
				$smartbill = Woo_SmartBill_Wrapper::get_instance();
				$smartbill->verifica_incasare( $order_id );

				if ( 'incasata' == get_post_meta( $order_id, 'woo_smartbil_status_factura', true ) ) {
					$order = wc_get_order( $order_id );
    				$order->update_status( 'completed' );
				}

			}

		}

	}

	public function adauga_factura_la_email( $order, $sent_to_admin, $plain_text, $email ){

		$woo_smartbill = get_option( 'woo_smartbill_options', array() );

		if ( empty( $woo_smartbill ) ) {
			return;
		}

		if ( ! isset( $woo_smartbill['email-types'] ) ) {
			return;
		}

		if ( ! is_array( $woo_smartbill['email-types'] ) ) {
			return;
		}

		if ( ! in_array( $email->id, $woo_smartbill['email-types'] ) ) {
			return;
		}

		if ( ! isset( $woo_smartbill['email-text'] ) || '' == $woo_smartbill['email-text'] ) {
			return;
		}

		$factura = get_post_meta( $order->get_id(), 'woo_smartbil_factura', true );

		if ( ! $factura || ! isset( $factura['url'] ) ) {

			if ( 'yes' == $woo_smartbill['force-email-creation'] ) {
				$woo_smartbill_wrapper = Woo_SmartBill_Wrapper::get_instance();
				$woo_smartbill_wrapper->genereaza( $order->get_id() );
				$factura = get_post_meta( $order->get_id(), 'woo_smartbil_factura', true );
			}else{
				return;
			}
			
		}

		$email_text = str_replace( '{url_factura}', $factura['url'], $woo_smartbill['email-text'] );
		echo wp_kses_post( $email_text );

	}

	public function adauga_factura_la_my_acc( $actions, $order ){

		$factura = get_post_meta( $order->get_id(), 'woo_smartbil_factura', true );

		if ( $factura && isset( $factura['url'] ) ) {
			$actions['woo_smartbill'] = array(
				'url'  => $factura['url'],
				'name' => 'Factura',
			);
		}

		return $actions;

	}

}
