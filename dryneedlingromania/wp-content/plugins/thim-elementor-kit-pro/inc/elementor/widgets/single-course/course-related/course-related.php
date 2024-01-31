<?php
namespace Elementor;

use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Thim_Ekit_Widget_Course_Related extends Thim_Ekits_Course_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'thim-ekits-course-related';
	}

	public function get_title() {
		return esc_html__( 'Thim Course Related', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit_Pro\Elementor::CATEGORY_SINGLE_COURSE );
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_options',
			array(
				'label' => esc_html__( 'Options', 'thim-elementor-kit' ),
			)
		);

		$this->add_control(
			'number_posts',
			array(
				'label'   => esc_html__( 'Number Post', 'thim-elementor-kit' ),
				'default' => '4',
				'type'    => Controls_Manager::NUMBER,
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'thim-elementor-kit' ),
				'default' => '4',
				'type'    => Controls_Manager::NUMBER,
				'selectors'          => array(
					'{{WRAPPER}}' => '--thim-ekits-course-columns: repeat({{VALUE}}, 1fr)',
				),
			)
		);
		$this->end_controls_section();

		$this->_register_style_layout();

		parent::register_controls();

	}

	protected function _register_style_layout() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => esc_html__( 'Layout', 'thim-elementor-kit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'              => esc_html__( 'Columns Gap', 'thim-elementor-kit' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 30,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}}' => '--thim-ekits-course-column-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'     => esc_html__( 'Rows Gap', 'thim-elementor-kit' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 35,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--thim-ekits-course-row-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings   = $this->get_settings_for_display();
		$course_id  = get_the_ID();
		$query_args = array(
			'post_type'           => 'lp_course',
			'posts_per_page'      => $settings['number_posts'],
			'post__not_in'        => array( $course_id ),
			'paged'               => 1,
			'order'               => 'desc',
			'ignore_sticky_posts' => true,
		);

		$tag_ids = array();
		$tags    = get_the_terms( $course_id, 'course_tag' );

		if ( $tags ) {
			foreach ( $tags as $individual_tag ) {
				$tag_ids[] = $individual_tag->term_id;
			}
		}

		if ( $tag_ids ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'course_tag',
					'field'    => 'term_id',
					'terms'    => $tag_ids,
				),
			);
		}

		$the_query = new \WP_Query( $query_args );
		?>
		<div class="thim-ekits-course">
			<div class="thim-ekits-course__inner">
					<?php
					if ( $the_query->have_posts() ) :
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							parent::render_course( $settings, 'thim-ekits-course__item' );
						}
					endif;
					wp_reset_postdata();
					?>
				</div>
			</div>
		<?php
	}
}
