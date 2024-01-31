<?php
namespace Elementor;

use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Thim_Ekit_Widget_Product_Stock extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-product-stock';
	}

	public function get_title() {
		return esc_html__( 'Product Stock', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-product-stock';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY_SINGLE_PRODUCT );
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__( 'Gennal', 'storepify' ),
			]
		);
		$this->add_control(
			'style_stock',
			array(
				'label'     => esc_html__( 'Style stock', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => esc_html__( 'Default', 'thim-elementor-kit' ),
					'text_number_stock' => esc_html__( 'Text + Number product stock', 'thim-elementor-kit' ),
				),
			)
		);
		$this->add_control(
			'before_content',
			[
				'label'       => esc_html__( 'Content Before Number Product Stock', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Add your text here', 'thim-elementor-kit' ),
				'default' => esc_html__( 'Only', 'thim-elementor-kit' ),
				'label_block' => true,
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			]
		);
		$this->add_control(
			'after_content',
			[
				'label'       => esc_html__( 'Content After Number Product Stock', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Add your text here', 'thim-elementor-kit' ),
				'default' => esc_html__( 'item(s) left in stock!', 'thim-elementor-kit' ),
				'label_block' => true,
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			]
		);
		$this->add_control(
			'notify_content',
			[
				'label'       => esc_html__( 'Notify when there are no more products in stock', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Add your text here', 'thim-elementor-kit' ),
				'default' => esc_html__( 'There are no items available at this time.', 'thim-elementor-kit' ),
				'label_block' => true,
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			]
		);

        $this->add_control(
			'show_progress',
			[
				'label' => esc_html__( 'Show Progress', 'storepify' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'storepify' ),
				'label_off' => esc_html__( 'Off', 'storepify' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_product_stock_style',
			array(
				'label' => esc_html__( 'Style', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'heading_content',
			array(
				'label'     => esc_html__( 'Content', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				//'separator' => 'before',
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .content-before-item-availability,
					{{WRAPPER}} .content-after-item-availability' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Typography', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .content-before-item-availability,
				{{WRAPPER}} .content-after-item-availability',
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_control(
			'heading_stock',
			array(
				'label'     => esc_html__( 'Stock', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',

			)
		);
		$this->add_control(
			'stock_color',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .stock' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'out_of_stock_color',
			array(
				'label'     => esc_html__( 'Out of stock Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-elementorkits-single-product__stock .stock.out-of-stock' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'style_stock' => 'default',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stock_typography',
				'label'    => esc_html__( 'Typography', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .thim-ekit-single-product__stock .stock',

			)
		);
		$this->add_responsive_control(
			'stock_margin',
			array(
				'label'      => esc_html__( 'Margin', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .number-item-availability' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_control(
			'heading_progress',
			array(
				'label'     => esc_html__( 'Progress', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_control(
			'progress_color',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .progress span' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_control(
			'progress_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .progress' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_responsive_control(
			'progress_width',
			array(
				'label'      => esc_html__( 'Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .progress span' => 'width: {{SIZE}}{{UNIT}} !important',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_responsive_control(
			'progress_height',
			array(
				'label'      => esc_html__( 'Height', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .progress' => 'height: {{SIZE}}{{UNIT}} !important',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->add_responsive_control(
			'progress_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekit-single-product__stock .progress' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'style_stock' => 'text_number_stock',
				),
			)
		);
		$this->end_controls_section();
	}

	public function render() {
		do_action( 'thim-ekit/modules/single-product/before-preview-query' );

		$product = wc_get_product( false );

		if ( ! $product ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		?>

		<div class="thim-ekit-single-product__stock">
			<div class="stock-item-availability">
				<?php if($settings['style_stock'] == 'text_number_stock'){?>
				<div class="stock-item-flex">
					<?php
						$count_stock = $product->get_stock_quantity();
						if($count_stock == 0){
							echo esc_html__($settings['notify_content'],'thim-elementor-kit');
						}else{
							if(!empty($settings['before_content'])){
								echo '<div class="content-before-item-availability">'.esc_html__($settings['before_content'],'thim-elementor-kit').'</div>';
							}
							echo '<div class="number-item-availability stock">'.$count_stock.'</div>';
							if(!empty($settings['after_content'])){
								echo '<div class="content-after-item-availability">'.esc_html__($settings['after_content'],'thim-elementor-kit').'</div>';
							}
						}
					?>
				</div>
				<?php }else{
					echo wc_get_stock_html( $product );
				} ?>
				<?php if($settings['show_progress'] == 'yes'): ?>
					<div class="progress">
						<span style="width: 15%;"></span>
					</div>
				<?php endif;?>
			</div>
		</div>
		<?php
		do_action( 'thim-ekit/modules/single-product/after-preview-query' );
	}
}
