<?php
namespace Elementor;

use Elementor\Plugin;

class Thim_Ekit_Widget_Course_Tabs extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-course-tabs';
	}

	public function get_title() {
		return esc_html__( 'Thim Course Tabs', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'eicon-archive-posts';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit_Pro\Elementor::CATEGORY_SINGLE_COURSE );
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

		$repeater_header = new \Elementor\Repeater();

		$repeater_header->add_control(
			'tab_key',
			array(
				'label'   => esc_html__( 'Type', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overview',
				'options' => \Thim_EL_Kit_Pro\Elementor::instance()->get_tab_options(),
			)
		);

		$repeater_header->add_control(
			'icon',
			array(
				'label'       => esc_html__( 'Icon', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$repeater_header->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'thim_tab_repeater',
			array(
				'label'       => esc_html__( 'Tabs', 'thim-elementor-kit' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_header->get_controls(),
				'default'     => array(
					array(
						'tab_key' => 'overview',
					),
					array(
						'tab_key' => 'curriculum',
					),
					array(
						'tab_key' => 'instructor',
					),
					array(
						'tab_key' => 'faqs',
					),
				),
				'title_field' => '<span style="text-transform: capitalize;">{{{ tab_key.replace("_", " ") }}}</span>',
			)
		);

		$this->add_control(
			'active_tab',
			array(
				'label'   => esc_html__( 'Active tab', 'thim-elementor-kit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'overview',
				'options' => \Thim_EL_Kit_Pro\Elementor::instance()->get_tab_options(),
			)
		);

		$this->end_controls_section();
		$this->_register_style_course_tab();
	}

	protected function _register_style_course_tab() {
		$this->start_controls_section(
			'section_style_course_tab',
			array(
				'label' => esc_html__( 'Item Tab', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'course_tab_align',
			array(
				'label'     => esc_html__( 'Alignment', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'thim-elementor-kit' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'right',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .ekits-course-tabs' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'course_tab_item_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 120,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'margin: 0 0 {{SIZE}}{{UNIT}} 0',
				),
			)
		);

		$this->add_responsive_control(
			'course_tab_item_margin',
			array(
				'label'      => esc_html__( 'Margin', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'course_tab_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// start tab for content
		$this->start_controls_tabs(
			'course_style_tabs_item'
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
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_item_border',
				'label'    => esc_html__( 'Border', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .ekits-course-tabs .tab-item',
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
					'{{WRAPPER}} .ekits-course-tabs .tab-item:hover,{{WRAPPER}} .ekits-course-tabs .tab-item[aria-selected="true"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_item_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item:hover,{{WRAPPER}} .ekits-course-tabs .tab-item[aria-selected="true"]' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_item_border_active',
				'label'    => esc_html__( 'Border', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .ekits-course-tabs .tab-item:hover,{{WRAPPER}} .ekits-course-tabs .tab-item[aria-selected="true"]',
			)
		);

		$this->end_controls_tab();
		// end hover tab

		$this->end_controls_tabs();

		$this->add_control(
			'tab_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ekits-course-tabs .tab-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_item_typography',
				'label'    => esc_html__( 'Typography', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .ekits-course-tabs .tab-item',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_course_tab_content',
			array(
				'label' => esc_html__( 'Item Content', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'course_tab_content_border_active',
				'label'          => esc_html__( 'Border', 'thim-elementor-kit' ),
				'selector'       => '{{WRAPPER}} .ekits-content-course-tabs',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'    => '1',
							'right'  => '1',
							'bottom' => '1',
							'left'   => '1',
						),
					),
					'color'  => array(
						'default' => '#f5f5f5',
					),
				),
			)
		);

		$this->add_control(
			'course_tab_content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ekits-content-course-tabs' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'course_tab_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'thim-elementor-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ekits-content-course-tabs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'course_tab_content_text_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ekits-content-course-tabs [role="tabpanel"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'course_tab_content_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ekits-content-course-tabs' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'course_tab_content_typography',
				'label'    => esc_html__( 'Typography', 'thim-elementor-kit' ),
				'selector' => '{{WRAPPER}} .ekits-content-course-tabs [role="tabpanel"]',
			)
		);

		$this->end_controls_section();
	}

	public function render() {
		do_action( 'thim-ekit/modules/single-course/before-preview-query' );

		$course = learn_press_get_course();

		if ( ! $course ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$tab_default = learn_press_get_course_tabs();

		$tabs = array();

		foreach ( $settings['thim_tab_repeater'] as $item ) {
			if ( ! isset( $tab_default[ $item['tab_key'] ] ) ) {
				continue;
			}

			$tabs[ $item['tab_key'] ] = array(
				'title'    => ! empty( $item['text'] ) ? esc_html( $item['text'] ) : $tab_default[ $item['tab_key'] ]['title'],
				'icon'     => ! empty( $item['icon'] ) ? $item['icon'] : '',
				'callback' => $tab_default[ $item['tab_key'] ]['callback'],
			);
		}

		if ( empty( $tabs ) ) {
			return;
		}

		// Fix class not found.
		if ( ! class_exists( 'LP_Model_User_Can_View_Course_Item' ) ) {
			require_once LP_PLUGIN_PATH . 'inc/course/class-model-user-can-view-course-item.php';
		}

		$tab_keys   = array_keys( $tabs );
		$active_tab = ! empty( $settings['active_tab'] ) ? $settings['active_tab'] : reset( $tab_keys );
		?>

		<div class="thim-ekit-single-course__tabs thim-ekit-tablist">
			<div class="ekits-course-tabs" role="tablist" aria-label="<?php echo esc_attr_e( 'Course Tabs', 'thim-elementor-kit' ); ?>">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<div class="tab-item" role="tab" aria-selected="<?php echo esc_attr( $active_tab === $key ? 'true' : 'false' ); ?>" aria-controls="<?php echo esc_attr( 'panel-' . $key ); ?>" id="<?php echo esc_attr( 'tab-' . $key ); ?>" tabindex="<?php echo esc_attr( $active_tab === $key ? 0 : -1 ); ?>">
						<span class="ekits-course-tabs__icon"><?php Icons_Manager::render_icon( $tab['icon'] ); ?></span>
						<?php echo esc_html( $tab['title'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="ekits-content-course-tabs">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<div id="<?php echo esc_attr( 'panel-' . $key ); ?>" role="tabpanel" tabindex="<?php echo esc_attr( $active_tab === $key ? 0 : -1 ); ?>" aria-labelledby="<?php echo esc_attr( 'tab-' . $key ); ?>" <?php echo esc_attr( $active_tab !== $key ? 'hidden' : '' ); ?>>
						<?php
						if ( is_callable( $tab['callback'] ) ) {
							call_user_func( $tab['callback'], $key, $tab );
						} else {
							do_action( 'learn-press/course-tab-content', $key, $tab );
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>
				document.body.dispatchEvent(new CustomEvent("thimEkitsEditor:init" ));
				LP.Hook.doAction( 'lp_course_curriculum_skeleton', <?php echo absint( $course->get_id() ); ?> );
			</script>
			<?php
		}

		do_action( 'thim-ekit/modules/single-course/after-preview-query' );
	}
}
