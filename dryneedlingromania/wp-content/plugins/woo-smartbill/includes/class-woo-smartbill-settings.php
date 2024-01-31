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

if ( class_exists( 'Woo_Smartbill_Settings', false ) ) {
	return new Woo_Smartbill_Settings();
}

/**
 * Woo_Smartbill_Settings.
 */
class Woo_Smartbill_Settings extends WC_Settings_Page {

	private $yes_no_values = array(
		0 => 'Nu',
		1 => 'Da'
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'woo-smartbill';
		$this->label = esc_html__( 'Smartbill', 'woo-smartbill' );

		parent::__construct();

		// Sections
		add_filter( 'woocommerce_get_sections_woo-smartbill', array( $this, 'sectiune_setari' ) );

		// Settings
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_autentificare' ), 10, 2 );
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_smartbill_cloud' ), 10, 2 );
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_smartbill' ), 15, 2 );
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_factura' ), 15, 2 );

		// License settings
		add_action( 'woocommerce_admin_field_woo_smartbill_license', array( $this, 'output_license_field' ) );

		// Select SmartBill Cloud
		add_action( 'woocommerce_admin_field_woo_smartbill_cloud', array( $this, 'output_cloud_select' ) );

	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => esc_html__( 'Autentificare', 'woo-smartbill' ),
		);

		/*
		* @hooked Woo_Smartbill_Settings::sectiune_setari - 10
		* @hooked Woo_SmartBill_Gestiune::sectiune_gestiune - 20
		*/
		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	public function sectiune_setari( $sections ){

		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		if ( ! $woo_smartbill_license->is_valid() ) {
			return $sections;
		}

		$sections['setari']   = esc_html__( 'Setari', 'woo-smartbill' );

		return $sections;

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

		do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );

	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {

		$settings = array();
		
		if ( '' == $current_section ) {

			$settings = array(
				array(
					'title' => esc_html__( 'Licență', 'woo-smartbill' ),
					'type'  => 'title',
					'id'    => 'woo_smartbill_license',
				),
				array(
					'id'       => 'woo_smartbill_license',
					'title'    => __( 'Licență', 'woo-smartbill' ),
					'type'     => 'woo_smartbill_license',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'woo_smartbill_license',
				),

			);

		}

		/*
		* @hooked Woo_Smartbill_Settings::setari_autentificare - 10
		* @hooked Woo_Smartbill_Settings::setari_smartbill_cloud - 10
		* @hooked Woo_Smartbill_Settings::setari_smartbill - 15
		* @hooked Woo_Smartbill_Settings::setari_factura - 15
		*/
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
		
	}

