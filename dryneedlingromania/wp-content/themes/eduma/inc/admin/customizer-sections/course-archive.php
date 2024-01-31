<?php
/**
 * Section Course Archive
 *
 * @package Eduma
 */

thim_customizer()->add_section(
	array(
		'id'       => 'course_archive',
		'panel'    => 'course',
		'title'    => esc_html__( 'Archive Pages', 'eduma' ),
		'priority' => 10,
 	)
);

$callback ='';
if(class_exists( 'Thim_EL_Kit' )){
	$callback = array(
		'setting'  => 'thim_ekits_archive_course_tpl',
		'operator' => '!=',
		'value'    => 'ekits_tpl',
	);

	thim_customizer()->add_field(
		array(
			'id'       => 'thim_ekits_archive_course_tpl',
			'type'     => 'select',
			'label'    => esc_html__( 'Template', 'eduma' ),
  			'priority' => 11,
			'default'  => '',
 			'multiple' => 0,
			'section'  => 'course_archive',
			'choices'  => array(
				'' => esc_html__( 'Theme Template', 'eduma' ),
				'ekits_tpl' => esc_html__( 'ElementorKits Template', 'eduma' ),
			),
		)
	);

	thim_customizer()->add_field(
		array(
			'id'       => 'thim_desc_archive_course_tpl',
			'type'     => 'tp_notice',
 			'description'=>  __('You can config and builder in <a href="'.admin_url('edit.php?post_type=thim_elementor_kit&thim_elementor_type=archive-course').'" target="_blank"> Thim Elementor</a>','eduma'),
			'section'  => 'course_archive',
			'priority' => 11,
			'active_callback' => array(array(
				'setting'  => 'thim_ekits_archive_course_tpl',
				'operator' => '===',
				'value'    => 'ekits_tpl',
			)),
		)
	);
}
thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_layout',
		'type'     => 'radio-image',
		'label'    => esc_html__( 'Layout', 'eduma' ),
		'tooltip'  => esc_html__( 'Allows you to choose a layout for all courses archive pages.', 'eduma' ),
		'section'  => 'course_archive',
		'priority' => 12,
		'default'  => 'sidebar-right',
		'choices'  => array(
			'sidebar-left'  => THIM_URI . 'images/layout/sidebar-left.jpg',
			'full-content'  => THIM_URI . 'images/layout/body-full.jpg',
			'sidebar-right' => THIM_URI . 'images/layout/sidebar-right.jpg',
		),
		'active_callback' => array($callback),
		'wrapper_attrs' => array(
			'class' => '{default_class} thim-col-3'
		)
	)
);

// Select All Content Page Layout
thim_customizer()->add_field(
	array(
		'type'     => 'radio-image',
		'id'       => 'thim_style_content_course',
		'label'    => esc_html__( 'Course Item', 'eduma' ),
		'default'  => 'normal',
		'section'  => 'course_archive',
		'priority' => 13,
		'choices'  => array(
			'normal'         => THIM_URI . 'images/customizer/item-course-1.svg',
			'new-1'          => THIM_URI . 'images/customizer/item-course-2.svg',
			'layout_style_2' => THIM_URI . 'images/customizer/item-course-3.svg',
 		),
		'active_callback' => array($callback),
		'wrapper_attrs' => array(
			'class' => '{default_class} thim-col-3'
		)
	)
);

// Enable or disable breadcrumbs
thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_hide_breadcrumbs',
		'type'     => 'switch',
		'label'    => esc_html__( 'Hide Breadcrumbs?', 'eduma' ),
		'tooltip'  => esc_html__( 'Check this box to hide/show breadcrumbs.', 'eduma' ),
		'section'  => 'course_archive',
		'default'  => false,
		'priority' => 15,
		'choices'  => array(
			true  => esc_html__( 'On', 'eduma' ),
			false => esc_html__( 'Off', 'eduma' ),
		),
		'active_callback' => array($callback),
	)
);

