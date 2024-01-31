<?php

class Woo_SmartBill_YITH_Bundle {

	private $options = array();
	private $bundles = array();

	public function __construct() {

		// add_filter( 'woo_smartbill_order_product', array( $this, 'parse_products' ), 50, 3 );

	}

	private function get_options(){
		if ( empty( $this->options ) ) {
			$defaults = array( 'bundle' => 'no', 'products' => 'no' );
			$options = get_option( 'woo_smartbill_integrari_bunlde', array() );
			$this->options = wp_parse_args( $options, $defaults );
		}

		return $this->options;
	}

	public function parse_products( $smartbill_product, $order_item, $stornare ){
		$options = $this->get_options();
		$product = $order_item->get_product();

		if ( 'no' == $options['bundle'] && $product->is_type( 'yith_bundle' ) ) {
			return $smartbill_product;
		}

		if ( 'no' == $options['products'] && ! $product->is_type( 'yith_bundle' ) && 'no' == $options['bundle'] ) {
			return $smartbill_product;
		}

		if ( $product->is_type( 'yith_bundle' ) ) {
			$_data = array();

			$bundle_data = $order_item->get_meta( '_cartstamp', true );
			$qty     = method_exists( $product, 'get_quantity' ) ? $product->get_quantity() : $order_item['qty'];
        	$_data['price'] = ($order_item->get_total() + $order_item->get_total_tax()) / $qty;
        	$_data['price'] = round( $_data['price'], wc_get_price_decimals() );
        	$_data['total_price'] = $this->get_total( $bundle_data );
			
			
			// echo $order_item->get_id();
			// print_r( $bundle_data );
			$_data['products'] = array();
			foreach ( $bundle_data as $bundle_product ) {
				$_p_data = array();
				
				$_b_product = wc_get_product( $bundle_product['product_id'] );
				$_p_data['total_price'] = wc_get_price_including_tax( $_b_product ) * $bundle_product['quantity'];

				$price = $_p_data['total_price'] * $_data['price'] / $_data['total_price'];
				$_p_data['price'] = $price / $bundle_product['quantity'];
				$_p_data['price'] = round( $_p_data['price'], wc_get_price_decimals() );
				$_p_data['is_used'] = false;


				$_data['products'][ $bundle_product['product_id'] ] = $_p_data;
			}

			$this->bundles[ $product->get_id() ] = $_data;
		}

		if ( 'yes' == $options['bundle'] && $product->is_type( 'yith_bundle' )  ) {
			return false;
		}

		$is_bundle = $order_item->get_meta( '_bundled_by', true );
		if ( 'yes' == $options['products'] && ! $product->is_type( 'yith_bundle' ) && $is_bundle ) {
			return false;
		}

		if ( ! $is_bundle ) {
			return $smartbill_product;
		}

		if ( 0 != $smartbill_product['price'] ) {
			return $smartbill_product;
		}

		$bundle_item = $this->search_product_in_bundle( $product->get_id() );

		if ( ! $bundle_item ) {
			return $smartbill_product;
		}

		$smartbill_product['price'] = $bundle_item['price'];
		return $smartbill_product;

	}


	// Helper functions
	private function get_total( $bundle_data ){
		$total = 0;
		foreach ( $bundle_data as $bundle_product ) {
			$_b_product = wc_get_product( $bundle_product['product_id'] );
			$total += $bundle_product['quantity'] * wc_get_price_including_tax( $_b_product );
		}
		return $total;
	}

	private function search_product_in_bundle( $id ){
		foreach ($this->bundles as $bundle_id => $bundle_data ) {
			if ( isset( $bundle_data['products'][ $id ] ) && ! $bundle_data['products'][ $id ]['is_used'] ) {
				$bundle_data['products'][ $id ]['is_used'] = true;
				return $bundle_data['products'][ $id ];
			}
		}

		return false;
	}

}

new Woo_SmartBill_YITH_Bundle();