	public function setari_autentificare( $settings, $current_section ){

		if ( '' != $current_section ) {
			return $settings;
		}

		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		if ( ! $woo_smartbill_license->is_valid() ) {
			return $settings;
		}

		$settings[] = array(
			'title' => esc_html__( 'Autentificare SmartBill', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_login_start',
		);
		$settings[] = array(
            'name'    => esc_html__( 'Nume utilizator / adresă email', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill[user]',
            'autoload' => false,
        );
        $settings[] = array(
            'name'    => esc_html__( 'Token', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill[token]',
            'autoload' => false,
        );
        $settings[] = array(
            'name'    => esc_html__( 'Cod fiscal', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill[codfiscal]',
            'autoload' => false,
        );

        $settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_login_end',
		);

        return $settings;

	}

	public function setari_smartbill_cloud( $settings, $current_section ){

		if ( 'setari' != $current_section ) {
			return $settings;
		}

		$smartbill = SmartBill_Client::get_instance();
		$smartbill_cloud = Woo_SmartBill_Cloud_Options::instance();

		$settings[] = array(
			'title' => esc_html__( 'SmartBill Cloud', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_cloud_start',
		);

		if ( $smartbill->isValid() ) {

			$woo_smartbill = get_option( 'woo_smartbill', array( 'codfiscal' => '' ) );

			$settings[] = array(
				'id'       => 'woo_smartbill_options[serie-factura]',
				'title'    => __( 'Serie factură', 'woo-smartbill' ),
				'type'     => 'woo_smartbill_cloud',
				'action'   => 'serii-facturi',
				'desc'     => '',
				'options'  => $smartbill_cloud->serii_facturi(),
				'autoload' => false,
			);

			$settings[] = array(
				'id'       => 'woo_smartbill_options[um]',
				'title'    => __( 'Unitate de măsură', 'woo-smartbill' ),
				'type'     => 'woo_smartbill_cloud',
				'action'   => 'um',
				'desc'     => '',
				'options'  => $smartbill_cloud->um(),
				'autoload' => false,
			);

			$settings[] = array(
				'id'       => 'woo_smartbill_options[moneda]',
				'title'    => __( 'Monedă', 'woo-smartbill' ),
				'type'     => 'select',
				'desc'     => '',
				'default'  => get_woocommerce_currency_symbol(),
				'autoload' => false,
				'options'  => array(
					'none' => 'Alege monedă',
					"RON"  => __('RON - Leu', 'woo-smartbill'),
		            "EUR"  => __('EUR - Euro', 'woo-smartbill'),
		            "USD"  => __('USD - Dolar', 'woo-smartbill'),
		            "GBP"  => __('GBP - Lira sterlina', 'woo-smartbill'),
		            "CAD"  => __('CAD - Dolar canadian', 'woo-smartbill'),
		            "AUD"  => __('AUD - Dolar australian', 'woo-smartbill'),
		            "CHF"  => __('CHF - Franc elvetian', 'woo-smartbill'),
		            "TRY"  => __('TRY - Lira turceasca', 'woo-smartbill'),
		            "CZK"  => __('CZK - Coroana ceheasca', 'woo-smartbill'),
		            "DKK"  => __('DKK - Coroana daneza', 'woo-smartbill'),
		            "HUF"  => __('HUF - Forintul maghiar', 'woo-smartbill'),
		            "MDL"  => __('MDL - Leu moldovenesc', 'woo-smartbill'),
		            "SEK"  => __('SEK - Coroana suedeza', 'woo-smartbill'),
		            "BGN"  => __('BGN - Leva bulgareasca', 'woo-smartbill'),
		            "NOK"  => __('NOK - Coroana norvegiana', 'woo-smartbill'),
		            "JPY"  => __('JPY - Yenul japonez', 'woo-smartbill'),
		            "EGP"  => __('EGP - Lira egipteana', 'woo-smartbill'),
		            "PLN"  => __('PLN - Zlotul polonez', 'woo-smartbill'),
		            "RUB"  => __('RUB - Rubla', 'woo-smartbill'),
		            'woo'  => __('Preluata din WooCommerce', 'woo-smartbill'),
				),
			);

			$settings[] = array(
				'id'       => 'woo_smartbill_options[tva]',
				'title'    => 'TVA',
				'type'     => 'checkbox',
				'desc'     => __( 'Plătitor de TVA ?', 'woo-smartbill' ),
				'default'  => ANAF_API::is_tax_payer( $woo_smartbill['codfiscal'] ),
				'autoload'    => false,
			);

			// Check taxes
			$smartbill_taxes = $smartbill_cloud->taxe();

			if ( ! empty( $smartbill_taxes ) ) {

				$all_tax_rates = [];
				$tax_classes = WC_Tax::get_tax_classes(); // Retrieve all tax classes.
				if ( !in_array( '', $tax_classes ) ) { // Make sure "Standard rate" (empty class name) is present.
				    array_unshift( $tax_classes, '' );
				}
				foreach ( $tax_classes as $tax_class ) { // For each tax class, get all rates.
				    $taxes = WC_Tax::get_rates_for_tax_class( $tax_class );
				    $all_tax_rates = array_merge( $all_tax_rates, $taxes );
				}

				foreach ( $all_tax_rates as $tax_rate ) {
					
					$settings[] = array(
						'id'       => 'woo_smartbill_taxes[' . $tax_rate->tax_rate_id . ']',
						'title'    => $tax_rate->tax_rate_name,
						'type'     => 'woo_smartbill_cloud',
						'action'   => 'taxe',
						'desc'     => '',
						'default'  => '',
						'autoload' => false,
						'options'  => $smartbill_taxes,
					);

				}

			}

		}

        $settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_cloud_start',
		);

		return $settings;

	}

	public function setari_smartbill( $settings, $current_section ){

		if ( 'setari' != $current_section ) {
			return $settings;
		}

		$settings[] = array(
			'title' => esc_html__( 'Setări SmartBill', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_start',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[transport]',
			'title'    => __( 'Include transportul în factură?', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[text-transport]',
			'title'    => __( 'Denumirea transport', 'woo-smartbill' ),
			'type'     => 'text',
			'desc'     => __( 'Denumirea serviciul de transport de pe factura.', 'woo-smartbill' ),
			'default'  => 'Transport',
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[salvareprodus]',
			'title'    => __( 'Salvează produsul în SmartBill?', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[salvareclient]',
			'title'    => __( 'Salvează clientul in SmartBill?', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[scadenta]',
			'title'    => __( 'Emite factură cu scadență?', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'default'  => 1,
			'autoload' => false,
		);

		$settings[] = array(
            'name'    => esc_html__( 'Numărul de zile până la scadență', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill[nr_zile_scadenta]',
            'default' => 15,
            'autoload' => false,
        );

        $settings[] = array(
			'id'       => 'woo_smartbill_options[discount]',
			'title'    => __( 'Adaugă cupoanele si reducerile în factură', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => true,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[sale]',
			'title'    => __( 'Adaugă reducerile în factură', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[fee]',
			'title'    => __( 'Adaugă taxele suplimentare în factură', 'woo-smartbill' ),
			'type'     => 'select',
			'desc'     => '',
			'options'  => $this->yes_no_values,
			'autoload' => true,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[stergere-factura]',
			'title'    => 'Anulare',
			'type'     => 'checkbox',
			'desc'     => __( 'Dacă este ultima factură să se șteargă în loc să se anuleze.', 'woo-smartbill' ),
			'default'  => 0,
			'autoload'    => false,
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_start',
		);

		return $settings;

	}

	public function setari_factura( $settings, $current_section ){

		if ( 'setari' != $current_section ) {
			return $settings;
		}

		$settings[] = array(
			'title' => esc_html__( 'Setări factură', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_factura',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[auto-generare]',
			'title'    => __( 'Generare', 'woo-smartbill' ),
			'type'     => 'multiselect',
			'desc'     => '',
			'options'  => wc_get_order_statuses(),
			'class'    => 'wc-enhanced-select',
			'default'  => array( 'wc-completed' ),
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[stornare]',
			'title'    => __( 'Stornare', 'woo-smartbill' ),
			'type'     => 'multiselect',
			'desc'     => '',
			'options'  => wc_get_order_statuses(),
			'class'    => 'wc-enhanced-select',
			'default'  => array( 'wc-refunded' ),
			'autoload' => false,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[stornare-transport]',
			'title'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Stornare transport', 'woo-smartbill' ),
			'autoload' => false,
			'default'  => 0,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[stornare-fee]',
			'title'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Stornare taxe suplimentare', 'woo-smartbill' ),
			'autoload' => false,
			'default'  => 0,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[custom-status]',
			'title'    => 'Status',
			'type'     => 'checkbox',
			'desc'     => 'Adăugare status <strong>Expediat</strong><br><div>Adăugarea unui nou status "Expediat" pentru a organiza mai bine comenzile.</div>',
			'autoload' => false,
			'default'  => 0,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_options[incasare]',
			'title'    => __( 'Încasare', 'woo-smartbill' ),
			'desc'     => 'Marchează factura ca încasată când este plătită prin următoarele metode de plată.',
			'type'     => 'multiselect',
			'options'  => $this->get_payment_methods(),
			'class'    => 'wc-enhanced-select',
			'autoload' => false,
			'default'  => array(),
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[verificare-incasare]',
			'title'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Încasare automată', 'woo-smartbill' )
			              . '<br/><div>'
			              . __( 'Verifică periodic în SmartBill dacă statusul comenzii nu s-a schimbat în încasată. Dacă comanda are alt status vă fi trecută automat în comandă finalizată.', 'woo-smartbill' )
			              . '</div>',
			'autoload' => false,
			'default'  => 0,
		);
		$settings[] = array(
			'title'       => __( 'Mențiuni', 'woo-smartbill' ),
			'id'          => 'woo_smartbill_options[mentiuni]',
			'desc' => 'Poți folosi {order_id} pentru a adaugă numărul comenzii la mențiuni. Ex: comandă online nr. {order_id}',
			'css'         => 'width:400px; height: 75px;',
			'type'        => 'textarea',
			'default'     => '',
			'autoload'    => false,
			'placeholder' => 'comandă online nr. {order_id}',
		);
		$settings[] = array(
			'title'       => __( 'Observații', 'woo-smartbill' ),
			'id'          => 'woo_smartbill_options[observatii]',
			'desc'        => 'Poți folosi {order_id} pentru a adaugă numărul comenzii la observații. Ex: comandă online nr. {order_id}',
			'css'         => 'width:400px; height: 75px;',
			'type'        => 'textarea',
			'default'     => '',
			'autoload'    => false,
			'placeholder' => 'comandă online nr. {order_id}',
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_factura',
		);

		// Setari email
		$settings[] = array(
			'title' => esc_html__( 'Setări Email', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_email',
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[email-types]',
			'title'    => __( 'Email-uri', 'woo-smartbill' ),
			'type'     => 'multiselect',
			'desc'     => '',
			'options'  => $this->get_emails(),
			'class'    => 'wc-enhanced-select',
			'default'  => array(),
			'autoload' => false,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[force-email-creation]',
			'title'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Forțează facturarea.', 'woo-smartbill' )
			              . '<br/><div>'
			              . __( 'Dacă factura nu a fost generată când se trimit emailuri o să forțăm generarea ei.', 'woo-smartbill' )
			              . '</div>',
          	'autoload'    => false,
			'default'  => 1,
		);
		$settings[] = array(
			'title'       => __( 'Text factură', 'woo-smartbill' ),
			'desc'        => __( 'Textul care apare în email pentru a accesa factura', 'woo-smartbill' ) . ' ' . sprintf( __( 'Placeholderele: %s', 'woo-smartbill' ), '{url_factura}' ),
			'id'          => 'woo_smartbill_options[email-text]',
			'css'         => 'width:400px; height: 75px;',
			'type'        => 'textarea',
			'default'     => 'Poți descărca factura ta <a href="{url_factura}">aici</a>',
			'autoload'    => false,
			'desc_tip'    => true,
		);
		$settings[] = array(
			'id'       => 'woo_smartbill_options[email-smartbill]',
			'title'    => 'Email SmartBill',
			'type'     => 'checkbox',
			'desc'     => '<strong>' . __( 'Trimite email din SmartBill cand se emite factura.', 'woo-smartbill' ) . '</strong>'
			              . '<br/><div>'
			              . __( 'Subiectul si mesajul email-ului trimis clientului este cel configurat in SmartBill > Configurare> Email.', 'woo-smartbill' )
			              . '</div>',
          	'autoload'    => false,
			'default'  => 1,
		);
        $settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_email',
		);

		// Setari Emitent
		$settings[] = array(
			'title' => esc_html__( 'Emitent', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_emitent',
		);

		$settings[] = array(
            'name'    => esc_html__( 'Nume emitent', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill_options[emitent-nume]',
            'autoload' => false,
        );
        $settings[] = array(
            'name'    => esc_html__( 'CNP emitent', 'woo-smartbill' ),
            'type'    => 'text',
            'id'      => 'woo_smartbill_options[emitent-cnp]',
            'autoload' => false,
        );

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_emitent',
		);

		return $settings;

	}

	public function output_license_field( $value ){

		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		?>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>">Licenta</label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<div>
				<input autocomplete="off"
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="password"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					value="<?php echo esc_attr( $value['value'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
					/>
					<?php if ( 'valid' == $woo_smartbill_license->get_license_status() ): ?>
						<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=woo-smartbill&woo_smartbill_deazctivate_license' ) ?>" class="button">Deazctiveaza licenta</a>
					<?php endif ?>
				</div>
				<?php if ( 'valid' == $woo_smartbill_license->get_license_status() ): ?>
					<p class="description">Licenta este activa.</p>
				<?php else: ?>
					<p class="description">Licenta este inactiva.</p>
				<?php endif ?>
			</td>
		</tr>

		<?php

	}

	public function output_cloud_select( $value ){
		$option_value = $value['value'];
		$action = isset( $value['action'] ) ? $value['action'] : 'serii-facturi';

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<select
					name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					<?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?>
					>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						?>
						<option value="<?php echo esc_attr( $key ); ?>"
							<?php

							if ( is_array( $option_value ) ) {
								selected( in_array( (string) $key, $option_value, true ), true );
							} else {
								selected( $option_value, (string) $key );
							}

							?>
						><?php echo esc_html( $val ); ?></option>
						<?php
					}
					?>
				</select>
				<a href="#" class="woo_smartbill_refresh_cloud" data-action="<?php echo esc_attr( $action ) ?>"><span class="dashicons dashicons-image-rotate"></span></a>
			</td>
		</tr>
		<?php
	}

	private function get_payment_methods(){

		$gateways = WC()->payment_gateways->payment_gateways();
		$return = array();

		foreach ( $gateways as $key => $gateway ) {
			$return[ $key ] = $gateway->title;
		}

		return $return;

	}

	public function get_emails(){

		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		$emails = array();

		foreach ( $email_templates as $email_key => $email_template ) {
			
			if ( $email_template->is_customer_email() ) {
				$emails[ $email_template->id ] = $email_template->get_title();
			}

		}

		return $emails;

	}

}

return new Woo_Smartbill_Settings();