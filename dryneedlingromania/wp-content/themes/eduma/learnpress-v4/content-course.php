<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$class = 'lpr_course course-grid-' . get_theme_mod( 'thim_learnpress_cate_grid_column', '3' );

?>

<div id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>

	<?php
	// @deprecated
	do_action( 'learn_press_before_courses_loop_item' );
	?>

	<div class="course-item">

		<?php
		// @since 4.0.0
		//do_action( 'learn-press/before-courses-loop-item' );
		?>

		<?php
		// @thim
		do_action( 'thim_courses_loop_item_thumb' );
		?>

		<div class="thim-course-content">
			<?php
			/**
			 * @hooked thim_learnpress_loop_item_title - 10
			 * @hooked learn_press_courses_loop_item_instructor - 5
			 */
			do_action( 'learnpress_loop_item_title' );

			do_action( 'learnpress_loop_item_desc' );

			do_action( 'learnpress_loop_item_course_meta' );

			?>
		</div>

		<?php
		// @since 4.0.0
		//do_action( 'learn-press/after-courses-loop-item' );
		?>

	</div>

	<?php
	// @deprecated
	do_action( 'learn_press_after_courses_loop_item' );
	?>

</div>