// Enable or disable title
thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_hide_title',
		'type'     => 'switch',
		'label'    => esc_html__( 'Hide Title', 'eduma' ),
		'tooltip'  => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
		'section'  => 'course_archive',
		'default'  => false,
		'priority' => 18,
		'choices'  => array(
			true  => esc_html__( 'On', 'eduma' ),
			false => esc_html__( 'Off', 'eduma' ),
		),
		'active_callback' => array($callback),

	)
);

thim_customizer()->add_field(
	array(
		'type'     => 'text',
		'id'       => 'thim_learnpress_cate_sub_title',
		'label'    => esc_html__( 'Sub Heading', 'eduma' ),
		'tooltip'  => esc_html__( 'Allows you can setup sub heading.', 'eduma' ),
		'section'  => 'course_archive',
		'priority' => 20,
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_show_description',
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Description', 'eduma' ),
		'tooltip'  => esc_html__( 'Allows you can show description on archive blog.', 'eduma' ),
		'section'  => 'course_archive',
		'default'  => false,
		'priority' => 25,
		'choices'  => array(
			true  => esc_html__( 'On', 'eduma' ),
			false => esc_html__( 'Off', 'eduma' ),
		),
		'active_callback' => array($callback),
	)
);

// Enable or disable Popular Courses
thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_show_popular',
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Popular Courses', 'eduma' ),
		'tooltip'  => esc_html__( 'Check this box to hide/show popular Courses.', 'eduma' ),
		'section'  => 'course_archive',
		'default'  => false,
		'priority' => 26,
		'choices'  => array(
			true  => esc_html__( 'On', 'eduma' ),
			false => esc_html__( 'Off', 'eduma' ),
		),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_excerpt_length',
		'type'     => 'number',
		'label'    => esc_html__( 'Excerpt Length', 'eduma' ),
		'tooltip'  => esc_html__( 'Course item description length (number of words)', 'eduma' ),
		'section'  => 'course_archive',
		'default'  => 25,
		'priority' => 27,
		'choices'  => array(
			'min' => 0
		),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'type'     => 'select',
		'id'       => 'thim_learnpress_cate_layout_grid',
		'label'    => esc_html__( 'Layout Grid/List Courses', 'eduma' ),
		'tooltip'  => esc_html__( 'Choose the layout of grid/list courses.', 'eduma' ),
		'default'  => '',
		'priority' => 28,
		'multiple' => 0,
		'section'  => 'course_archive',
		'choices'  => array(
			'list_courses' => esc_html__( 'Layout List', 'eduma' ),
			'grid_courses' => esc_html__( 'Layout Grid', 'eduma' ),
		),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'type'     => 'select',
		'id'       => 'thim_learnpress_cate_grid_column',
		'label'    => esc_html__( 'Grid Columns', 'eduma' ),
		'tooltip'  => esc_html__( 'Choose the number of columns.', 'eduma' ),
		'default'  => '3',
		'priority' => 29,
		'multiple' => 0,
		'section'  => 'course_archive',
		'choices'  => array(
			'2' => esc_html__( '2', 'eduma' ),
			'3' => esc_html__( '3', 'eduma' ),
			'4' => esc_html__( '4', 'eduma' ),
		),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'type'      => 'image',
		'id'        => 'thim_learnpress_cate_top_image',
		'label'     => esc_html__( 'Top Image', 'eduma' ),
		'priority'  => 30,
		'transport' => 'postMessage',
		'section'   => 'course_archive',
		'default'   => THIM_URI . "images/bg-page.jpg",
		'active_callback' => array($callback),
	)
);

