<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

use Thim_EL_Kit\Elementor;
use Thim_EL_Kit\Settings;

class Thim_Ekit_Widget_Search_Form extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-search-form';
	}

	public function get_title() {
		return esc_html__( 'Search Form', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-search';
	}

	public function get_keywords() {
		return [
			'thim',
			'search from',
			'search',
		];
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Search Form', 'thim-elementor-kit' ),
			)
		);
		$this->_register_form_search();
		$this->_register_button_search();
		$this->end_controls_section();
		$this->register_section_style_search_form();
	}

	protected function _register_form_search() {
		$this->add_control(
			'advanced_type_search',
			array(
				'label' => esc_html__( 'Form Input', 'thim-elementor-kit' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'search_type',
			array(
				'label'   => esc_html__( 'Search Result', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'     => esc_html__( 'All', 'thim-elementor-kit' ),
					'product' => esc_html__( 'Product', 'thim-elementor-kit' ),
					'post'    => esc_html__( 'Blog', 'thim-elementor-kit' ),
				),
			)
		);
		$this->add_control(
			'placeholder_text',
			array(
				'label'   => esc_html__( 'Placeholder Text', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search products...', 'thim-elementor-kit' ),
			)
		);
	}

	protected function _register_button_search() {
		$this->add_control(
			'advanced_button_search',
			array(
				'label'     => esc_html__( 'Button Search', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'text_button_search',
			array(
				'label'   => esc_html__( 'Button Search Text', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'icon_button_search',
			array(
				'label'   => esc_html__( 'Button Search Icon', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'library' => 'tk',
					'value'   => 'tk tk-search',
				),
			)
		);
		$this->add_control(
			'icon_button_search_size',
			array(
				'label'     => esc_html__( 'Font Size Icon', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .button-search i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);
		$this->add_control(
			'spacing_icon_button_search',
			array(
				'label'     => esc_html__( 'Spacing Icon', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .button-search i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	protected function register_section_style_search_form() {
		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => esc_html__( 'Style', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'search_box_style_heading',
			array(
				'label'     => esc_html__( 'Box Search', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'search_form_layout',
			array(
				'label'     => esc_html__( 'Display', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'row'    => array(
						'title' => esc_html__( 'Inline', 'thim-elementor-kit' ),
						'icon'  => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Block', 'thim-elementor-kit' ),
						'icon'  => 'eicon-editor-list-ul',
					),
				),
				'default'   => 'row',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .input-group ' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding_search_form',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .input-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'border_search_form',
				'selector'  => '{{WRAPPER}} .thim-ekits-search-form .input-group',
				'condition' => array(
					'border_style_thim-ekits_search_form!' => '',
				),
			)
		);
		$this->add_control(
			'bg_search_form',
			array(
				'label'     => esc_html__( 'Background', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .input-group' => 'background: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'border_radius_search_form',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .input-group' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		// input
		$this->add_control(
			'input_search_style_heading',
			array(
				'label'     => esc_html__( 'Input', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'input_width',
			array(
				'label'      => esc_html__( 'Input Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);
		$this->add_responsive_control(
			'input_search_padding',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bg_input',
			array(
				'label'     => esc_html__( 'Background', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'background: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'color_input',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'color: {{VALUE}}',
					'{{WRAPPER}} .thim-ekits-search::placeholder'            => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'border_style_input_search',
			array(
				'label'     => esc_html__( 'Border Type', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => esc_html__( 'None', 'thim-elementor-kit' ),
					'solid'  => esc_html__( 'Solid', 'thim-elementor-kit' ),
					'double' => esc_html__( 'Double', 'thim-elementor-kit' ),
					'dotted' => esc_html__( 'Dotted', 'thim-elementor-kit' ),
					'dashed' => esc_html__( 'Dashed', 'thim-elementor-kit' ),
					'groove' => esc_html__( 'Groove', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'border-style: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'border_width_input_search',
			array(
				'label'      => esc_html__( 'Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),

				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),
				'condition' => array(
					'border_style_input_search!' => '',
				),
			)
		);
		$this->add_control(
			'border_color_input_search',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'border_style_input_search!' => '',
				),
			)
		);

		$this->add_control(
			'border_radius_input_search',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .thim-ekits-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),
				'condition'  => array(
					'border_radius_input_search!' => '',
				),
			)
		);

		// Button
		$this->add_control(
			'btn_search_style_heading',
			array(
				'label'     => esc_html__( 'Button', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'btn_width',
			array(
				'label'      => esc_html__( 'Button Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
					'%'  => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);
		$this->add_responsive_control(
			'padding_btn',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin_btn',
			array(
				'label'      => esc_html__( 'Margin', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'border_radius_button',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),
			)
		);
		$this->add_control(
			'border_style_button',
			array(
				'label'     => esc_html__( 'Border Type', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => esc_html__( 'None', 'thim-elementor-kit' ),
					'solid'  => esc_html__( 'Solid', 'thim-elementor-kit' ),
					'double' => esc_html__( 'Double', 'thim-elementor-kit' ),
					'dotted' => esc_html__( 'Dotted', 'thim-elementor-kit' ),
					'dashed' => esc_html__( 'Dashed', 'thim-elementor-kit' ),
					'groove' => esc_html__( 'Groove', 'thim-elementor-kit' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'border-style: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'border_width_button',
			array(
				'label'      => esc_html__( 'Width', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),

				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),
				'condition' => array(
					'border_style_button!' => '',
				),
			)
		);
		$this->add_control(
			'border_color_button',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'border_style_button!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_style_btn_search' );

		$this->start_controls_tab(
			'tab_btn_search_normal',
			array(
				'label' => esc_html__( 'Normal', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'color_btn_search',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search i,
                    {{WRAPPER}} .thim-ekits-search-form .button-search .text' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'bg_btn_search',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_btn_search_hover',
			array(
				'label' => esc_html__( 'Hover', 'thim-elementor-kit' ),
			)
		);
		$this->add_control(
			'hover_color_btn_search',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search:hover i,
                    {{WRAPPER}} .thim-ekits-search-form .button-search:hover .text' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'hover_bg_btn_search',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekits-search-form .button-search:hover' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'search_form',
			array(
				'data-appendto'  => '.search-results-' . esc_attr( $this->get_id() ),
				'data-post-type' => esc_attr( $settings['search_type'] ),
			)
		);
		?>

		<div class="thim-ekits-search-form">
			<form action="<?php echo esc_url( home_url( '/' ) ); ?>"
				  method="get" <?php Utils::print_unescaped_internal_string( $this->get_render_attribute_string( 'search_form' ) ); ?>>
				<div class="input-group">
					<input type="text" placeholder="<?php echo esc_attr( $settings['placeholder_text'] ); ?>" name="s"
						   class="thim-ekits-search"/>

					<?php if ( $settings['search_type'] != 'all' ) : ?>
						<input type="hidden" name="post_type"
							   value="<?php echo esc_attr( $settings['search_type'] ); ?>"/>
					<?php endif; ?>

					<button type="submit" class="button-search">
						<?php Icons_Manager::render_icon( $settings['icon_button_search'], array( 'aria-hidden' => 'true' ) ); ?>

						<?php if ( ! empty( $settings['text_button_search'] ) && isset( $settings['text_button_search'] ) ) : ?>
							<span class="text"><?php echo esc_html( $settings['text_button_search'] ); ?></span>
						<?php endif; ?>
					</button>
				</div>
			</form>
		</div>

		<?php
	}

}
