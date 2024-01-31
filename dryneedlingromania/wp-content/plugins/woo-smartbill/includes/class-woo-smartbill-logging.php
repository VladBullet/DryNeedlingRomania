<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Woo_SmartBill_Logging {

	public $is_writable = true;
	private $filename   = '';
	private $file       = '';

	public function __construct() {}

	public static function get_instance() {
		static $inst;

		if ( ! $inst ) {
			$inst = new Woo_SmartBill_Logging();
		}

		return $inst;
	}

	public function setup_log_file() {

		$this->filename   = 'woo-smartbill-' . wp_hash( home_url( '/' ) ) . '.log';
		$this->file       = trailingslashit( WC_LOG_DIR ) . $this->filename;

		if ( ! is_writeable( WC_LOG_DIR ) ) {
			$this->is_writable = false;
		}

	}

	public function get_file_contents() {
		return $this->get_file();
	}

	public function log_to_file( $message = '' ) {
		$message = date( 'Y-n-d H:i:s' ) . ' - ' . $message . "\r\n";
		$this->write_to_log( $message );

	}

	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
	}

	protected function write_to_log( $message = '' ) {
		$file = $this->get_file();
		$file .= $message;
		@file_put_contents( $this->file, $file );
	}

	public function get_log_file_path() {
		return $this->file;
	}

}

function woo_smartbill_add_log( $message = '' ) {
	$woo_smartbill_logging = Woo_SmartBill_Logging::get_instance();

	if( function_exists( 'mb_convert_encoding' ) ) {

		$message = mb_convert_encoding( $message, 'UTF-8' );

	}

	$woo_smartbill_logging->log_to_file( $message );
}
