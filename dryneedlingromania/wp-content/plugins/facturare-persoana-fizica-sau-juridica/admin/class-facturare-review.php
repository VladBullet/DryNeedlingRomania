<?php

class GC_Facturare_Review {

	private $value;
	private $messages;
	private $link = 'https://wordpress.org/support/plugin/%s/reviews/#new-post';
	private $slug = 'facturare-persoana-fizica-sau-juridica';
	
	function __construct() {

		$this->messages = array(
			'notice'  => "Salut! Mă bucur că folosești pluginul de facturare de câteva zile - sper că iți place și iți este folositor. Dacă chiar iți este de ajutor o să te rog să îi lași un review. Așa vor afla mai mulți oameni de el și poate le va fi folositor și lor.<br><br>Mulțumesc,<br>George",
			'rate'    => 'Scrie un review',
			'rated'   => 'Adu-mi aminte mai tarziu',
			'no_rate' => 'Nu vreau.',
		);

		if ( isset( $args['messages'] ) ) {
			$this->messages = wp_parse_args( $args['messages'], $this->messages );
		}

		add_action( 'init', array( $this, 'init' ) );

	}

	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		$this->value = $this->value();

		if ( $this->check() ) {
			add_action( 'admin_notices', array( $this, 'five_star_wp_rate_notice' ) );
			add_action( 'wp_ajax_woo_facturare_review', array( $this, 'ajax' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'ajax_script' ) );
		}

	}

	private function check() {

		if ( ! current_user_can('manage_options') ) {
			return false;
		}

		return( time() > $this->value );

	}

	private function value() {

		$value = get_option( 'facturare-rate-time', false );

		if ( $value ) {
			return $value;
		}

		$value = time() + DAY_IN_SECONDS;
		update_option( 'facturare-rate-time', $value );

		return $value;

	}

	public function five_star_wp_rate_notice() {

		$url = sprintf( $this->link, $this->slug );

		?>
		<div id="<?php echo esc_attr($this->slug) ?>-epsilon-review-notice" class="notice notice-success" style="margin-top:30px;">
			<p><?php echo sprintf( wp_kses_post( $this->messages['notice'] ), $this->value ) ; ?></p>
			<p class="actions">
				<a id="woo-facturare-rate" href="<?php echo esc_url( $url ) ?>" target="_blank" class="button button-primary epsilon-review-button">
					<?php echo esc_html( $this->messages['rate'] ); ?>
				</a>
				<a id="woo-facturare-later" href="#" style="margin-left:10px" class="epsilon-review-button"><?php echo esc_html( $this->messages['rated'] ); ?></a>
				<a id="woo-facturare-no-rate" href="#" style="margin-left:10px" class="epsilon-review-button"><?php echo esc_html( $this->messages['no_rate'] ); ?></a>
			</p>
		</div>
		<?php
	}

	public function ajax() {

		check_ajax_referer( 'woo-facturare-review', 'security' );

		if ( ! isset( $_POST['check'] ) ) {
			wp_die( 'ok' );
		}

		$time = get_option( 'facturare-rate-time' );

		if ( 'woo-facturare-rate' == $_POST['check'] ) {
			$time = time() + YEAR_IN_SECONDS * 5;
		}elseif ( 'woo-facturare-later' == $_POST['check'] ) {
			$time = time() + WEEK_IN_SECONDS;
		}elseif ( 'woo-facturare-no-rate' == $_POST['check'] ) {
			$time = time() + YEAR_IN_SECONDS * 5;
		}

		update_option( 'facturare-rate-time', $time );
		wp_die( 'ok' );

	}

	public function enqueue() {
		wp_enqueue_script( 'jquery' );
	}

	public function ajax_script() {

		$ajax_nonce = wp_create_nonce( "woo-facturare-review" );

		?>

		<script type="text/javascript">
			jQuery( document ).ready( function( $ ){

				$( '.epsilon-review-button' ).click( function( evt ){
					var href = $(this).attr('href'),
						id = $(this).attr('id');

					// evt.preventDefault();

					var data = {
						action: 'woo_facturare_review',
						security: '<?php echo $ajax_nonce; ?>',
						check: id
					};

					$.post( '<?php echo admin_url( 'admin-ajax.php' ) ?>', data, function( response ) {
						$( '#<?php echo $this->slug ?>-epsilon-review-notice' ).slideUp( 'fast', function() {
							$( this ).remove();
						} );
					});

				} );

			});
		</script>

		<?php
	}
}