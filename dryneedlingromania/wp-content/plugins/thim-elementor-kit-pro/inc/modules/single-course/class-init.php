<?php
namespace Thim_EL_Kit_Pro\Modules\SingleCourse;

use Thim_EL_Kit\Modules\Modules;
use Thim_EL_Kit_Pro\SingletonTrait;

class Init extends Modules {
	use SingletonTrait;

	public function __construct() {
		$this->tab      = 'single-course';
		$this->tab_name = esc_html__( 'Single Course', 'thim-elementor-kit-pro' );

		parent::__construct();

		add_action( 'thim-ekit/modules/single-course/before-preview-query', array( $this, 'before_preview_query' ) );
		add_action( 'thim-ekit/modules/single-course/after-preview-query', array( $this, 'after_preview_query' ) );
	}

	public function template_include( $template ) {
		$this->template_include = is_singular( 'lp_course' ) && ! \LP_Global::course_item();

		return parent::template_include( $template );
	}

	public function get_preview_id() {
		global $post;

		$output = false;

		if ( $post ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post->ID );

			if ( $document ) {
				$preview_id = $document->get_settings( 'thim_ekits_preview_id' );

				$output = ! empty( $preview_id ) ? absint( $preview_id ) : false;
			}
		}

		return $output;
	}

	public function before_preview_query() {
		if ( $this->is_editor_preview() || $this->is_modules_view() ) {
			$this->after_preview_query();
			$preview_id = $this->get_preview_id();

			if ( $preview_id ) {
				$query = array(
					'p'         => absint( $preview_id ),
					'post_type' => 'lp_course',
				);
			} else {
				$query_vars = array(
					'post_type'      => 'lp_course',
					'posts_per_page' => 1,
				);

				$posts = get_posts( $query_vars );

				if ( ! empty( $posts ) ) {
					$query = array(
						'p'         => $posts[0]->ID,
						'post_type' => 'lp_course',
					);
				}
			}

			if ( ! empty( $query ) ) {
				\Elementor\Plugin::instance()->db->switch_to_query( $query, true );
			}
		}
	}

	public function after_preview_query() {
		if ( $this->is_editor_preview() || $this->is_modules_view() ) {
			\Elementor\Plugin::instance()->db->restore_current_query();
		}
	}

	public function is( $condition ) {
		if ( ! class_exists( '\LearnPress' ) ) {
			return false;
		}

		switch ( $condition['type'] ) {
			case 'all':
				return is_singular( 'lp_course' );
			case 'course_id':
				return is_singular( 'lp_course' ) && get_the_ID() === (int) $condition['query'];
			case 'course_category':
			case 'course_tag':
				$terms = wp_get_post_terms( get_the_ID(), get_taxonomies(), array( 'fields' => 'ids' ) );

				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return false;
				}

				return in_array( (int) $condition['query'], $terms, true );
		}

		return false;
	}

	public function priority( $type ) {
		$priority = 100;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
			case 'course_category':
			case 'course_tag':
				$priority = 20;
				break;
			case 'course_id':
				$priority = 30;
				break;
		}

		return apply_filters( 'thim_ekit_pro/condition/priority', $priority, $type );
	}

	public function get_conditions() {
		return array(
			array(
				'label'    => esc_html__( 'All courses', 'thim-elementor-kit-pro' ),
				'value'    => 'all',
				'is_query' => false,
			),
			array(
				'label'    => esc_html__( 'Select course', 'thim-elementor-kit-pro' ),
				'value'    => 'course_id',
				'is_query' => true,
			),
			array(
				'label'    => esc_html__( 'Course category', 'thim-elementor-kit-pro' ),
				'value'    => 'course_category',
				'is_query' => true,
			),
			array(
				'label'    => esc_html__( 'Course tag', 'thim-elementor-kit-pro' ),
				'value'    => 'course_tag',
				'is_query' => true,
			),
		);
	}
}

Init::instance();
