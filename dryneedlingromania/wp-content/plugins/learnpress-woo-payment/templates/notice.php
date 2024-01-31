<?php
/**
 * Template for displaying notice when buy course via product
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 1.0.0
 * @editor tungnx
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course_id ) ) {
	return;
}
global $wpdb;
$filter                   = new LP_Course_Filter();
$filter->post_type        = 'product';
$filter->collection_alias = 'p';
$filter->join[]           = 'INNER JOIN ' . $wpdb->postmeta . ' AS pm ON pm.post_id = p.ID';
$filter->where[]          = 'AND pm.meta_key = "_lp_woo_courses_assigned" AND pm.meta_value LIKE ' . "'%" . '"' . $course_id . '"' . "%'";

$filter   = apply_filters( 'lp/woo-payment/notice-sidebar/get-products', $filter );
$products = LP_Course_DB::getInstance()->get_courses( $filter );
wp_enqueue_style( 'lp-woo-css' );
?>

<div class="course-via-product">
	<div class="learn-press-message error">
		<?php if ( empty( $products ) ) : ?>
			<p>
				<?php
				if ( current_user_can( 'administrator' ) || current_user_can( LP_TEACHER_ROLE ) ) {
					_e( 'Purchase is only available if the course is already assigned to a product!', 'learnpress-woo-payment' );
				} else {
					_e( 'You couldn\'t purchase this course because it hasn\'t been assigned to any product yet!', 'learnpress-woo-payment' );
				}
				?>
			</p>
		<?php else : ?>
			<p>
				<?php
				_e( 'You need to purchase courses from products list to begin learning.', 'learnpress-woo-payment' );
				?>
			</p>
			<h6><?php _e( '--List products: ', 'learnpress-woo-payment' ); ?></h6>
			<ul>
				<?php foreach ( $products as $product ) : ?>
					<li>
						<a href="<?php echo get_permalink( $product->ID ); ?>"><?php echo get_the_title( $product->ID ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
