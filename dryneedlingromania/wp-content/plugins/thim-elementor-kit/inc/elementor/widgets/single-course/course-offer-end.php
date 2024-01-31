<?php

namespace Elementor;

class Thim_Ekit_Widget_Course_Offer_End extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-course-offer-end';
	}

	public function get_title() {
		return esc_html__( 'Course Offer End', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'thim-eicon  eicon-countdown';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY_SINGLE_COURSE );
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'thim-elementor-kit' ),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'This offer ends in',
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'       => esc_html__( 'Icon', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$this->end_controls_section();
		$this->_register_style_offer_end();
	}

	protected function _register_style_offer_end() {
		$this->start_controls_section(
			'section_offer_date_style',
			array(
				'label' => esc_html__( 'Icon', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-course__offer-end i'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .thim-ekit-single-course__offer-end svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 2,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .thim-ekit-single-course__offer-end i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .thim-ekit-single-course__offer-end svg' => 'width: {{SIZE}}{{UNIT}}; height: auto',
				),
			)
		);
		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => esc_html__( 'Icon Spacing', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-course__offer-end i, {{WRAPPER}} .thim-ekit-single-course__offer-end svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'Text', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-course__offer-end' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .thim-ekit-single-course__offer-end',
			)
		);

		$this->add_responsive_control(
			'text_and_date_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .thim-ekit-single-course__offer-end .text' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	public function render() {
		do_action( 'thim-ekit/modules/single-course/before-preview-query' );
		$settings = $this->get_settings_for_display();
		$course   = learn_press_get_course();
		$user     = learn_press_get_current_user();

		if ( ! $course ) {
			return;
		}

		if ( $user && $user->has_enrolled_course( get_the_ID() ) ) {
			return;
		}

		$has_sale = $course->has_sale_price();
		if ( ! $has_sale ) {
			return;
		}
		$date_end = get_post_meta( get_the_ID(), '_lp_sale_end', true );

		if ( $date_end ) : ?>
			<div class="thim-ekit-single-course__offer-end">

				<?php Icons_Manager::render_icon( $settings['icon'] ); ?>

				<?php if ( $settings['text'] ) {
					echo '<span class="text">' . esc_html( $settings['text'] ) . '</span>';
				} ?>

				<span id="date-offer" data-offerend="<?php echo $date_end; ?>"></span>
			</div>
		<?php endif;

		do_action( 'thim-ekit/modules/single-course/after-preview-query' );
	}

}
