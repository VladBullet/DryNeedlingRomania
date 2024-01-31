<?php

namespace Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Page_Title extends Widget_Base {

	public function get_name() {
		return 'thim-page-title';
	}

	public function get_title() {
		return esc_html__( 'Page Title', 'eduma' );
	}

	public function get_icon() {
		return 'eicon-archive-title';
	}

	protected function get_html_wrapper_class() {
		return  $this->get_name();
	}

	public function get_categories() {
		return [ 'thim_ekit' ];
	}

	public function get_base() {
		return basename( __FILE__, '.php' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'Page Title', 'eduma' )
			]
		);
		$this->add_control(
			'tag',
			[
				'label'   => esc_html__( 'HTML Tag', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h2',
			]
		);
		$this->add_responsive_control(
			'text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'eduma' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'eduma' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'eduma' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'eduma' ),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
						'{{WRAPPER}}.thim-page-title' => 'text-align: {{VALUE}};'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'page_title_settings',
			[
				'label' => esc_html__( 'Setting', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'page_title_typography',
				'label'    => esc_html__( 'Typography', 'eduma' ),
				'selector' => '{{WRAPPER}}.thim-page-title .page-title',
			]
		);
		$this->add_control(
			'page_title_color',
			[
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}.thim-page-title .page-title' => 'color: {{VALUE}};'
				],
			]
		);
		$this->add_control(
			'blend_mode',
			array(
				'label'     => esc_html__( 'Blend Mode', 'thim-elementorkits' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => esc_html__( 'Normal', 'thim-elementorkits' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'difference'  => 'Difference',
					'exclusion'   => 'Exclusion',
					'hue'         => 'Hue',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}}.thim-page-title .page-title' => 'mix-blend-mode: {{VALUE}}',
				),
				'separator' => 'none',
			)
		);
		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        echo '<'.$settings['tag'].' class="page-title">'.thim_get_page_title('','Blog').'</'.$settings['tag'].'>';
	}
}
