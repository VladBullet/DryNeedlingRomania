<?php

class Woo_Facturare_Public {

	private $defaults;

	public function __construct() {
		$this->defaults = array(
			'facturare_pers_fiz_label'                  => esc_html__( 'Persoana Fizica', 'woo-facturare' ),
			'facturare_pers_fiz_cnp_label'              => esc_html__( 'CNP', 'woo-facturare' ),
			'facturare_pers_fiz_cnp_placeholder'        => esc_html__( 'Introduceti Codul numeric personal', 'woo-facturare' ),
			'facturare_pers_fiz_cnp_vizibility'         => 'no',
			'facturare_pers_fiz_cnp_required'           => 'no',
			'facturare_pers_fiz_cnp_error'              => esc_html__( 'Datorita legislatiei in vigoare trebuie sa completati campul CNP', 'woo-facturare' ),
			'facturare_pers_jur_label'                  => esc_html__( 'Persoana Juridica', 'woo-facturare' ),
			'facturare_pers_jur_company_label'          => esc_html__( 'Nume Firma', 'woo-facturare' ),
			'facturare_pers_jur_company_placeholder'    => esc_html__( 'Introduceti numele firmei dumneavoastra', 'woo-facturare' ),
			'facturare_pers_jur_company_vizibility'     => 'yes',
			'facturare_pers_jur_company_required'       => 'yes',
			'facturare_pers_jur_company_error'          => esc_html__( 'Pentru a va putea emite factura avem nevoie de numele firmei dumneavoastra', 'woo-facturare' ),
			'facturare_pers_jur_cui_label'              => esc_html__( 'CUI', 'woo-facturare' ),
			'facturare_pers_jur_cui_placeholder'        => esc_html__( 'Introduceti Codul Unic de Inregistrare', 'woo-facturare' ),
			'facturare_pers_jur_cui_vizibility'         => 'yes',
			'facturare_pers_jur_cui_required'           => 'yes',
			'facturare_pers_jur_cui_error'              => esc_html__( 'Pentru a va putea emite factura avem nevoie de CUI-ul firmei dumneavoastra', 'woo-facturare' ),
			'facturare_pers_jur_nr_reg_com_label'       => esc_html__( 'Nr. Reg. Com', 'woo-facturare' ),
			'facturare_pers_jur_nr_reg_com_placeholder' => 'J20/20/20.02.2020',
			'facturare_pers_jur_nr_reg_com_vizibility'  => 'yes',
			'facturare_pers_jur_nr_reg_com_required'    => 'yes',
			'facturare_pers_jur_nr_reg_com_error'       => esc_html__( 'Pentru a va putea emite factura avem nevoie de numarul de ordine in registrul comertului', 'woo-facturare' ),
			'facturare_pers_jur_nume_banca_label'       => esc_html__( 'Nume Banca', 'woo-facturare' ),
			'facturare_pers_jur_nume_banca_placeholder' => esc_html__( 'Numele bancii cu care lucrati', 'woo-facturare' ),
			'facturare_pers_jur_nume_banca_vizibility'  => 'no',
			'facturare_pers_jur_nume_banca_required'    => 'no',
			'facturare_pers_jur_nume_banca_error'       => esc_html__( 'Pentru a va putea emite factura avem nevoie de numele bancii cu care lucrati', 'woo-facturare' ),
			'facturare_pers_jur_iban_label'             => esc_html__( 'IBAN', 'woo-facturare' ),
			'facturare_pers_jur_iban_placeholder'       => esc_html__( 'Numarul contului IBAN', 'woo-facturare' ),
			'facturare_pers_jur_iban_vizibility'        => 'no',
			'facturare_pers_jur_iban_required'          => 'no',
			'facturare_pers_jur_iban_error'             => esc_html__( 'Pentru a va putea emite factura avem nevoie de numarul contului', 'woo-facturare' ),
			'facturare_output'                          => 'select',
			'facturare_default'                         => 'pers-fiz',
			'facturare_label'                           => esc_html__( 'Tip Facturare', 'woo-facturare' ),
		);
	}

