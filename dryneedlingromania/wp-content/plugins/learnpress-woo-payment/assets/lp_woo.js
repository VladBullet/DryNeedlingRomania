/**
 * Js handle add to cart
 *
 * @version 4.0.1
 * @since 3.0.0
 */
( function( $ ) {
	let $elThimLoginPopup;

	$.fn._add_course_to_cart = function() {
		$( document ).on( 'submit', 'form[name=form-add-course-to-cart]',
			function( e ) {
				e.preventDefault();
				const selfForm = $( this );

				/**
				 * For theme Eduma
				 * When user not login, click add-to-cart will show popup login
				 * Set params submit course
				 */
				if ( $elThimLoginPopup.length && 'yes' !== localize_lp_woo_js.woo_enable_signup_and_login_from_checkout &&
					'yes' !== localize_lp_woo_js.woocommerce_enable_guest_checkout ) {
					if ( $( 'body:not(".logged-in")' ) ) {
						$elThimLoginPopup.trigger( 'click' );

						// Add param add course to cart to login form
						const $popupUpForm = $( 'form[name=loginpopopform]' );

						if ( ! $popupUpForm.find( '.params-purchase-code' ).length ) {
							const course_id = selfForm.find( 'input[name=course-id]' ).val();

							$popupUpForm.append( '<p class="params-purchase-code"></p>' );
							const $params_purchase_course = $popupUpForm.find(
								'.params-purchase-code'
							);
							$params_purchase_course.append(
								'<input type="hidden" name="add-to-cart" value="' + course_id +
								'" />'
							);
							$params_purchase_course.append(
								'<input type="hidden" name="purchase-course" value="' +
								course_id + '" />'
							);
						}

						return false;
					}
				}

				const el_btn_add_course_to_cart_woo = selfForm.find(
					'.btn-add-course-to-cart'
				);

				let data = $( this ).serialize();
				data += '&action=lpWooAddCourseToCart';

				$.ajax(
					{
						url: localize_lp_woo_js.url_ajax,
						data,
						method: 'post',
						dataType: 'json',
						success( rs ) {
							if ( rs.code === 1 ) {
								if ( undefined !== rs.redirect_to && rs.redirect_to !== '' ) {
									window.location = rs.redirect_to;
								} else {
									$( '.wrap-btn-add-course-to-cart' ).each( function( e ) {
										const el = $( this );
										const course_id = el.find( '[name=course-id]' ).val();
										const course_id_added_to_cart = selfForm.find( '[name=course-id]' ).val();

										if ( course_id === course_id_added_to_cart ) {
											el.append( rs.button_view_cart );

											// Remove button 'add to cart' of course has added
											const $elFormLlpWooAddCourseToCarts = el.find( 'form[name=form-add-course-to-cart]' );
											$elFormLlpWooAddCourseToCarts.remove();
											//
										}
									} );

									$( 'div.widget_shopping_cart_content' ).html( rs.widget_shopping_cart_content );
									$( '.minicart_hover .items-number' ).html( rs.count_items );
								}
							} else {
								alert( rs.message );
							}
						},
						beforeSend() {
							el_btn_add_course_to_cart_woo.append(
								'<span class="fa fa-spinner"></span>'
							);
						},
						complete() {
							el_btn_add_course_to_cart_woo.find( 'span' ).removeClass( 'fa fa-spinner' );
						},
						error( e ) {
							console.log( e );
						},
					}
				);
				return false;
			}
		);
	};

	const check_reload_browser = function() {
		window.addEventListener(
			'pageshow',
			function( event ) {
				const hasCache = event.persisted ||
					( typeof window.performance != 'undefined' && String( window.performance.getEntriesByType( 'navigation' )[ 0 ].type ) == 'back_forward' );

				//console.log( hasCache );

				if ( hasCache ) {
					location.reload();
				}
			}
		);
	};

	// Fix event browser back - load page to show 'view cart' button if added to cart
	check_reload_browser();

	$( function() {
		// For theme eduma
		$elThimLoginPopup = $( '.thim-login-popup .login' );

		$.fn._add_course_to_cart();
	} );
}( jQuery ) );
