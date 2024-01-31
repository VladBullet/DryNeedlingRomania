<?php

/**
 * 
 */
class Woo_SmartBill_Option_Helper {

	private $options = array();
	private $settings = array();
	
	function __construct() {}

	public static function get_instance() {
		static $inst;

		if ( ! $inst ) {
			$inst = new Woo_SmartBill_Option_Helper();
		}

		return $inst;
	}

	public function get_option( $name = '' ){

		if ( empty( $this->options ) ) {
			$this->options = get_option( 'woo_smartbill', array() );
		}

		if ( '' == $name ) {
			return $this->options;
		}

		if ( isset( $this->options[ $name ] ) ) {
			return $this->options[ $name ];
		}

		return false;

	}

	public function get_setting( $name = '' ){

		if ( empty( $this->settings ) ) {
			$this->settings = get_option( 'woo_smartbill_options', array() );
			$this->settings = wp_parse_args( $this->settings, $this->get_defaults() );
		}

		if ( '' == $name ) {
			return $this->settings;
		}

		if ( isset( $this->settings[ $name ] ) ) {
			return $this->settings[ $name ];
		}

		return false;
		
	}

	/*
	* Defaults pentru setari
	*/
	public function get_defaults( $type = 'settings' ) {

		$setari = apply_filters( 'woo_smartbill_settings_defaults', array(
			'serie-factura'        => '',
			'um'                   => '',
			'moneda'               => 'RON',
			'tva'                  => 'no',
			'transport'            => 0,
			'text-transport'       => 'Transport',
			'salvareprodus'        => 0,
			'salvareclient'        => 0,
			'scadenta'             => 1,
			'nr_zile_scadenta'     => 15,
			'discount'             => 1,
			'fee'                  => 1,
			'sale'                 => 1,
			'stergere-factura'     => 'no',
			'auto-generare'        => array( 'wc-completed' ),
			'stornare'             => array( 'wc-refunded' ),
			'stornare-transport'   => 'no',
			'stornare-fee'         => 'no',
			'custom-status'        => 'no',
			'incasare'             => array(),
			'verificare-incasare'  => 'no',
			'mentiuni'             => '',
			'observatii'           => '',
			'email-types'          => array(),
			'force-email-creation' => 'no',
			'email-text'           => 'Poți descărca factura ta <a href="{url_factura}">aici</a>',
			'email-smartbill'      => 'no',
			'emitent-nume'         => '',
			'emitent-cnp'          => '',
		) );


		switch ( $type ) {
			case 'settings':
				return $setari;
				break;
		}

	}


}