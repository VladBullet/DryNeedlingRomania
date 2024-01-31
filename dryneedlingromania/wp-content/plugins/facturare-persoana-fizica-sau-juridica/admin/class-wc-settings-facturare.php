<?php
/**
 * WooCommerce Facturare Settings
 *
 * @package WooCommerce/Admin
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Settings_Facturare', false ) ) {
	return new WC_Settings_Facturare();
}

/**
 * WC_Settings_Facturare.
 */
class WC_Settings_Facturare extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'facturare';
		$this->label = esc_html__( 'Facturare', 'woo-facturare' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''         => esc_html__( 'General', 'woo-facturare' ),
			'pers-fiz' => esc_html__( 'Persoana Fizica', 'woo-facturare' ),
			'pers-jur' => esc_html__( 'Persoana Juridica', 'woo-facturare' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );

	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {

		if ( 'pers-fiz' === $current_section ) {
			$settings = array(
				array(
					'title' => esc_html__( 'Setari Persoane Fizice', 'woo-facturare' ),
					'type'  => 'title',
					'id'    => 'facturare_pers_fiz_start',
				),
				array(
	                'name'    => esc_html__( 'Label Persoana Fizica', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Persoana Fizica', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_fiz_label]'
	            ),
	            array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_fiz_end',
				),

				array(
					'title' => esc_html__( 'Camp CNP', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_fiz_cnp_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'CNP', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_fiz_cnp_label]'
	            ),
	            array(
	                'name'    => esc_html__( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Introduceti Codul numeric personal', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_fiz_cnp_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_fiz_cnp_vizibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>CNP</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_fiz_cnp_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Datorita legislatiei in vigoare trebuie sa completati campul CNP', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_fiz_cnp_error]'
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_fiz_cnp_end',
				),
			);

		} elseif ( 'pers-jur' === $current_section ) {
			$settings = array(
				array(
					'title' => esc_html__( 'Setari Persoane Juridice', 'woo-facturare' ),
					'type'  => 'title',
					'id'    => 'facturare_pers_jur_start',
				),
				array(
	                'name'    => esc_html__( 'Label Persoana Juridica', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Persoana Juridica', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_label]'
	            ),
		        array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_end',
				),

		        // Nume Firma
		        array(
					'title' => esc_html__( 'Camp Nume Firma', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_jur_company_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Nume Firma', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_company_label]'
	            ),
	            array(
	                'name'    => esc_html__( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Introduceti numele firmei dumneavoastra', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_company_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_company_vizibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>Nume Firma</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_company_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Pentru a va putea emite factura avem nevoie de numele firmei dumneavoastra', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_company_error]'
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_company_end',
				),

		        // CUI
		        array(
					'title' => esc_html__( 'Camp CUI', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_jur_cui_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'CUI', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_cui_label]'
	            ),
	            array(
	                'name'    => __( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Introduceti Codul Unic de Inregistrare', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_cui_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_cui_vizibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>CUI</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_cui_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Pentru a va putea emite factura avem nevoie de CUI-ul firmei dumneavoastra', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_cui_error]'
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_cui_end',
				),

				// Nr. Reg. Com.
				array(
					'title' => esc_html__( 'Camp Nr. Reg. Com.', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_jur_nr_reg_com_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Nr. Reg. Com', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_nr_reg_com_label]'
	            ),
	            array(
	                'name'    => esc_html__( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => 'J20/20/20.02.2020',
	                'id'      => 'av_facturare[facturare_pers_jur_nr_reg_com_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_nr_reg_com_vizibility]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>Nr. Reg. Com</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_nr_reg_com_required]',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Pentru a va putea emite factura avem nevoie de numarul de ordine in registrul comertului', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_nr_reg_com_error]'
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_nr_reg_com_end',
				),

				// Nume Banca
				array(
					'title' => esc_html__( 'Camp Nume Banca', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_jur_nume_banca_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Nume Banca', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_nume_banca_label]'
	            ),
	            array(
	                'name'    => esc_html__( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Numele bancii cu care lucrati', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_nume_banca_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_nume_banca_vizibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>Nume Banca</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_nume_banca_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Pentru a va putea emite factura avem nevoie de numele bancii cu care lucrati', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_nume_banca_error]',
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_nume_banca_end',
				),

				// IBAN
				array(
					'title' => esc_html__( 'Camp IBAN', 'woo-facturare' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'facturare_pers_jur_iban_start',
				),
				array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'IBAN', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_iban_label]'
	            ),
	            array(
	                'name'    => esc_html__( 'Placeholder', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Numarul contului IBAN', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_iban_placeholder]'
	            ),
				array(
					'title'   => esc_html__( 'Vizibilitate', 'woo-facturare' ),
					'desc'    => esc_html__( 'Arata acest camp pe pagina de checkout', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_iban_vizibility]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => esc_html__( 'Obligatoriu', 'woo-facturare' ),
					'desc'    => __( 'Da, campul <strong>IBAN</strong> este Obligatoriu', 'woo-facturare' ),
					'id'      => 'av_facturare[facturare_pers_jur_iban_required]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
	                'name'    => esc_html__( 'Mesaj Eroare', 'woo-facturare' ),
	                'type'    => 'textarea',
	                'default' => esc_html__( 'Pentru a va putea emite factura avem nevoie de numarul contului', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_pers_jur_iban_error]'
	            ),
				array(
					'type' => 'sectionend',
					'id'   => 'facturare_pers_jur_iban_end',
				),

			);

		} else {
			$settings = array(
				array(
					'title' => esc_html__( 'Setari Generale', 'woo-facturare' ),
					'type'  => 'title',
					'id'    => 'facturare_general_start',
				),
				array(
	                'name' => esc_html__( 'Tip', 'woo-facturare' ),
	                'type' => 'select',
	                'options' => array(
	                	'radio'  => esc_html__( 'Butoane radio', 'woo-facturare' ),
	                	'select' => esc_html__( 'Select', 'woo-facturare' ),
	                ),
	                'default' => 'select',
	                'desc' => __( '<p>Cum va fi afisata optiunea de a alaege intre persoana fizica sau juridica</p>', 'woo-facturare' ),
	                'id'   => 'av_facturare[facturare_output]'
	            ),
	            array(
	                'name' => esc_html__( 'Optiune implicita', 'woo-facturare' ),
	                'type' => 'select',
	                'options' => array(
	                	'pers-fiz' => esc_html__( 'Persoana Fizica', 'woo-facturare' ),
	                	'pers-jur' => esc_html__( 'Persoana Juridica', 'woo-facturare' ),
	                ),
	                'desc' => __( '<p>Optiunea care va fi selectata implicit pe pagina de checkout</p>', 'woo-facturare' ),
	                'id'   => 'av_facturare[facturare_default]'
	            ),
	            array(
	                'name'    => esc_html__( 'Label', 'woo-facturare' ),
	                'type'    => 'text',
	                'default' => esc_html__( 'Tip Facturare', 'woo-facturare' ),
	                'id'      => 'av_facturare[facturare_label]'
	            ),
	            array(
					'title'   => esc_html__( 'Reclame', 'woo-facturare' ),
					'desc'    => esc_html__( 'Ascunde reclamele.', 'woo-facturare' ),
					'id'      => 'av_facturare[reclame]',
					'default' => 'no',
					'type'    => 'checkbox',
				),
	            array(
					'type' => 'sectionend',
					'id'   => 'facturare_general_end',
				),
			);

		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}
}

return new WC_Settings_Facturare();