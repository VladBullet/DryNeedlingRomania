<?php

class Woo_SmartBill_Gestiune {

	private $options = array();

	public function __construct() {

		// Sections
		add_filter( 'woocommerce_get_sections_woo-smartbill', array( $this, 'sectiune_gestiune' ), 20 );

		// Settings
		add_filter( 'woocommerce_get_settings_woo-smartbill', array( $this, 'setari_gestiune' ), 20, 2 );

		// Add warehouse to invoice
		add_filter( 'woo_smartbill_order_product', array( $this, 'add_gestiune' ), 10, 3 );

		// add usestock to invoice
		add_filter( 'woo_smartbill_genereaza_data', array( $this, 'add_usestock' ), 20, 2 );

		// action scheduler
		add_action( 'admin_init', array( $this, 'set_action_scheduler' ) );

		// check stock
		add_action( 'woo_smartbill_verifica_gestiune', array( $this, 'verifica_gestiune' ) );

		// Metabox produs
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'gestiune_produs' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_gestiune' ), 10, 2 );

		// Custom checkbox
		add_action( 'woocommerce_admin_field_woo_smartbill_checkbox', array( $this, 'output_checkbox' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_custom_checkbox' ), 10, 3 );

		// Rest API implementation
		add_action( 'rest_api_init', array( $this, 'rest_api' ) );

		// Regenerate token
		add_action( 'woo_smartbill_refresh_cloud', array( $this, 'regenerate_token' ) );

	}

	private function get_option( $key = '' ){

		$defaults = apply_filters( 'woo_smartbill_gestiune_defaults', array(
			'enable'              => 'no',
			'descarcare-gestiune' => 'no',
			'sincronizare'        => 'no',
			'produs'              => 'no',
			'auto-sincronizare'   => 'no'
		) );

		if ( empty( $this->options ) ) {
			$this->options = get_option( 'woo_smartbill_gestiune', array() );
			$this->options = wp_parse_args( $this->options, $defaults );
		}

		if ( '' != $key ) {
			return isset( $this->options[ $key ] ) ? $this->options[ $key ] : '';
		}

		return $this->options;

	}

	public function sectiune_gestiune( $sections ){
		$woo_smartbill_license = Woo_SmartBill_License::get_instance();

		if ( ! $woo_smartbill_license->is_valid() ) {
			return $sections;
		}

		$sections['gestiune'] = esc_html__( 'Gestiune', 'woo-smartbill' );

		return $sections;

	}

	public function setari_gestiune( $settings, $current_section ){

		if ( 'gestiune' != $current_section ) {
			return $settings;
		}

		$smartbill_cloud = Woo_SmartBill_Cloud_Options::instance();

		$settings[] = array(
			'title' => esc_html__( 'Setări Gestiune', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_section_gestiune',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[enable]',
			'title'    => __( 'Folosesti Gestiune ?', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => 'Da',
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[descarcare-gestiune]',
			'title'    => __( 'Descarcare gestiune manual', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'default'  => 'no',
			'desc'     => 'Da<br>Daca doriti sa faceti vanzarea in lipsa stocului puteti sa folositi aceasta optiune si apoi sa efectuati descarcarea stocului la o data ulterioara din contul SmartBill din sectiunea <strong>Documente fara descarcare gestiune</strong> a meniului <strong>Rapoarte</strong>.',
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[warehouse]',
			'title'    => __( 'Gestiuni', 'woo-smartbill' ),
			'type'     => 'woo_smartbill_cloud',
			'action'   => 'depozite',
			'desc'     => '',
			'options'  => $smartbill_cloud->depozite(),
			'autoload' => false,
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[sincronizare]',
			'title'    => __( 'Sincronizare ?', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'desc'     => 'Da <br><div>Sincronizare o sa se faca prin WooCommerce Action Scheduler 1 data pe zi.</div>',
			'autoload' => false,
			'default'  => 'no',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[auto-sincronizare]',
			'title'    => '',
			'type'     => 'woo_smartbill_checkbox',
			'desc'     => 'Automat',
			'autoload' => false,
			'default'  => 'no',
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_section_gestiune',
		);

		$settings[] = array(
			'title' => esc_html__( 'Setări Produs', 'woo-smartbill' ),
			'type'  => 'title',
			'id'    => 'woo_smartbill_setari_produs',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[produs]',
			'title'    => __( 'Activeaza setari produs ?', 'woo-smartbill' ),
			'type'     => 'checkbox',
			'desc'     => 'Da <br><div>Daca activezi aceasta optiune vei putea sa alegi pentru fiecare produs daca este in gestiu sau daca este un serviciu.</div>',
			'autoload' => false,
			'default'  => 'no',
		);

		$settings[] = array(
			'id'       => 'woo_smartbill_gestiune[produs-default]',
			'title'    => __( 'Gestiune Implicita', 'woo-smartbill' ),
			'type'     => 'select',
			'action'   => 'depozite',
			'desc'     => 'Aceasta setare se va aplica pentru produsele care au setata gestiunea pe Implicit.',
			'options'  => array(
				'gestiune'      => 'Se afla in gestiune',
				'fara-gestiune' => 'Nu se afla in gestiune',
			),
			'autoload' => 'gestiune',
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'woo_smartbill_setari_produs',
		);

		return $settings;

	}

	public function parseaza_gestiunile(){

		$cloud_options = get_option( 'woo_smartbill_cloud_options', array() );
		if ( isset( $cloud_options['depozite'] ) ) {

			if ( isset( $cloud_options['depozite'][''] ) ) {
				unset( $cloud_options['depozite'][''] );
			}

			return $cloud_options['depozite'];
		}
		
		return array();

	}

	public function add_gestiune( $product, $order_item, $stornare ){

		if ( $stornare ) {
			return $product;
		}

		$woo_product = $order_item->get_product();

		if ( 'no' == $this->get_option( 'enable' ) ) {
			return $product;
		}

		$warehouse   = get_post_meta( $woo_product->get_id(), 'woo_smartbill_warehouse', true );
		if ( ! $warehouse || 'implicit' == $warehouse ) {
			$warehouse = $this->get_option( 'warehouse' );
		}

		if ( '' == $warehouse ) {
			return $product;
		}

		if ( 'no' != $this->get_option( 'produs' )  ) {

			$in_gestiune = get_post_meta( $woo_product->get_id(), 'woo_smartbill_gestiune', true );

			if ( ! $in_gestiune ) {
				$in_gestiune = 'implicit';
			}

			if ( 'implicit' == $in_gestiune ) {
				$in_gestiune = $this->get_option( 'produs-default' );
			}

			if ( 'gestiune' == $in_gestiune ) {
				$product['warehouseName'] = $warehouse;
			}else {
				$product['isService'] = 1;
			}
			
		}else{
			$product['warehouseName'] = $warehouse;
		}
		
		return $product;

	}

	public function add_usestock( $data, $order_id ){

		if ( 'no' == $this->get_option( 'enable' ) ) {
			return $data;
		}

		if ( '' == $this->get_option( 'warehouse' ) ) {
			return $data;
		}

		$data['useStock'] = true;
		if ( 'yes' == $this->get_option( 'descarcare-gestiune' ) ) {
			$data['useStock'] = false;
		}
		
		return $data;

	}

	public function set_action_scheduler(){

		$queue = WC()->queue();

		if ( 'no' == $this->get_option( 'enable' ) ) {
			if ( $queue->get_next( 'woo_smartbill_verifica_gestiune' ) ) {
				$queue->cancel_all( 'woo_smartbill_verifica_gestiune' );
			}
			return ;
		}

		if ( 'no' == $this->get_option( 'sincronizare' ) ) {
			if ( $queue->get_next( 'woo_smartbill_verifica_gestiune' ) ) {
				$queue->cancel_all( 'woo_smartbill_verifica_gestiune' );
			}
			return ;
		}

		if ( $queue->get_next( 'woo_smartbill_verifica_gestiune' ) ) {
			return;
		}

		$time = strtotime( "tomorrow 8:00" );
		$queue->schedule_recurring( $time, 86400, 'woo_smartbill_verifica_gestiune' );

	}

	public function verifica_gestiune(){

		$queue = WC()->queue();

		if ( 'no' == $this->get_option( 'enable' ) ) {
			if ( $queue->get_next( 'woo_smartbill_verifica_gestiune' ) ) {
				$queue->cancel_all( 'woo_smartbill_verifica_gestiune' );
			}
			return ;
		}

		if ( 'no' == $this->get_option( 'sincronizare' ) ) {
			if ( $queue->get_next( 'woo_smartbill_verifica_gestiune' ) ) {
				$queue->cancel_all( 'woo_smartbill_verifica_gestiune' );
			}
			return ;
		}

		$warehouse = $this->get_option( 'warehouse' );

		$smartbill = SmartBill_Client::get_instance();
		$stocks = $smartbill->productsStock( array( 'warehouseName' => $warehouse ) );

		if ( ! empty( $stocks ) && isset( $stocks[0]['products'] ) ) {
			$smartbill_products = $stocks[0]['products'];

			foreach ( $smartbill_products as $smartbill_product ) {
				$product = false;
				if ( '' != $smartbill_product['productCode'] ) {
					$product_id = wc_get_product_id_by_sku( $smartbill_product['productCode'] );
					if ( $product_id ) {
						$product = wc_get_product( $product_id );
					}
				}else{
					$product_post = get_page_by_title( $smartbill_product['productName'], OBJECT, 'product' );
					if ( $product_post ) {
						$product = wc_get_product( $product_post->ID );
					}
				}

				if ( $product ) {

					if ( 0 != $smartbill_product['quantity'] ) {
						$product->set_stock_quantity( $smartbill_product['quantity'] );
					}else{
						$product->set_stock_quantity();
						$product->set_stock_status('outofstock');
					}

					$product->save();

				}

			}
		}

	}
 
	public function gestiune_produs() {

		if ( 'yes' != $this->get_option( 'enable' )  ) {
			return;
		}

		$in_gestiune = get_post_meta( get_the_ID(), 'woo_smartbill_gestiune', true );
		$warehouse   = get_post_meta( get_the_ID(), 'woo_smartbill_warehouse', true );
		if ( ! $in_gestiune ) {
			$in_gestiune = 'implicit';
		}

		if ( ! $warehouse ) {
			$warehouse = 'implicit';
		}

		echo '<div class="option_group">';
		echo '<p>Gestiune SmartBill</p>';

		if ( 'yes' == $this->get_option( 'produs' ) ) {
			woocommerce_wp_select( array(
				'id'          => 'woo_smartbill_gestiune',
				'value'       => $in_gestiune,
				'label'       => 'Gestiune',
				'options'     => array(
					'implicit'      => 'Implicit',
					'gestiune'      => 'Se afla in gestiune',
					'fara-gestiune' => 'Nu se afla in gestiune',
				),
			) );
		}

		$depozite = array_merge( array( 'implicit' => 'Implicit' ), $this->parseaza_gestiunile() );
		woocommerce_wp_select( array(
			'id'          => 'woo_smartbill_warehouse',
			'value'       => $warehouse,
			'label'       => 'Depozit',
			'options'     => $depozite,
		) );

		echo '</div>';
	}

	
	public function save_gestiune( $id, $post ){
	 	
	 	// Save tipe of product
		if( isset( $_POST['woo_smartbill_gestiune'] ) && ! empty( $_POST['woo_smartbill_gestiune'] ) ) {
			$value = 'implicit';
			if ( in_array( $_POST['woo_smartbill_gestiune'], array( 'implicit', 'gestiune', 'fara-gestiune', 'serviciu' ) ) ) {
				$value = $_POST['woo_smartbill_gestiune'];
			}
			update_post_meta( $id, 'woo_smartbill_gestiune', $value );
		} else {
			delete_post_meta( $id, 'woo_smartbill_gestiune' );
		}

		// Save warehouse
		if( isset( $_POST['woo_smartbill_warehouse'] ) && ! empty( $_POST['woo_smartbill_warehouse'] ) ) {
			$value = 'implicit';
			$depozite = array_merge( array( 'implicit' => 'Implicit' ), $this->parseaza_gestiunile() );
			if ( in_array( $_POST['woo_smartbill_warehouse'], $depozite ) ) {
				$value = $_POST['woo_smartbill_warehouse'];
			}
			update_post_meta( $id, 'woo_smartbill_warehouse', $value );
		} else {
			delete_post_meta( $id, 'woo_smartbill_warehouse' );
		}
	 
	}


	public function output_checkbox( $value ){
		$option_value = $value['value'];

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"></th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<label for="<?php echo esc_attr( $value['id'] ); ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="checkbox"
						class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
						value="1"
						<?php checked( $option_value, 'yes' ); ?>
					/> <?php echo esc_html( $value['desc'] ); // WPCS: XSS ok. ?>
				</label>
				<div>
					<p>Va trebui sa te duci in <strong>SmartBill Cloud > Contul Meu > Integrari > Sincronizare stocuri</strong> pentru a addauga URL-ul si Tokenul de mai jos.</p>
					<p><strong>URL</strong>: <span><input onClick="this.select();" type="text" readonly="" value="<?php echo site_url() ?>/wp-json/woo-smartbill/v1/stock"></span></p>
					<p><strong>Token</strong>: <span><input onClick="this.select();" type="text" readonly="" value="<?php echo $this->get_token() ?>"></span><a href="#" class="woo_smartbill_refresh_cloud" data-action="token-gestiune"><span class="dashicons dashicons-image-rotate"></span></a></p>
				</div>
				
			</td>
		</tr>
		<?php
	}

	public function sanitize_custom_checkbox( $value, $option, $raw_value ){

		if ( 'woo_smartbill_checkbox' == $option['type'] ) {
			$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
		}

		return $value;
	}

	public function rest_api(){

		if ( 'yes' != $this->get_option( 'enable' )  ) {
			return;
		}

		if ( 'yes' != $this->get_option( 'auto-sincronizare' ) ) {
			return;
		}

		register_rest_route( 'woo-smartbill/v1', '/stock', array(
		    'methods' => 'POST',
		    'callback' => array( $this, 'handle_rest_api' ),
		    'permission_callback' => '__return_true'
		) );
	}

	public function handle_rest_api( $request ){

		$authorization = $request->get_header('authorization');

		if ( ! $authorization ) {
			return new WP_REST_Response(__("Conexiunea nu s-a putut realiza. \nAsigura-te ca token-ul este introdus corect si ca serverul permite receptionarea de autentificari prin headers.\n Pentru mai multe detalii, consulta ghidul. ", 'smartbill-woocommerce'), 403);
		}

		$smart_token = substr( $authorization, strlen('Bearer ') );
		$token = $this->get_token();

		if ( $smart_token != $token ) {
			return new WP_REST_Response(__("Conexiunea nu s-a putut realiza. \nAsigura-te ca token-ul este introdus corect si ca serverul permite receptionarea de autentificari prin headers.\n Pentru mai multe detalii, consulta ghidul. ", 'smartbill-woocommerce'), 403);
		}

		$body = json_decode( $request->get_body(), 1 );

		$products = array();
		if ( isset( $body['products'] ) && is_array( $body['products'] ) ){
	        $products = $body['products'];
	    }

	    $warehouse = $this->get_option( 'warehouse' );
	    foreach ( $products as $product ) {
	    	$product_id = 0;

	    	if( isset( $product['productCode'] ) && trim( $product['productCode'] ) != "" ){
	    		$product_id = wc_get_product_id_by_sku( $product['productCode'] );
	    	}else{
	    		$product_obj = get_page_by_title( $product['productName'], OBJECT, 'product' );
	    		if ( $product_obj ) {
	    			$product_id = $product_obj->ID;
	    		}
	    	}

	    	if ( 0 == $product_id ) {
	    		continue;
	    	}

	    	$product_warehouse   = get_post_meta( $product_id, 'woo_smartbill_warehouse', true );
			if ( $product_warehouse && 'implicit' != $product_warehouse ) {
				$warehouse = $product_warehouse;
			}

			if ( strtolower( $product['warehouse'] ) == strtolower( $warehouse ) ) {
				update_post_meta( $product_id, '_stock', $product['quantity'] );
			}

	    }


	}

	public function regenerate_token(){

		$token = wp_hash( time() );
		update_option( 'woo_smartbill_token_gestiune', $token, false );
		return $token;

	}

	public function get_token(){

		$token = get_option( 'woo_smartbill_token_gestiune', false );
		if ( ! $token ) {
			return $this->regenerate_token();
		}

		return $token;

	}

}

new Woo_SmartBill_Gestiune();