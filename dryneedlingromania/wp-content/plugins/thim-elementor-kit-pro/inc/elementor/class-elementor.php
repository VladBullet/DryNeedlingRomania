<?php
namespace Thim_EL_Kit_Pro;

use Thim_EL_Kit_Pro\SingletonTrait;
use Thim_EL_Kit_Pro\Modules\ArchiveCourse\Init as ArchiveCourse;
use Thim_EL_Kit_Pro\Modules\SingleCourse\Init as SingleCourse;

class Elementor {
	use SingletonTrait;

	const CATEGORY_ARCHIVE_COURSE = 'thim_ekit_archive_course';
	const CATEGORY_SINGLE_COURSE  = 'thim_ekit_single_course';

	const WIDGETS = array(
		'archive-course' => array( 'archive-course' ),
		'single-course'  => array(
			'course-title',
			'course-instructor',
			'course-meta',
			'course-category',
			'course-tags',
			'course-image',
			'course-price',
			'course-graduation',
			'course-user-time',
			'course-user-progress',
			'course-tabs',
			'course-extra',
			'course-buttons',
			'course-related',
		),
	);

	public function __construct() {
		add_filter( 'thim_ekit_elementor_category', array( $this, 'add_categories' ) );
		add_filter( 'thim_ekit/elementor/widgets/list', array( $this, 'add_widgets' ) );
		add_filter( 'thim_ekit/elementor/widget/file_path', array( $this, 'change_widget_path' ), 10, 2 );
		add_filter( 'thim_ekit/elementor/documents/preview_item', array( $this, 'change_documents_preview_item' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_learnpress_scripts' ) );
	}

	public function add_widgets( $widget_default ) {
		$widgets = self::WIDGETS;

		global $post;

		// Only register archive-post, post-title in Elementor Editor only template.
		if ( $post && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$type = get_post_meta( $post->ID, \Thim_EL_Kit\Custom_Post_Type::TYPE, true );

			foreach ( $widgets as $key => $widget ) {
				if ( $key === 'archive-course' && ( ! class_exists( 'LearnPress' ) || $type !== ArchiveCourse::instance()->tab ) ) {
					unset( $widgets['archive-course'] );
				}

				if ( $key === 'single-course' && ( ! class_exists( 'LearnPress' ) || $type !== SingleCourse::instance()->tab ) ) {
					unset( $widgets['single-course'] );
				}
			}
		}

		if ( ! class_exists( 'LearnPress' ) ) {
			unset( $widgets['archive-course'] );
			unset( $widgets['single-course'] );
		}

		$widgets = array_merge( $widget_default, $widgets );

		return $widgets;
	}

	public function change_widget_path( $path, $widget ) {
		foreach ( self::WIDGETS as $key => $widgets ) {
			if ( in_array( $widget, $widgets ) ) {
				$path = THIM_EKIT_PRO_PLUGIN_PATH . 'inc/elementor/widgets/' . $key . '/' . $widget . '/' . $widget . '.php';
			}
		}

		return $path;
	}

	public function change_documents_preview_item( $preview ) {
		if ( class_exists( 'LearnPress' ) ) {
			$preview = array(
				'type'      => SingleCourse::instance()->tab,
				'post_type' => 'lp_course',
			);
		}

		return $preview;
	}

	public function add_categories( $categories ) {
		return array(
			self::CATEGORY_ARCHIVE_COURSE => array(
				'title' => esc_html__( 'Thim Archive Course', 'thim-elementor-kit' ),
				'icon'  => 'fa fa-plug',
			),
			self::CATEGORY_SINGLE_COURSE  => array(
				'title' => esc_html__( 'Thim Single Course', 'thim-elementor-kit' ),
				'icon'  => 'fa fa-plug',
			),
		) + $categories;
	}

	/** Nhamdv */
	public function get_tab_options() {
		$tab_options = array(
			'overview'   => esc_html__( 'Overview', 'thim-elementor-kit' ),
			'curriculum' => esc_html__( 'Curriculum', 'thim-elementor-kit' ),
			'faqs'       => esc_html__( 'FAQs', 'thim-elementor-kit' ),
			'instructor' => esc_html__( 'Instructor', 'thim-elementor-kit' ),
		);

		if ( class_exists( '\LP_Addon_Announcements' ) ) {
			$tab_options['announcements'] = esc_html__( 'Announcements', 'thim-elementor-kit' );
		}

		if ( class_exists( '\LP_Addon_Course_Review' ) ) {
			$tab_options['reviews'] = esc_html__( 'Reviews', 'thim-elementor-kit' );
		}

		if ( class_exists( '\LP_Addon_Students_List' ) ) {
			$tab_options['students-list'] = esc_html__( 'Students List', 'thim-elementor-kit' );
		}

		return apply_filters( 'thim_ekits_learnpress_tab_options', $tab_options );
	}

	public function enqueue_learnpress_scripts() {
		if ( class_exists( 'LearnPress' ) && ! in_array( \LP_Page_Controller::page_current(), array( LP_PAGE_COURSES, LP_PAGE_SINGLE_COURSE_CURRICULUM ) ) ) {
			if ( ! wp_script_is( 'lp-global' ) ) {
				wp_enqueue_script( 'lp-global', LP_PLUGIN_URL . 'assets/src/js/global.js', array( 'jquery', 'underscore', 'utils' ) );
			}
			if ( ! wp_script_is( 'lp-utils' ) ) {
				wp_enqueue_script( 'lp-utils', LP_PLUGIN_URL . 'assets/js/dist/utils.js', array( 'jquery' ) );
			}
			if ( ! wp_script_is( 'lp-single-course' ) ) {
				wp_enqueue_script(
					'lp-single-course',
					LP_PLUGIN_URL . 'assets/js/dist/frontend/single-course.js',
					array(
						'jquery',
						'wp-element',
						'wp-compose',
						'wp-data',
						'wp-hooks',
						'wp-api-fetch',
						'lodash',
						'lp-global',
						'lp-utils',
					),
					false,
					true
				);
			}
		}
	}
}
Elementor::instance();
