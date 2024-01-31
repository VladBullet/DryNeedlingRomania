<?php

namespace Elementor;

use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Thim_Ekit_Widget_Archive_Product extends Thim_Ekit_Products_Base {
	protected $attributes = array();

	/**
	 * Query args.
	 *
	 * @since 3.2.0
	 * @var   array
	 */
	protected $query_args = array();
	protected $current_permalink;

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-archive-product';
	}

	protected function get_html_wrapper_class() {
		return 'thim-ekits-archive-product';
	}

	public function get_title() {
		return esc_html__( 'Archive Product', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-archive-posts';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY_ARCHIVE_PRODUCT );
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_Setting',
			array(
				'label' => esc_html__( 'Setting', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'build_loop_item',
			array(
				'label'     => esc_html__( 'Build Loop Item', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'   => esc_html__( 'Choose a template', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '0',
				'options' => array( '0' => esc_html__( 'None', 'thim-elementor-kit' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'product' ),
				'condition'   => array(
					'build_loop_item' => 'yes',
				),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'     => esc_html__( 'Columns', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'selectors' => array(
					'body {{WRAPPER}} ul.products' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_control(
			'rows',
			array(
				'label'   => esc_html__( 'Rows', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Limit', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '-1',
				'condition' => array(
					'rows' => '',
				),
			)
		);

		$this->add_control(
			'paginate',
			array(
				'label'   => esc_html__( 'Paginate', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		parent::register_style_product_controls();

//		parent::register_controls();
		//
		parent::register_style_image();
		//
		parent::register_style_sale_controls();
		parent::register_style_content();
		//
		parent::register_layout_content();
		parent::register_style_pagination_controls();
	}

	protected function render() {
		if ( WC()->session ) {
			wc_print_notices();
		}

		$settings = $this->get_settings_for_display();

		$shortcode = $this->get_shortcode_object( $settings );

		$this->query_args = $shortcode->get_query_args();

		$query = new \WP_Query( $this->query_args );

		$paginated = ! $query->get( 'no_found_rows' );

		$results = (object) array(
			'ids'          => wp_parse_id_list( $query->posts ),
			'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
			'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
			'per_page'     => (int) $query->get( 'posts_per_page' ),
			'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
		);
		?>
		<div class="thim-ekit-archive-product thim-ekits-product">
			<?php
			if ( $results && $results->ids ) {
				// Setup the loop.
				wc_setup_loop(
					array(
						'columns'      => absint( $settings['columns'] ),
						'name'         => 'product',
						'is_shortcode' => true,
						'is_search'    => false,
						'is_paginated' => true,
						'total'        => $results->total,
						'total_pages'  => $results->total_pages,
						'per_page'     => $results->per_page,
						'current_page' => $results->current_page,
					)
				);

				do_action( 'woocommerce_before_shop_loop' );

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					foreach ( $results->ids as $product_id ) {
						$GLOBALS['post'] = get_post( $product_id );
						setup_postdata( $GLOBALS['post'] );
						?>
						<li <?php wc_product_class( '', $product_id ); ?>>
							<?php
							// render product
							parent::render_item_product( $settings );
							?>
						</li>
						<?php
					}
				}
				woocommerce_product_loop_end();
				if ( $settings['paginate'] == 'yes' ) {
					do_action( 'woocommerce_after_shop_loop' );
				}

				wp_reset_postdata();
				wc_reset_loop();
			} else {
				do_action( 'woocommerce_no_products_found' );
			}
			?>

		</div>

		<?php
	}

	protected function get_shortcode_object( $settings ) {
		return new \WC_Shortcode_Products(
			array(
				'columns'  => absint( $settings['columns'] ),
				'rows'     => absint( $settings['rows'] ),
				'paginate' => $settings['paginate'] === 'yes',
				'limit'    => floatval( $settings['limit'] ),
				'cache'    => false,
			),
			'products'
		);
	}

}