	public function override_checkout_fields( $fields ) {

		$options = get_option( 'av_facturare', array() );
		$options = wp_parse_args( $options, $this->defaults );
		$customer_id = get_current_user_id();
		$customer_facturare = get_user_meta( $customer_id, 'tip_facturare', true );
		$facturare = $customer_facturare ? $customer_facturare : $options['facturare_default'];

		// Add Facturare Field
		$ordered_fields['tip_facturare'] = array(
			'type'     => $options['facturare_output'],
			'label'    => $options['facturare_label'],
			'required' => true,
			'class'    => array( 'form-row-wide' ),
			'options'  => array(
				'pers-fiz' => $options['facturare_pers_fiz_label'],
				'pers-jur' => $options['facturare_pers_jur_label'],
			),
			'default'  => $options['facturare_default'],
			'priority' => 0,
			'clear'    => true,
		);

		if ( 'radio' == $options['facturare_output'] ) {
			$ordered_fields['tip_facturare']['class'][] = 'av_tip_facturare_radio';
		}

		// Extra Fields
		$company = $fields['billing_company'];
		unset( $fields['billing_company'] );
		$extra_fields = array();

		// CNP Field
		if ( 'yes' == $options['facturare_pers_fiz_cnp_vizibility'] ) {
			$extra_fields['cnp'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_fiz_cnp_label'],
				'placeholder' => $options['facturare_pers_fiz_cnp_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_fiz' ),
				'needed_req'  => $options['facturare_pers_fiz_cnp_required'],
			);

		}

		// Company Field
		if ( 'yes' == $options['facturare_pers_jur_company_vizibility'] ) {
			$extra_fields['billing_company'] = $company;

			$extra_fields['billing_company']['label']       = $options['facturare_pers_jur_company_label'];
			$extra_fields['billing_company']['placeholder'] = $options['facturare_pers_jur_company_placeholder'];
			$extra_fields['billing_company']['needed_req']  = $options['facturare_pers_jur_company_required'];
			$extra_fields['billing_company']['class'][]     = 'show_if_pers_jur';
			$extra_fields['billing_company']['required']    = false;

		}

		// CUI Field
		if ( 'yes' == $options['facturare_pers_jur_cui_vizibility'] ) {
			$extra_fields['cui'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_cui_label'],
				'placeholder' => $options['facturare_pers_jur_cui_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'needed_req'  => $options['facturare_pers_jur_cui_required'],
			);

		}

		// Nr. Reg. Com Field
		if ( 'yes' == $options['facturare_pers_jur_nr_reg_com_vizibility'] ) {
			$extra_fields['nr_reg_com'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_nr_reg_com_label'],
				'placeholder' => $options['facturare_pers_jur_nr_reg_com_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'needed_req'  => $options['facturare_pers_jur_nr_reg_com_required'],
			);

		}

		// Nume Banca Field
		if ( 'yes' == $options['facturare_pers_jur_nume_banca_vizibility'] ) {
			$extra_fields['nume_banca'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_nume_banca_label'],
				'placeholder' => $options['facturare_pers_jur_nume_banca_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'needed_req'  => $options['facturare_pers_jur_nume_banca_required'],
			);

		}

		// IBAN Field
		if ( 'yes' == $options['facturare_pers_jur_iban_vizibility'] ) {
			$extra_fields['iban'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_iban_label'],
				'placeholder' => $options['facturare_pers_jur_iban_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'needed_req'  => $options['facturare_pers_jur_iban_required'],
			);

		}

		foreach ( $fields as $key => $field ) {
			
			$ordered_fields[ $key ] = $field;

			if ( 'billing_last_name' == $key ) {
				$ordered_fields = array_merge( $ordered_fields, $extra_fields );
			}

		}

		$fields = $ordered_fields;

		return $fields;
	}

	public function fix_checkout_args( $args, $key, $value ){

		$our_fields = array( 'cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company' );
		$options_keys = array(
			'cnp' 				=> 'facturare_pers_fiz_cnp_required',
			'iban' 				=> 'facturare_pers_jur_iban_required',
			'nume_banca' 		=> 'facturare_pers_jur_nume_banca_required',
			'nr_reg_com' 		=> 'facturare_pers_jur_nr_reg_com_required',
			'cui' 				=> 'facturare_pers_jur_cui_required',
			'billing_company'	=> 'facturare_pers_jur_company_required'
		);

		if ( in_array( $key, $our_fields ) ) {

			$options = get_option( 'av_facturare', array() );
			$options = wp_parse_args( $options, $this->defaults );
			$customer_id = get_current_user_id();
			$customer_facturare = get_user_meta( $customer_id, 'tip_facturare', true );
			$facturare = $customer_facturare ? $customer_facturare : $options['facturare_default'];

			if ( 'cnp' == $key && 'pers-fiz' != $facturare ) {
				$args['class'][] = 'av-hide';
			}elseif ( 'cnp' != $key && 'pers-jur' != $facturare ) {
				$args['class'][] = 'av-hide';
			}

			$args['needed_req'] =  $options[ $options_keys[ $key ] ];

		}

		return $args;

	}

	public function all_fields_are_optional( $fields ){
		$our_fields = array( 'cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company' );
		foreach ( $our_fields as $our_field ) {
			if ( isset( $fields['billing'][ $our_field ] ) ) {
				$fields['billing'][ $our_field ]['required'] = false;
			}
		}
		// print_r( $fields );
		return $fields;
	}

	public function override_field_html( $field, $key, $args ) {

		$our_fields = array( 'cnp', 'iban', 'nume_banca', 'nr_reg_com', 'cui', 'billing_company' );

		if ( in_array( $key, $our_fields ) ) {

			$optional_label = '<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
			$required_label = '<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
			if ( 'yes' == $args['needed_req'] ) {
				$field = str_replace( $optional_label, $required_label, $field );
			}

		}

		return $field;

	}

	public function hide_fields() {

		if ( ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
			echo '<style>.woocommerce .woocommerce-billing-fields .av-hide,.woocommerce .woocommerce-address-fields .av-hide,.wcf-embed-checkout-form .woocommerce form .form-row.av-hide{display:none}.woocommerce .av_tip_facturare_radio span.woocommerce-input-wrapper {display: flex;align-items: center;}.woocommerce .av_tip_facturare_radio span.woocommerce-input-wrapper label + input[type="radio"] {margin-left: 10px;}.woocommerce .av_tip_facturare_radio span.woocommerce-input-wrapper label{line-height:1;}</style>';
		}

	}

	public function add_js_to_footer() {

		if ( ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
			echo '<script>!function(i){"use strict";i(document).ready(function(){i("#tip_facturare, #tip_facturare_field .input-radio").change(function(){"pers-jur"==i(this).val()?(i(".show_if_pers_jur").show(),i(".show_if_pers_fiz").hide()):(i(".show_if_pers_jur").hide(),i(".show_if_pers_fiz").show())})})}(jQuery);</script>';
			echo '<script>(function($){$(document).ready(function(){if($().selectWoo){if($("select#tip_facturare").length>0){$("select#tip_facturare").selectWoo({minimumResultsForSearch:-1,width: "100%"});}}});})(jQuery);</script>';
		}

	}

	public function validate_checkout() {

		$options = get_option( 'av_facturare', array() );
		$options = wp_parse_args( $options, $this->defaults );

		if ( 'pers-fiz' == $_POST['tip_facturare'] ) {
			
			// validate CNP
			if ( 'yes' == $options['facturare_pers_fiz_cnp_required'] ) {
				if ( ! av_validare_cnp( $_POST['cnp'] ) ) {
					wc_add_notice( $options['facturare_pers_fiz_cnp_error'], 'error' );
				}
			}

		}

		if ( 'pers-jur' == $_POST['tip_facturare'] ) {
			
			// validate Nume Firma
			if ( 'yes' == $options['facturare_pers_jur_company_required'] && '' == $_POST['billing_company'] && '' != $options['facturare_pers_jur_company_error'] ) {
				wc_add_notice( $options['facturare_pers_jur_company_error'], 'error' );
			}

			// validate CUI
			if ( 'yes' == $options['facturare_pers_jur_cui_required'] ) {
				if ( ! av_validare_cif( $_POST['cui'] ) ) {
					wc_add_notice( $options['facturare_pers_jur_cui_error'], 'error' );
				}
			}

			// validate Nr. Reg. Com.
			if ( 'yes' == $options['facturare_pers_jur_nr_reg_com_required'] && '' == $_POST['nr_reg_com'] && '' != $options['facturare_pers_jur_nr_reg_com_error'] ) {
				wc_add_notice( $options['facturare_pers_jur_nr_reg_com_error'], 'error' );
			}

			// validate Nume Banca
			if ( 'yes' == $options['facturare_pers_jur_nume_banca_required'] && '' == $_POST['nume_banca'] && '' != $options['facturare_pers_jur_nume_banca_error'] ) {
				wc_add_notice( $options['facturare_pers_jur_nume_banca_error'], 'error' );
			}

			// validate Nume Banca
			if ( 'yes' == $options['facturare_pers_jur_iban_required'] ) {
				if ( ! av_validare_iban( $_POST['iban'] ) ) {
					wc_add_notice( $options['facturare_pers_jur_iban_error'], 'error' );
				}
			}

		}

	}

	// Add fields to customer profile
	public function add_profile_fields( $fields, $load_address ) {

		if ( 'billing' != $load_address ) {
			return $fields;
		}

		$options = get_option( 'av_facturare', array() );
		$options = wp_parse_args( $options, $this->defaults );
		$customer_id = get_current_user_id();

		// Add Facturare Field
		$ordered_fields['tip_facturare'] = array(
			'type'     => $options['facturare_output'],
			'label'    => $options['facturare_label'],
			'required' => true,
			'class'    => array( 'form-row-wide' ),
			'options'  => array(
				'pers-fiz' => $options['facturare_pers_fiz_label'],
				'pers-jur' => $options['facturare_pers_jur_label'],
			),
			'default'  => $options['facturare_default'],
			'priority' => 0,
			'clear'    => true,
			'value'    => get_user_meta( $customer_id, 'tip_facturare', true ),
		);

		if ( 'radio' == $options['facturare_output'] ) {
			$ordered_fields['tip_facturare']['class'][] = 'av_tip_facturare_radio';
		}

		// Extra Fields
		$company = $fields['billing_company'];
		unset( $fields['billing_company'] );
		$extra_fields = array();

		// CNP Field
		if ( 'yes' == $options['facturare_pers_fiz_cnp_vizibility'] ) {
			$extra_fields['cnp'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_fiz_cnp_label'],
				'placeholder' => $options['facturare_pers_fiz_cnp_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_fiz' ),
				'value'       => get_user_meta( $customer_id, 'cnp', true ),
			);
		}

		// Company Field
		if ( 'yes' == $options['facturare_pers_jur_company_vizibility'] ) {
			$extra_fields['billing_company'] = $company;

			$extra_fields['billing_company']['label']       = $options['facturare_pers_jur_company_label'];
			$extra_fields['billing_company']['placeholder'] = $options['facturare_pers_jur_company_placeholder'];
			$extra_fields['billing_company']['needed_req']  = $options['facturare_pers_jur_company_required'];
			$extra_fields['billing_company']['class'][]     = 'show_if_pers_jur';
			$extra_fields['billing_company']['required']    = false;

		}

		// CUI Field
		if ( 'yes' == $options['facturare_pers_jur_cui_vizibility'] ) {
			$extra_fields['cui'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_cui_label'],
				'placeholder' => $options['facturare_pers_jur_cui_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'value'       => get_user_meta( $customer_id, 'cui', true ),
			);

		}

		// Nr. Reg. Com Field
		if ( 'yes' == $options['facturare_pers_jur_nr_reg_com_vizibility'] ) {
			$extra_fields['nr_reg_com'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_nr_reg_com_label'],
				'placeholder' => $options['facturare_pers_jur_nr_reg_com_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'value'       => get_user_meta( $customer_id, 'nr_reg_com', true ),
			);

		}

		// Nume Banca Field
		if ( 'yes' == $options['facturare_pers_jur_nume_banca_vizibility'] ) {
			$extra_fields['nume_banca'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_nume_banca_label'],
				'placeholder' => $options['facturare_pers_jur_nume_banca_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'value'       => get_user_meta( $customer_id, 'nume_banca', true ),
			);

		}

		// IBAN Field
		if ( 'yes' == $options['facturare_pers_jur_iban_vizibility'] ) {
			$extra_fields['iban'] = array(
				'type'        => 'text',
				'label'       => $options['facturare_pers_jur_iban_label'],
				'placeholder' => $options['facturare_pers_jur_iban_placeholder'],
				'priority'    => 25,
				'clear'       => true,
				'class'       => array( 'form-row-wide', 'show_if_pers_jur' ),
				'value'       => get_user_meta( $customer_id, 'iban', true ),
			);

		}

		foreach ( $fields as $key => $field ) {
			
			$ordered_fields[ $key ] = $field;

			if ( 'billing_last_name' == $key ) {
				$ordered_fields = array_merge( $ordered_fields, $extra_fields );
			}

		}

		return $ordered_fields;
	}

	public function save_profile_fields( $user_id, $load_address ) {

		if ( isset( $_POST['tip_facturare'] ) ) {
			update_user_meta( $user_id, 'tip_facturare', sanitize_text_field( $_POST['tip_facturare'] ) );
		}

		if ( isset( $_POST['cnp'] ) ) {
			update_user_meta( $user_id, 'cnp', sanitize_text_field( $_POST['cnp'] ) );
		}

		if ( isset( $_POST['cui'] ) ) {
			update_user_meta( $user_id, 'cui', sanitize_text_field( $_POST['cui'] ) );
		}

		if ( isset( $_POST['nume_banca'] ) ) {
			update_user_meta( $user_id, 'nume_banca', sanitize_text_field( $_POST['nume_banca'] ) );
		}

		if ( isset( $_POST['iban'] ) ) {
			update_user_meta( $user_id, 'iban', sanitize_text_field( $_POST['iban'] ) );
		}

	}

}