// Page Title Background Color
thim_customizer()->add_field(
	array(
		'id'        => 'thim_learnpress_cate_bg_color',
		'type'      => 'color',
		'label'     => esc_html__( 'Background Color', 'eduma' ),
		'tooltip'   => esc_html__( 'If you do not use background image, then can use background color for page title on heading top. ', 'eduma' ),
		'section'   => 'course_archive',
		'default'   => 'rgba(0,0,0,0.5)',
		'priority'  => 35,
		'choices'   => array( 'alpha' => true ),
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main>.overlay-top-header',
				'property' => 'background',
			)
		),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'id'        => 'thim_learnpress_cate_title_color',
		'type'      => 'color',
		'label'     => esc_html__( 'Title Color', 'eduma' ),
		'tooltip'   => esc_html__( 'Allows you can select a color make text color for title.', 'eduma' ),
		'section'   => 'course_archive',
		'default'   => '#ffffff',
		'priority'  => 40,
		'choices'   => array( 'alpha' => true ),
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main h1, .top_site_main h2',
				'property' => 'color',
			)
			),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'id'        => 'thim_learnpress_cate_sub_title_color',
		'type'      => 'color',
		'label'     => esc_html__( 'Sub Title Color', 'eduma' ),
		'tooltip'   => esc_html__( 'Allows you can select a color make sub title color page title.', 'eduma' ),
		'section'   => 'course_archive',
		'default'   => '#999',
		'priority'  => 45,
		'choices'   => array( 'alpha' => true ),
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main .banner-description',
				'property' => 'color',
			)
			),
		'active_callback' => array($callback),
	)
);

thim_customizer()->add_field(
	array(
		'id'       => 'thim_display_course_filter',
		'type'     => 'switch',
		'label'    => esc_html__( 'Display Courses Filter?', 'eduma' ),
		'tooltip'  => '',
		'section'  => 'course_archive',
		'default'  => false,
		'priority' => 50,
		'active_callback' => array($callback),
		'choices'  => array(
			true  => esc_html__( 'Show', 'eduma' ),
			false => esc_html__( 'Hide', 'eduma' ),
		),
	)
);

thim_customizer()->add_field(
	array(
		'id'              => 'thim_filter_by_cate',
		'type'            => 'toggle',
		'label'           => esc_html__( 'Filter by Categories?', 'eduma' ),
		'tooltip'         => '',
		'section'         => 'course_archive',
		'default'         => 0,
		'priority'        => 55,
		'active_callback' => array(
			array(
				'setting'  => 'thim_display_course_filter',
				'operator' => '==',
				'value'    => true,
			),
			$callback
		),
	)
);

thim_customizer()->add_field(
	array(
		'id'              => 'thim_filter_by_instructor',
		'type'            => 'toggle',
		'label'           => esc_html__( 'Filter by Instructors?', 'eduma' ),
		'tooltip'         => '',
		'section'         => 'course_archive',
		'default'         => 0,
		'priority'        => 60,
		'active_callback' => array(
			array(
				'setting'  => 'thim_display_course_filter',
				'operator' => '==',
				'value'    => true,
			),
			$callback
		),
	)
);

thim_customizer()->add_field(
	array(
		'id'              => 'thim_filter_by_price',
		'type'            => 'toggle',
		'label'           => esc_html__( 'Filter by Price?', 'eduma' ),
		'tooltip'         => '',
		'section'         => 'course_archive',
		'default'         => 0,
		'priority'        => 65,
		'active_callback' => array(

			array(
				'setting'  => 'thim_display_course_filter',
				'operator' => '==',
				'value'    => true,
			),
			array(
				'setting'  => 'thim_single_course_offline',
				'operator' => '==',
				'value'    => false,
			),
			$callback
		),
	)
);

thim_customizer()->add_field(
	array(
		'id'       => 'thim_display_course_sort',
		'type'     => 'switch',
		'label'    => esc_html__( 'Display Courses Sort?', 'eduma' ),
		'tooltip'  => '',
		'section'  => 'course_archive',
		'default'  => true,
		'priority' => 70,
		'active_callback' => array($callback),
		'choices'  => array(
			true  => esc_html__( 'Show', 'eduma' ),
			false => esc_html__( 'Hide', 'eduma' ),
		),
	)
);
