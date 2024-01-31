<?php

/**
 * Class LP_Woo_Ajax
 *
 * Handle function LP Order, WC Order
 *
 * @since 4.0.8
 * @version 1.0.0
 */
class LP_Woo_Order {
	public $lp_order = false;
	public $wc_order = false;

	public function __construct( int $lp_order_id = 0, int $wc_order_id = 0 ) {
		$this->lp_order = learn_press_get_order( $lp_order_id );
		$this->wc_order = wc_get_order( $wc_order_id );
	}

	/**
	 * Create LP order base on WC order data
	 *
	 * @since 4.0.2
	 * @version 1.0.1
	 */
	public function create_lp_order() {
		try {
			$wc_order = $this->wc_order;
			if ( ! $wc_order ) {
				return;
			}

			$wc_order_id = $wc_order->get_id();
			$user_id     = get_current_user_id();
			$user        = learn_press_get_user( $user_id );

			// Get LP order key related with WC order
			$lp_order_id = get_post_meta( $wc_order_id, '_learn_press_order_id', true );
			if ( $lp_order_id && get_post_type( $lp_order_id ) === LP_ORDER_CPT ) {
				return;
			}

			// Get wc order items
			$wc_items = $wc_order->get_items();
			if ( ! $wc_items ) {
				return;
			}

			// Find LP courses in WC order and preparing to create LP Order
			$lp_order_items = array();
			$order_total    = 0;
			$order_subtotal = 0;
			$opt_buy_course = LP_Gateway_Woo::is_by_courses_via_product();

			/**
			 * @var $item WC_Order_Item_Product
			 */
			foreach ( $wc_items as $item ) {

				if ( $opt_buy_course ) {
					// Get lists course of product
					$product_id = $item['product_id'] ?? 0;
					if ( ! $product_id ) {
						continue;
					}

					$list_course = get_post_meta( $product_id, LP_Woo_Assign_Course_To_Product::$meta_key_lp_woo_courses_assigned, true );

					if ( empty( $list_course ) ) {
						continue;
					}

					foreach ( $list_course as $course_id ) {
						$can_purchase = apply_filters( 'learnpress/wc-order/can-purchase-product', true, $course_id );
						if ( ! $can_purchase ) {
							continue;
						}

						$course = learn_press_get_course( $course_id );
						if ( ! $course || array_key_exists( $course_id, $lp_order_items ) ) {
							continue;
						}

						$order_total    += floatval( $course->get_price() );
						$order_subtotal += floatval( $course->get_price() );

						$lp_order_items[ $course_id ] = array(
							'item_type'      => get_post_type( $course_id ),
							'item_id'        => $course_id,
							'order_subtotal' => $order_subtotal,
							'order_total'    => $order_total,
						);
					}
				} else {
					$item_id   = $item['product_id'] ?? 0;
					$item_type = get_post_type( $item['product_id'] );

					if ( ! in_array( $item_type, learn_press_get_item_types_can_purchase() ) ) {
						continue;
					}

					switch ( $item_type ) {
						case 'product':
							break;
						case LP_COURSE_CPT:
							$order_total    += floatval( $item->get_total() );
							$order_subtotal += floatval( $item->get_subtotal() );
							break;
						default:
							$order_total    = apply_filters( 'learnpress/wc-order/total/item_type_' . $item_type, $order_total, $item );
							$order_subtotal = apply_filters( 'learnpress/wc-order/subtotal/item_type_' . $item_type, $order_subtotal, $item );
							break;
					}
					$lp_order_items[ $item_id ] = array(
						'item_type'      => get_post_type( $item_id ),
						'item_id'        => $item_id,
						'order_subtotal' => $order_subtotal,
						'order_total'    => ! empty( $order_total ) ? $order_total : $order_subtotal,
					);
				}
			}

			// If there is no course in wc order
			if ( empty( $lp_order_items ) ) {
				return;
			}

			// create lp_order
			$order_data = array(
				'post_author' => $user_id,
				'post_parent' => '0',
				'post_type'   => LP_ORDER_CPT,
				'post_status' => '',
				'ping_status' => 'closed',
				'post_title'  => __( 'Order on', 'learnpress-woo-payment' ) . ' ' . current_time( 'l jS F Y h:i:s A' ),
				'meta_input'  => array(
					'_order_currency'       => get_post_meta( $wc_order_id, '_order_currency', true ),
					'_prices_include_tax'   => $wc_order->get_total_tax() > 0 ? 'yes' : 'no',
					'_user_ip_address'      => learn_press_get_ip(),
					'_user_agent'           => $_SERVER['HTTP_USER_AGENT'] ?? '',
					'_user_id'              => get_post_meta( $wc_order_id, '_customer_user', true ),
					'_order_total'          => ! empty( $order_total ) ? $order_total : $order_subtotal,
					'_order_subtotal'       => $order_subtotal,
					'_order_key'            => apply_filters( 'learn_press_generate_order_key', uniqid( 'order' ) ),
					'_payment_method'       => get_post_meta( $wc_order_id, '_payment_method', true ),
					'_payment_method_title' => get_post_meta( $wc_order_id, '_payment_method_title', true ),
					'_created_via'          => 'manual',
					'_woo_order_id'         => $wc_order_id,
					'user_note'             => '',
				),
			);

			$lp_order_id = wp_insert_post( $order_data );

			// Add email guest when enable guest checkout woo
			$is_guest = ! $wc_order->get_user();
			if ( $is_guest ) {
				$email    = $wc_order->get_billing_email();
				$lp_order = learn_press_get_order( $lp_order_id );
				$lp_order->set_checkout_email( $email );
				$lp_order->save();
			}

			update_post_meta( $wc_order_id, '_learn_press_order_id', $lp_order_id );

			if ( $opt_buy_course ) {
				add_post_meta( $lp_order_id, '_lp_create_order_buy_course_via_product', 1 );
			}

			// Add items to lp_order
			if ( LP_Addon_Woo_Payment::check_background_available() ) {
				// Call background
				$this->background_add_item_to_order( $lp_order_id, $lp_order_items, $wc_order );
			} else {
				// Add item to order not in background
				$lp_woo_order = new LP_Woo_Order( $lp_order_id, $wc_order_id );
				$lp_woo_order->add_item_to_order( $lp_order_items );
			}

			do_action( 'learn-press/checkout-order-processed', $lp_order_id, null );
			do_action( 'learn-press/woo-checkout-create-lp-order-processed', $lp_order_id, null );
		} catch ( Throwable $e ) {
			error_log( __FUNCTION__ . ': ' . $e->getMessage() );
		}
	}

