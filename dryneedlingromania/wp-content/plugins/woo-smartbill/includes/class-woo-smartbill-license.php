<?php


class Woo_SmartBill_License {

	private $vars = array(

		'store_url' => 'https://avianstudio.com',
		'author'    => 'George Ciobanu',
        
		// The URL to renew or purchase a license
	    'purchase_url' => 'https://avianstudio.com/woo-smartbill',

	    // The URL of your contact page
	    'contact_url' => 'https://avianstudio.com/contact',

	    // This should match the download name exactly
	    'item_name' => 'WooCommerce SmartBill',
	    'item_id'   => 73,

	    // The option names to store the license key and activation status
	    'license_key'    => 'woo_smartbill_license',
	    'license_status' => 'woo_smartbill_license_status',
	);

	private $license_key = '';
	private $license_status = '';
	
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'sl_updater'), 0 );
		add_action( 'woocommerce_update_options_woo-smartbill_', array( $this, 'verify_license' ) );

		add_action( 'init', array( $this, 'deactivate_license' ) );

	}

	public static function get_instance() {
		static $inst;

		if ( ! $inst ) {
			$inst = new Woo_SmartBill_License();
		}

		return $inst;
	}

	public function get_var( $var ) {
        if ( isset( $this->vars[ $var ] ) )
            return $this->vars[ $var ];
        return false;
    }

    public function get_license_key(){
    	if ( '' == $this->license_key ) {
    		$this->license_key = trim( get_option( $this->get_var( 'license_key' ) ) );
    	}

    	return $this->license_key;

    }

    public function get_license_status(){
    	if ( '' == $this->license_status ) {
    		$this->license_status = trim( get_option( $this->get_var( 'license_status' ) ) );
    	}

    	return $this->license_status;

    }

    public function init() {
        if ( 'valid' != get_option( $this->get_var( 'license_status' ) ) ) {
            if ( ( ! isset( $_GET['page'] ) or $this->get_var( 'admin_page_slug' ) != $_GET['page'] ) ) {
                add_action( 'admin_notices', function() {
                    echo '<div class="error"><p>' .
                         sprintf( __( 'The %s license needs to be activated. %sActivate Now%s', 'woo-smartbill' ), $this->get_var( 'plugin_title' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=woo-smartbill' ) . '">', '</a>' ) .
                         '</p></div>';
                } );
            } else {
                add_action( 'admin_notices', function() {
                    echo '<div class="notice"><p>' .
                         sprintf( __( 'License key invalid. Need a license? %sPurchase Now%s', 'woo-smartbill' ), '<a target="_blank" href="' . $this->get_var( 'purchase_url' ) . '">', '</a>' ) .
                         '</p></div>';
                } );
            }
        }

    }

    public function sl_updater() {
        // retrieve our license key from the DB
        $license_key = $this->get_license_key();
        $license_status = get_option( $this->get_var( 'license_status' ) );

        // setup the updater
        new Woo_Smartbill_EDD_License( $this->get_var( 'store_url' ), WOO_SMARTBILL_FILE, array(
                'version'   => WOO_SMARTBILL_VERSION,
                'license'   => $license_key,
                'item_name' => $this->get_var( 'item_name' ),
                'item_id'   => $this->get_var( 'item_id' ),
                'author'    => $this->get_var( 'author' )
            ),
	        array(
		        'license_status' => $license_status,
		        'admin_page_url' => admin_url( 'admin.php?page=' . $this->get_var( 'admin_page_slug' ) ),
		        'purchase_url' => $this->get_var( 'purchase_url' ),
		        'plugin_title' => $this->get_var( 'plugin_title' )
	        )
        );
    }

	public function activate_license() {

        // retrieve the license from the database
        $license = trim( $this->get_license_key() );

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> $license,
            'item_name' => urlencode( $this->get_var( 'item_name' ) ), // the name of our product in EDD
            'item_id'   => urlencode( $this->get_var( 'item_id' ) ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( $this->get_var( 'store_url' ), array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) ) {
            add_settings_error(
	            $this->get_var( 'option_group' ),
	            'activate',
	            __( 'There was an error activating the license, please verify your license is correct and try again or contact support.', 'woo-smartbill' )
            );
            return false;
        }

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "valid" or "invalid"
        update_option( $this->get_var( 'license_status' ), $license_data->license );

    }

    public function deactivate_license() {
        // listen for our activate button to be clicked
        if ( isset( $_GET['woo_smartbill_deazctivate_license'] ) && current_user_can( 'manage_options' ) ) {

            // retrieve the license from the database
            $license = trim( $this->get_license_key() );

            // data to send in our API request
            $api_params = array(
                'edd_action'=> 'deactivate_license',
                'license' 	=> $license,
                'item_name' => urlencode( $this->get_var( 'item_name' ) ), // the name of our product in EDD
                'item_id'   => urlencode( $this->get_var( 'item_id' ) ), // the name of our product in EDD
                'url'       => home_url()
            );

            // Call the custom API.
            $response = wp_remote_post( $this->get_var( 'store_url' ), array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            // $license_data->license will be either "deactivated" or "failed"
	        if ( 'valid' != $license_data->license ) {
		        delete_option( $this->get_var( 'license_status' ) );
		        wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=woo-smartbill' ) );
	        }
        }
    }

    public function check_license() {

        $license = trim( get_option( $this->get_var( 'license_key' ) ) );

        $api_params = array(
            'edd_action' => 'check_license',
            'license'    => $license,
            'item_name'  => urlencode( $this->get_var( 'item_name' ) ),
            'item_id'    => absint( $this->get_var( 'item_id' ) ),
            'url'        => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post(
            $this->get_var( 'store_url' ),
            array(
                'timeout' => 15,
                'sslverify' => false,
                'body' => $api_params
            )
        );

        if ( is_wp_error( $response ) )
            return false;

        $license_data = json_decode(
            wp_remote_retrieve_body( $response )
        );

        if ( $license_data->license != 'valid' ) {
            delete_option( $this->get_var( 'license_status' ) );
        }

    }

    public function is_valid(){
    	$license = $this->get_license_key();

    	if ( ! $license ) {
    		return false;
    	}

    	$license_status = $this->get_license_status();

    	if ( 'valid' != $license_status ) {
    		return false;
    	}

    	return true;
    }

    public function verify_license(){

    	$status  = $this->get_license_status();
    	$license = $this->get_license_key();

    	if ( ! $license ) {
    		return;
    	}

    	if ( 'valid' == $status ) {
    		return;
    	}

    	$this->activate_license();

    }

}

$woo_smartbill_license = Woo_SmartBill_License::get_instance();