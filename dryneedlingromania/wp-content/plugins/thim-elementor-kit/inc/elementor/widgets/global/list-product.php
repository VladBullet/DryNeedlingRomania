<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Core\Schemes;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Thim_EL_Kit\GroupControlTrait;
use Thim_EL_Kit\Elementor\Controls\Controls_Manager as Thim_Control_Manager;

class Thim_Ekit_Widget_List_Product extends Thim_Ekit_Products_Base {
	use GroupControlTrait;

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

	}

	public function get_name() {
		return 'thim-ekits-list-product';
	}

	public function get_title() {
		return esc_html__( 'List Product', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-gallery-grid';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

	public function get_keywords() {
		return [
			'thim',
			'product',
			'list product',
			'products',
		];
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls() {
		$this->register_product_setting();

		$this->register_style_layout();
		$this->register_style_product_tab();
		parent::register_style_image();
		//
		parent::register_style_content();
		//
		parent::register_layout_content();

		parent::register_style_sale_controls();
		$this->_register_settings_slider(
			array(
				'style' => 'slider',
			)
		);

		$this->_register_setting_slider_dot_style(
			array(
				'style'                   => 'slider',
				'slider_show_pagination!' => 'none',
			)
		);

		$this->_register_setting_slider_nav_style(
			array(
				'style'             => 'slider',
				'slider_show_arrow' => 'yes',
			)
		);

	}

	protected function register_product_setting() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Options', 'thim-elementor-kit' ),
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => esc_html__( 'Skin', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'thim-elementor-kit' ),
					'tab'     => esc_html__( 'Tab', 'thim-elementor-kit' ),
					'slider'  => esc_html__( 'Slider', 'thim-elementor-kit' ),
				),
			)
		);

		$this->add_control(
			'cat_slug',
			array(
				'label'    => esc_html__( 'Select Category', 'thim-elementor-kit' ),
				'type'     => Thim_Control_Manager::SELECT2,
				'multiple' => true,
				'sortable' => true,
				'options'  => \Thim_EL_Kit\Elementor::get_cat_taxonomy( 'product_cat', false , false ),
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order by', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'recent' => esc_html__( 'Date', 'thim-elementor-kit' ),
					'title'  => esc_html__( 'Title', 'thim-elementor-kit' ),
					'random' => esc_html__( 'Random', 'thim-elementor-kit' ),
				),
				'default' => 'recent',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order by', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'thim-elementor-kit' ),
					'desc' => esc_html__( 'DESC', 'thim-elementor-kit' ),
				),
				'default' => 'asc',
			)
		);
		$this->add_control(
			'text_more_tabs',
			array(
				'label'     => esc_html__( 'Title show more tabs', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'More',
				'condition' => array(
					'style' => 'tab',
				),
			)
		);
		$this->add_control(
			'limit_item_tabs',
			array(
				'label'   => esc_html__( 'Limit show item tab', 'thim-elementor-kit' ),
				'default' => '2',
				'type'    => Controls_Manager::NUMBER,
				'condition' => array(
					'style' => 'tab',
				),
			)
		);
		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Number Post', 'thim-elementor-kit' ),
				'default' => '4',
				'type'    => Controls_Manager::NUMBER,
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_product_tab() {
		$this->start_controls_section(
			'section_style_product_tab',
			array(
				'label'     => esc_html__( 'Product Tab', 'thim-elementor-kit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'style' => 'tab',
				),
			)
		);

		$this->add_responsive_control(
			'product_tab_align',
			array(
				'label'     => esc_html__( 'Alignment', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'   => array(
						'title' => esc_html__( 'Left', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'  => array(
						'title' => esc_html__( 'Right', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-filter' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_tab_item_spacing',
			array(
				'label'     => esc_html__( 'Horizontal spacing', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 120,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-tabs li' => 'margin-right:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .thim-product-tabs .cat-dropdown' => 'margin-left:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .thim-product-tabs .cat-item' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				),
				'default'   => array(
					'size' => 80,
				),
			)
		);
		$this->add_responsive_control(
			'product_spacing_tab_item',
			array(
				'label'     => esc_html__( 'Vertical spacing', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 120,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-filter' => 'margin: 0 auto {{SIZE}}{{UNIT}} auto',
				),
				'default'   => array(
					'size' => 80,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_item_typography',
				'label'    => esc_html__( 'Typography', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .thim-product-tabs .nav-tabs li a,{{WRAPPER}} .cat-more,{{WRAPPER}} .cat-item',
			)
		);
		// start tab for content
		$this->start_controls_tabs(
			'product_style_tabs_item'
		);

		// start normal tab
		$this->start_controls_tab(
			'tab_item_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'tab_item_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-tabs li a,{{WRAPPER}} .cat-more ,{{WRAPPER}} .cat-item a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-tabs li a,{{WRAPPER}} .cat-more,{{WRAPPER}} .cat-item a' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		// end normal tab

		// start active tab
		$this->start_controls_tab(
			'tab_item_style_active',
			array(
				'label' => esc_html__( 'Active', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'tab_item_text_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-tabs li a:hover,{{WRAPPER}} .thim-product-tabs .nav-tabs li.active a,
					{{WRAPPER}} .cat-item a:hover,{{WRAPPER}} .cat-more.active,{{WRAPPER}} .cat-item.active a,{{WRAPPER}} .cat-more:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-product-tabs .nav-tabs li a:hover,{{WRAPPER}} .thim-product-tabs .nav-tabs li.active a,
					{{WRAPPER}} .cat-item a:hover,{{WRAPPER}} .cat-more.active,{{WRAPPER}} .cat-more:hover,{{WRAPPER}} .cat-item.active a' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		// end hover tab
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_style_layout() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => esc_html__( 'Layout', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'thim-elementor-kit' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}}' => '--thim-ekits-product-columns: repeat({{VALUE}}, 1fr)',
				),
				'condition'      => array(
					'style' => array( 'default', 'tab' ),
				),
			)
		);
		$this->add_responsive_control(
			'column_gap',
			array(
				'label'     => esc_html__( 'Columns Gap', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--thim-ekits-product-column-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'     => esc_html__( 'Rows Gap', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--thim-ekits-product-row-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'item_pr_border',
				'selector'  => '{{WRAPPER}} .product',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_pr_box_shadow',
				'exclude'  => array(
					'box_shadow_position',
				),
				'selector' => '{{WRAPPER}} .product',
			)
		);
		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['style'] == 'tab' ) {
			$this->render_product_tab( $settings );
		} else {
			$query_args = array(
				'post_status'         => 'publish',
				'post_type'           => 'product',
				'posts_per_page'      => $settings['posts_per_page'],
				'order'               => ( 'asc' == $settings['order'] ) ? 'asc' : 'desc',
				'ignore_sticky_posts' => true,
			);

			switch ( $settings['order_by'] ) {
				case 'recent':
					$query_args['order_by'] = 'post_date';
					break;
				case 'title':
					$query_args['order_by'] = 'post_title';
					break;
				default: // random
					$query_args['order_by'] = 'rand';
			}
			//var_dump($settings['cat_slug']);
			if ( $settings['cat_slug'] ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $settings['cat_slug'],
					),
				);
			}

			$the_query   = new \WP_Query( $query_args );
			$class       = 'thim-ekits-product';
			$class_inner = 'thim-ekits-product__inner';
			$class_item  = 'thim-ekits-product__item';

			if ( $the_query->have_posts() ) {
				if ( isset( $settings['style'] ) && $settings['style'] == 'slider' ) {
					$swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
					$class       .= ' thim-ekits-sliders ' . $swiper_class;
					$class_inner = 'swiper-wrapper';
					$class_item  .= ' swiper-slide';

					$this->render_nav_pagination_slider( $settings );
				}
				?>
				<div class="<?php echo esc_attr( $class ); ?>">
					<div class="<?php echo esc_attr( $class_inner ); ?>">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							?>
							<div <?php wc_product_class( $class_item ); ?>>
								<?php parent::render_item_product( $settings ); ?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
			} else {
				echo '<div class="message-info">' . __( 'No data were found matching your selection, you need to create Post or select Category of Widget.', 'thim-elementor-kit' ) . '</div>';
			}

			wp_reset_postdata();
		}
	}

	public function render_product_tab( $settings ) {
		$params = array(
			'page_id'   => get_the_id(),
			'widget_id' => $this->get_id()
		);
		$list_tab   = $list_dropdown = '';
		$break_list = (!empty($settings['limit_item_tabs'])) ? $settings['limit_item_tabs'] : 2;
		if ( $settings['cat_slug'] ) {
			$cat_slugs = $settings['cat_slug'];
		} else {
			$cat_slugs         = array();
			$all_product_cat = get_terms(
				'product_cat',
				array(
					'hide_empty' => false,
					'number'     => 4,
				)
			);
			if ( ! is_wp_error( $all_product_cat ) ) {
				foreach ( $all_product_cat as $cat_id_df ) {
					$cat_slugs[] = esc_attr( $cat_id_df->slug );
				}
			}
		}

		echo '<div class="thim-product-tabs thim-block-filter" id="thim-filter-' . esc_attr( $this->get_id() ) . '" data-params="' . htmlentities( json_encode( $params ) ) . '">';
		if ( $cat_slugs ) {
			$cat_default_active = $cat_slugs;
			foreach ( $cat_slugs as $k => $tab ) {
				$term = get_term_by( 'slug', $tab, 'product_cat' );
				if ( $term ) {
					$tab_class = ' class="cat-item"';
					if ( $k == 0 ) {
						$tab_class          = ' class="cat-item active"';
						$cat_default_active = $term->slug;
					}
					if ( $k < $break_list ) {
						$list_tab .= '<li' . $tab_class . '><a data-cat="' . $term->slug . '">' .
							esc_html( $term->name ) . '</a></li>';
					} else {
						$list_dropdown .= '<li' . $tab_class . '><a data-cat="' . $term->slug . '">' .
							$term->name . '</a></li>';
					}
				}
			}
			// show html tab
			echo '<div class="nav-filter">';
			if ( $list_tab ) {
				echo '<ul class="nav nav-tabs">' . wp_kses_post( $list_tab ) . '</ul>';
			}
			if ( $list_dropdown ) {
				$title_show_more = (!empty($settings['text_more_tabs']))  ? $settings['text_more_tabs'] :'More';
				echo '<div class="cat-dropdown">';
				echo '<div class="cat-more"><span>' . esc_html__( $title_show_more , 'thim-elementor-kit' ) . '<i class="fa fa-caret-down"></i></span></div>';
				echo '<ul class="pulldown-list">' . $list_dropdown . '</ul>';
				echo '</div>';
			}
			echo '</div>';

			// render content
			echo '<div class="sc-loop"><div class="loop-wrapper">';
			$this->render_data_content_tab( $settings, $cat_default_active );
			echo '</div></div>';
		}
		echo '</div>';
	}

	public function render_data_content_tab( $settings, $cat_slug ) {
		$query_args = array(
			'post_status'         => 'publish',
			'post_type'           => 'product',
			'posts_per_page'      => $settings['posts_per_page'],
			'order'               => ( 'asc' == $settings['order'] ) ? 'asc' : 'desc',
			'ignore_sticky_posts' => true,
		);

		switch ( $settings['order_by'] ) {
			case 'recent':
				$query_args['order_by'] = 'post_date';
				break;
			case 'title':
				$query_args['order_by'] = 'post_title';
				break;
			default: // random
				$query_args['order_by'] = 'rand';
		}
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $cat_slug,
			),
		);

		$the_query = new \WP_Query( $query_args );
		// Show HTMl content

		if ( $the_query->have_posts() ) {
			?>
			<div class="thim-ekits-product">
				<div class="thim-ekits-product__inner">
					<?php
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						?>
						<div <?php wc_product_class( 'thim-ekits-product__item' ); ?>>
							<?php parent::render_item_product( $settings ); ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		} else {
			echo '<div class="message-info">' . __( 'No data were found matching your selection, you need to create Post or select Category of Widget.', 'thim-elementor-kit' ) . '</div>';
		}


		wp_reset_postdata();
	}
}