	/**
	 * It adds a course to an order
	 *
	 * @param array $lp_order_items
	 *
	 * @return void
	 */
	public function add_item_to_order( array $lp_order_items = [] ) {
		try {
			$lp_order = $this->lp_order;
			$wc_order = $this->wc_order;
			if ( ! $lp_order || ! $wc_order ) {
				return;
			}

			foreach ( $lp_order_items as $course ) {
				$item_id        = $course['item_id'] ?? 0;
				$order_total    = $course['order_total'] ?? 0;
				$order_subtotal = $course['order_subtotal'] ?? 0;

				$item = array(
					'item_id'         => $item_id,
					'order_item_name' => get_the_title( $item_id ),
					'subtotal'        => $order_subtotal,
					'total'           => $order_total,
				);

				$lp_order->add_item( $item );
			}

			$lp_status = 'lp-' . $wc_order->get_status();
			$lp_order->set_status( $lp_status );
			$lp_order->save();
		} catch ( Throwable $e ) {
			error_log( __FUNCTION__ . ': ' . $e->getMessage() );
		}
	}

	/**
	 * Add items to LP Order on background.
	 *
	 * @param int $lp_order_id
	 * @param array $lp_order_items
	 * @param WC_Order|null $wc_order
	 */
	public function background_add_item_to_order( int $lp_order_id = 0, array $lp_order_items = [], WC_Order $wc_order = null ) {
		// Handle background, add items to LP Order
		$params = array(
			'lp_order_id'         => $lp_order_id,
			'lp_order_items'      => $lp_order_items,
			'lp_no_check_referer' => 1,
			'wc_order_id'         => $wc_order->get_id(),
		);

		$bg = LP_Woo_Payment_Background_Process::instance();
		$bg->data( $params )->dispatch();
	}
}
