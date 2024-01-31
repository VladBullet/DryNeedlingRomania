<?php

class Woo_SmartBill_Integrari {

	private $options = array();

	public function __construct() {

		// Sections
		add_filter( 'woocommerce_get_sections_woo-smartbill', array( $this, 'sectiune_integrari' ), 40 );

		// Settings
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_integrari' ), 30, 2 );

	}

	public function sectiune_integrari( $sections ){
		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		if ( ! $woo_smartbill_license->is_valid() ) {
			return $sections;
		}

		$sections['integrari'] = esc_html__( 'Integrari', 'woo-smartbill' );

		return $sections;

	}

	public function setari_integrari( $settings, $current_section ){

		if ( 'integrari' != $current_section ) {
			return $settings;
		}

		$smartbill_cloud = Woo_SmartBill_Cloud_Options::instance();

		$settings[] = array(
			'title' => esc_html__( 'Integrare YITH Bundle', 'woo-smartbill' ),
			'desc'  => 'Integrare cu pluginul <a href="https://wordpress.org/plugins/yith-woocommerce-product-bundles/" target="_blank">YITH WooCommerce Product Bundles</a> pentru a seta cum o sa fie facturate produsele de tip bundle.',
			'type'  => 'title',
			'id'    => 'woo_smartbill_int_bundle',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_integrari_bunlde[bundle]',
			'title'    => __( 'Nu factura produsul de tip bundle', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => 'Da',
			'autoload' => false,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_integrari_bunlde[products]',
			'title'    => __( 'Nu factura produsele din bundle', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => 'Da',
			'autoload' => false,
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_int_bundle',
		);

		return $settings;

	}

}

new Woo_SmartBill_Integrari();