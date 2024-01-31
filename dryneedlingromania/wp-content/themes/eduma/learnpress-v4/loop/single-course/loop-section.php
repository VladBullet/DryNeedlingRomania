<?php
/**
 * Template for displaying loop course of section v2.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/loop-section.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.3
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $args ) ) {
	return;
}

/**
 * @var LP_Course_Section $section
 */
if ( isset( $args['section'] ) && isset( $args['course_id'] ) ) {
	$section   = $args['section'];
	$course_id = $args['course_id'];
} else {
	return;
}

$filters             = new LP_Section_Items_Filter();
$filters->section_id = $section['section_id'];
$section_items       = LP_Section_DB::getInstance()->get_section_items_by_section_id( $filters );
$count_items         = $section_items['total'];
$user                = learn_press_get_current_user();
?>

<li class="section" id="section-<?php echo $section['section_id']; ?>"
									data-id="<?php echo $section['section_id']; ?>"
									data-section-id="<?php echo $section['section_id']; ?>">
	<?php do_action( 'learn-press/before-section-summary', $section, $course_id ); ?>
	<div class="section-header">
		<div class="section-left">
			<span class="section-toggle collapse"></span>
			<?php
			echo '<h3 class="section-title">';
			echo ! ( $section['section_name'] ) ? _x( 'Untitled', 'template title empty', 'eduma' ) : $section['section_name'];
			echo '</h3>';
			?>
			<span class="meta">
			<?php
			if ( $user->has_enrolled_or_finished( $course_id ) ) {
				$user_course = $user->get_course_data( $course_id );
				?>
				<span
					class="step"><?php printf( '%d/%d', $user_course->get_completed_items( '', false, $section['section_id'] ), $count_items ); ?></span>
			<?php } else { ?>
				<span class="step"><?php printf( '%d', $count_items ); ?></span>
			<?php } ?>
				</span>
		</div>
		<?php if ( $section['section_description'] ) : ?>
			<p class="section-desc"><?php echo $section['section_description']; ?></p>
		<?php endif; ?>
	</div>
	<?php do_action( 'learn-press/before-section-content', $section, $course_id ); ?>

	<?php
	try {
		$controller = new LP_REST_Lazy_Load_Controller();
		$request    = new WP_REST_Request();
		$request->set_param( 'sectionId', $section['section_id'] );
		$response              = $controller->course_curriculum_items( $request );
		$object_data           = $response->get_data();
		$section_items_content = '';
		$section_items_pages   = 0;

		if ( defined( 'LEARNPRESS_VERSION' ) && version_compare( LEARNPRESS_VERSION, '4.1.6', '>' ) ) {
			$section_items_content = $object_data->data->content;
			$section_items_pages   = $object_data->data->pages;
		} else {
			$section_items_content = $object_data->data;
			$section_items_pages   = $object_data->pages;
		}
	} catch ( Throwable $e ) {
		error_log( $e );
		return;
	}
	?>

	<ul class="section-content">
		<?php echo wp_kses_post( $section_items_content ); ?>
	</ul>
	<?php if ( ! empty( $section_items_pages ) && $section_items_pages > 1 ) : ?>
		<div class="section-item__loadmore" data-page="1">
			<span><?php esc_html_e( 'Show more items', 'learnpress' ); ?> <i class="fa fa-plus"></i></span>
		</div>
	<?php endif; ?>

	<?php do_action( 'learn-press/after-section-summary', $section, $course_id ); ?>
</li>
