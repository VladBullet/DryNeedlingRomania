<?php

/* Compatibility with Advanced Order Export For WooCommerce ( https://wordpress.org/plugins/woo-order-export-lite/ ) */
add_filter( 'woe_get_order_fields', 'woo_facturare_extra_fields' );
function woo_facturare_extra_fields( $fields ) {

	$fields['woofact_cui'] = array(
		'label' => 'CUI',
		'colname' => 'CUI',
		'checked' => 1
	);

	$fields['woofact_cnp'] = array(
		'label' => 'CNP',
		'colname' => 'CNP',
		'checked' => 0
	);

	$fields['woofact_nrregcom'] = array(
		'label' => 'Nr. Reg. Com',
		'colname' => 'Nr. Reg. Com',
		'checked' => 1
	);

	$fields['woofact_banca'] = array(
		'label' => 'Nume Banca',
		'colname' => 'Nume Banca',
		'checked' => 0
	);

	$fields['woofact_iban'] = array(
		'label' => 'IBAN',
		'colname' => 'IBAN',
		'checked' => 0
	);

	return $fields;
}

add_filter( 'woe_get_order_value_woofact_cui', 'woo_facturare_woofact_cui', 10, 3);
function woo_facturare_woofact_cui( $value,$order, $field ) {
	$options_helper = Facturare_Options_Helper::get_instance();
	return $options_helper->get_cui( $order->get_id() );
}

add_filter( 'woe_get_order_value_woofact_cnp', 'woo_facturare_woofact_cnp', 10, 3);
function woo_facturare_woofact_cnp( $value,$order, $field ) {
	$options_helper = Facturare_Options_Helper::get_instance();
	return $options_helper->get_cnp( $order->get_id() );
}

add_filter( 'woe_get_order_value_woofact_nrregcom', 'woo_facturare_woofact_nrregcom', 10, 3);
function woo_facturare_woofact_nrregcom( $value,$order, $field ) {
	$options_helper = Facturare_Options_Helper::get_instance();
	return $options_helper->get_nr_reg_com( $order->get_id() );
}

add_filter( 'woe_get_order_value_woofact_banca', 'woo_facturare_woofact_banca', 10, 3);
function woo_facturare_woofact_banca( $value,$order, $field ) {
	$options_helper = Facturare_Options_Helper::get_instance();
	return $options_helper->get_nume_banca( $order->get_id() );
}

add_filter( 'woe_get_order_value_woofact_iban', 'woo_facturare_woofact_iban', 10, 3);
function woo_facturare_woofact_iban( $value,$order, $field ) {
	$options_helper = Facturare_Options_Helper::get_instance();
	return $options_helper->get_iban( $order->get_id() );
}
