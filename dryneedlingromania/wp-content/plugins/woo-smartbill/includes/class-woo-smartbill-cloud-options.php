<?php

/**
 * 
 */
class Woo_SmartBill_Cloud_Options {

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	private $options   = array();
	private $defaults  = array();
	private $smartbill = false;
	
	function __construct() {
		$this->smartbill = SmartBill_Client::get_instance();

		$this->defaults = apply_filters( 'woo_smartbill_cloud_options_defaults', array(
			'serii_facturi'  => array(),
			'serii_proforme' => array(),
			'um'             => array(),
			'taxe'           => array(),
			'depozite'       => array(),
		));

		$this->options = get_option( 'woo_smartbill_cloud_options', array() );
		$this->options = wp_parse_args( $this->options, $this->defaults );

	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function update(){
		update_option( 'woo_smartbill_cloud_options', $this->options, false );
	}

	public function refresh(){
		$this->options = get_option( 'woo_smartbill_cloud_options', array() );
		$this->options = wp_parse_args( $this->options, $this->defaults );
	}

	public function serii_facturi( $force = false ){

		$serii = $this->options['serii_facturi'];

		if ( empty( $serii ) || $force ) {
			$smartbill_response = $this->smartbill->getDocumentSeries();

			if ( 'ok' == $smartbill_response['status'] ) {
				$serii = wp_list_pluck( $smartbill_response['list'], 'name', 'name' );
				$serii = array_merge( array( 'Alege serie factura' ), $serii );

				$this->options['serii_facturi'] = $serii;
				$this->update();

			}else{
				$serii = array( 'Alege serie factura' );
			}
			
		}

		return $serii;

	}

	public function serii_proforme( $force = false ) {
		$serii = $this->options['serii_proforme'];

		if ( empty( $serii ) || $force ) {
			$smartbill_response = $this->smartbill->getDocumentSeries( 'p' );

			if ( 'ok' == $smartbill_response['status'] ) {
				$serii = wp_list_pluck( $smartbill_response['list'], 'name', 'name' );
				$serii = array_merge( array( 'Alege serie proforma' ), $serii );

				$this->options['serii_proforme'] = $serii;
				$this->update();

			}else{
				$serii = array( 'Alege serie proforma' );
			}
			
		}

		return $serii;

	}

	public function depozite( $force = false ){
		$warehouses = $this->options['depozite'];

		if ( empty( $warehouses ) || $force ) {

			$stocks = $this->smartbill->productsStock( array() );

			if ( $stocks ) {
				foreach ( $stocks as $stock ) {
					$warehouses[ $stock['warehouse']['warehouseName'] ] = $stock['warehouse']['warehouseName'];
				}
			}

			if ( ! empty( $warehouses ) ) {
				
				$this->options['depozite'] = array( '' => 'Fara gestiune' ) + $warehouses;
				$this->update();

			}else{
				$warehouses = array( 
					'' => 'Fara gestiune',
				);
			}
			
		}

		return $warehouses;

	}

	public function um( $force = false ){
		$um = $this->options['um'];

		if ( empty( $um ) || $force ) {

			$um = $this->smartbill->getMeasuringUnits();
			if ( 'ok' == $um['status'] ) {
				
				$um_values = array_combine( $um['mu'], $um['mu'] );
				$um = array( 'Alege o unitatea de măsură' ) + $um_values;

				$this->options['um'] = $um;
				$this->update();

			}else{
				$um = array( 'Alege o unitatea de măsură' );
			}
			
		}

		return $um;

	}

	public function taxe( $force = false ){
		$taxes = $this->options['taxe'];

		if ( empty( $taxes ) || $force ) {
			
			$smartbill_taxes = $this->smartbill->getTaxes();

			if ( 'ok' == $smartbill_taxes['status'] ) {

				$smartbill_taxes_values = array(
					'none' => 'Alege o taxă',
				);

				foreach ( $smartbill_taxes['taxes'] as $smartbill_tax_value ) {
					$smartbill_taxes_values[ $smartbill_tax_value['name'] ] = $smartbill_tax_value['name'] . ' - ' . $smartbill_tax_value['percentage'] . '%';
				}

				$this->options['taxe'] = $smartbill_taxes_values;
				$this->update();

				$taxes = $this->options['taxe'];

			}

		}

		return $taxes;

	}


}

