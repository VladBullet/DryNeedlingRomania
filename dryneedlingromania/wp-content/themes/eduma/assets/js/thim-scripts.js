/*
* Re-structure JS
* */
(function ($) {
	'use strict'

	/*
	* Helper functions
	* */
	function thim_get_url_parameters(sParam) {
		var sPageURL = window.location.search.substring(1)

		var sURLVariables = sPageURL.split('&')
		for (var i = 0; i < sURLVariables.length; i++) {
			var sParameterName = sURLVariables[i].split('=')

			if (sParameterName[0] === sParam) {
				return sParameterName[1]
			}
		}

	}

	var thim_eduma = {
		el_thim_pop_up_login   : null,
		el_loginpopopform      : null,
		el_registerPopupForm   : null,
		el_form_purchase_course: null,
		el_form_enroll_course: null,

		ready: function () {
			this.getElements()
			if (this.el_thim_pop_up_login.length) {
				this.el_loginpopopform = this.el_thim_pop_up_login.find('form[name=loginpopopform]')
				this.el_registerPopupForm = this.el_thim_pop_up_login.find('form[name=registerformpopup]')
				this.login_form_popup()
			}

			this.form_submission_validate()
			this.thim_TopHeader()
			this.ctf7_input_effect()
			this.mobile_menu_toggle()
			this.thim_backgroud_gradient()
			this.thim_single_image_popup()
			this.full_right(),
				this.course_sidebar_right_offset_top();
			this.thim_carousel()


		},

		getElements: function () {
			this.el_thim_pop_up_login = $('#thim-popup-login')
			this.el_form_purchase_course = $('form[name=purchase-course]')
			this.el_form_enroll_course = $('form[name=enroll-course]')
		},

		load: function () {
			this.thim_menu()
			// this.thim_carousel()
			this.thim_contentslider()
			this.counter_box()
			if ($('#contact-form-registration').length) {
				this.thim_course_offline_popup_form_register();
			}
		},

		resize: function () {
			this.full_right()
			this.thim_carousel()
		},

		validate_form: function (form) {
			var valid = true,
				email_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm

			form.find('input.required').each(function () {
				// Check empty value
				if (!$(this).val()) {
					$(this).addClass('invalid')
					valid = false
				}

				// Uncheck
				if ($(this).is(':checkbox') && !$(this).is(':checked')) {
					$(this).addClass('invalid')
					valid = false
				}

				// Check email format
				if ('email' === $(this).attr('type')) {
					if (!email_valid.test($(this).val())) {
						$(this).addClass('invalid')
						valid = false
					}
				}

				// Check captcha
				if ($(this).hasClass('captcha-result')) {
					let captcha_1 = parseInt($(this).data('captcha1')),
						captcha_2 = parseInt($(this).data('captcha2'))

					if ((captcha_1 + captcha_2) !== parseInt($(this).val())) {
						$(this).addClass('invalid').val('')
						valid = false
					}
				}
			})

			// Check repeat password
			if (form.hasClass('auto_login')) {
				let $pw = form.find('input[name=password]'),
					$repeat_pw = form.find('input[name=repeat_password]')

				if ($pw.val() !== $repeat_pw.val()) {
					$pw.addClass('invalid')
					$repeat_pw.addClass('invalid')
					valid = false
				}
			}

			$('form input.required').on('focus', function () {
				$(this).removeClass('invalid')
			})

			return valid
		},

		login_form_popup: function () {
			var teduma = this

			$(document).on('click', '#thim-popup-login .close-popup', function (event) {
				event.preventDefault()
				$('body').removeClass('thim-popup-active')
				teduma.el_thim_pop_up_login.removeClass()

				// Remove param purchase course on login popup
				teduma.el_loginpopopform.find('.params-purchase-code').remove()
				// Remove param enroll course on login popup
				teduma.el_loginpopopform.find('.params-enroll-code').remove()
			})

			$('body .thim-login-popup a.js-show-popup').on('click', function (event) {
				event.preventDefault()

				$('body').addClass('thim-popup-active')
				teduma.el_thim_pop_up_login.addClass('active')

				if ($(this).hasClass('login')) {
					teduma.el_thim_pop_up_login.addClass('sign-in')
				} else {
					teduma.el_thim_pop_up_login.addClass('sign-up')
				}
			})

			//when login in single page event, show login-popup ,remove redirect to page account
			$('body .widget_book-event a.js-show-popup').on('click', function (event) {
				event.preventDefault()
				$('body').addClass('thim-popup-active')
				teduma.el_thim_pop_up_login.addClass('active')
			})

			teduma.el_thim_pop_up_login.find('.link-bottom a').on('click', function (e) {
				e.preventDefault()

				if ($(this).hasClass('login')) {
					teduma.el_thim_pop_up_login.removeClass('sign-up').addClass('sign-in')
				} else {
					teduma.el_thim_pop_up_login.removeClass('sign-in').addClass('sign-up')
				}
			})

			// Show login popup when click to LP buttons
			$('body:not(".logged-in") .enroll-course .button-enroll-course, body:not(".logged-in") form.purchase-course:not(".guest_checkout") .button:not(.button-add-to-cart)').on('click', function (e) {
				e.preventDefault()

				if ($('body').hasClass('thim-popup-feature')) {
					$('.thim-link-login.thim-login-popup .login').trigger('click')

					// Add param purchase course to login and Register form if exists
					teduma.add_params_purchase_course_to_el(teduma.el_loginpopopform)
					teduma.add_params_purchase_course_to_el(teduma.el_registerPopupForm)

				} else {
					window.location.href = $(this).parent().find('input[name=redirect_to]').val()
				}
			})
			$('.learn-press-content-protected-message .lp-link-login').on('click', function (e) {
				e.preventDefault()

				if ($('body').hasClass('thim-popup-feature')) {
					$('.thim-link-login.thim-login-popup .login').trigger('click')
					// Add param purchase course to login and Register form if exists
					teduma.add_params_purchase_course_to_el(teduma.el_loginpopopform)
					teduma.add_params_purchase_course_to_el(teduma.el_registerPopupForm)
				} else {
					window.location.href = $(this).href()
				}
			})

			$(document).on('click', this.el_thim_pop_up_login, function (e) {
				if ($(e.target).attr('id') === 'thim-popup-login') {
					$('body').removeClass('thim-popup-active')
					teduma.el_thim_pop_up_login.removeClass()

					// remove param purchase course on login popup
					teduma.el_loginpopopform.find('.params-purchase-code').remove()
					teduma.el_registerPopupForm.find('.params-purchase-code').remove()
					// remove param enroll course on login popup
					teduma.el_loginpopopform.find('.params-enroll-code').remove()
					teduma.el_registerPopupForm.find('.params-enroll-code').remove()
				}
			})

			this.el_loginpopopform.submit(function (e) {
				if (!thim_eduma.validate_form($(this))) {
					e.preventDefault()
					return false
				}

				var $elem = teduma.el_thim_pop_up_login.find('.thim-login-container')
				$elem.addClass('loading')
			})

			teduma.el_thim_pop_up_login.find('form[name=registerformpopup]').on('submit', function (e) {
				if (!thim_eduma.validate_form($(this))) {
					e.preventDefault()
					return false
				}

				var $elem = teduma.el_thim_pop_up_login.find('.thim-login-container')
				$elem.addClass('loading')
			})
		},

		/**
		 * Add params purchase course to element
		 * @purpose When register, login via buy course will send params purchase to action
		 *
		 * @param el
		 * @since 4.2.6
		 * @author tungnx
		 */
		add_params_purchase_course_to_el: function (el) {
			const teduma = this
			// Purchase course.
			if (teduma.el_form_purchase_course.length) {
				el.append('<p class="params-purchase-code"></p>')

				var el_paramsPurchaseCode = el.find('.params-purchase-code')

				$.each(teduma.el_form_purchase_course.find('input'), function (i) {
					const inputName = $(this).attr('name')
					const inputPurchaseCourse = $(this).clone()

					if ( el_paramsPurchaseCode.find('input[name=' + inputName + ']').length === 0 ) {
						el_paramsPurchaseCode.append(inputPurchaseCourse)
					}
				})
			}

			// Enroll course
			if (teduma.el_form_enroll_course.length) {
				el.append('<p class="params-enroll-code"></p>')
				const el_paramsEnrollCode = el.find('.params-enroll-code')

				$.each(teduma.el_form_enroll_course.find('input'), function (i) {
					const inputName = $(this).attr('name')
					const inputEnrollCourse = $(this).clone()

					if ( el_paramsEnrollCode.find('input[name=' + inputName + ']').length === 0 ) {
						el_paramsEnrollCode.append(inputEnrollCourse)
					}
				})
			}
		},

		form_submission_validate: function () {
			// Form login
			$('.form-submission-login form[name=loginform]').on('submit', function (e) {
				if (!thim_eduma.validate_form($(this))) {
					e.preventDefault()
					return false
				}
			})

			// Form register
			$('.form-submission-register form[name=registerform]').on('submit', function (e) {
				if (!thim_eduma.validate_form($(this))) {
					e.preventDefault()
					return false
				}
			})

			// Form lost password
			$('.form-submission-lost-password form[name=lostpasswordform]').on('submit', function (e) {
				if (!thim_eduma.validate_form($(this))) {
					e.preventDefault()
					return false
				}
			})
		},

		thim_TopHeader: function () {
			var header = $('#masthead'),
				height_sticky_header = header.outerHeight(true),
				content_pusher = $('#wrapper-container .content-pusher'),
				top_site_main = $('#wrapper-container .top_site_main')
			$('body').removeClass('fixloader');
			if (header.hasClass('header_overlay')) { // Header overlay
				top_site_main.css({'padding-top': height_sticky_header + 'px'})
				$(window).resize(function () {
					let height_sticky_header = header.outerHeight(true)
					top_site_main.css({'padding-top': height_sticky_header + 'px'})
				})
			} else if (header.hasClass('sticky-header') & header.hasClass('header_default')) { // Header default
				content_pusher.css({'padding-top': height_sticky_header + 'px'})
				$(window).resize(function () {
					let height_sticky_header = header.outerHeight(true)
					content_pusher.css({'padding-top': height_sticky_header + 'px'})
				})
			}
		},

		ctf7_input_effect: function () {
			let $ctf7_edtech = $('.form_developer_course'),
				$item_input = $ctf7_edtech.find('.field_item input'),
				$submit_wrapper = $ctf7_edtech.find('.submit_row')

			$item_input.focus(function () {
				$(this).parent().addClass('focusing')
			}).blur(function () {
				$(this).parent().removeClass('focusing')
			})

			$submit_wrapper.on('click', function () {
				$(this).closest('form').submit()
			})
		},

		mobile_menu_toggle: function () {
			$(document).on('click', '.menu-mobile-effect', function (e) {
				e.stopPropagation()
				$('body').toggleClass('mobile-menu-open')
			})

			$(document).on('click', '.wrapper-container', function (e) {
				$('body').removeClass('mobile-menu-open')
			})

			$(document).on('click', '.mobile-menu-inner', function (e) {
				e.stopPropagation()
			})
		},

		thim_menu: function () {

			//Add class for masthead
			var $header = $('#masthead.sticky-header'),
				off_Top = ($('.content-pusher').length > 0) ? $('.content-pusher').offset().top : 0,
				menuH = $header.outerHeight(),
				latestScroll = 0
			var $imgLogo = $('.site-header .thim-logo img'),
				srcLogo = $($imgLogo).attr('src'),
				dataRetina = $($imgLogo).data('retina'),
				dataSticky = $($imgLogo).data('sticky'),
				dataMobile = $($imgLogo).data('mobile'),
				dataStickyMobile = $($imgLogo).data('sticky_mobile');
			if ($(window).scrollTop() > 2) {
				$header.removeClass('affix-top').addClass('affix')
			}
			if ($(window).outerWidth() < 769) {
				if (dataMobile != null) {
					$($imgLogo).attr('src', dataMobile);
				}
			} else {
				if (window.devicePixelRatio > 1 && dataRetina != null) {
					$($imgLogo).attr('src', dataRetina);
				}
			}

			$(window).scroll(function () {
				var current = $(this).scrollTop()
				if (current > 2) {
					$header.removeClass('affix-top').addClass('affix');
					if ($(window).outerWidth() < 769) {
						if (dataStickyMobile != null) {
							$($imgLogo).attr('src', dataStickyMobile);
						} else {
							if (dataSticky != null) {
								$($imgLogo).attr('src', dataSticky);
							}
						}
					} else {
						if (dataSticky != null) {
							$($imgLogo).attr('src', dataSticky);
						}
					}
				} else {
					$header.removeClass('affix').addClass('affix-top');
					if ($(window).outerWidth() < 769) {
						if (dataMobile != null) {
							$($imgLogo).attr('src', dataMobile);
						} else if (srcLogo != null) {
							$($imgLogo).attr('src', srcLogo);
						}
					} else {
						if (window.devicePixelRatio > 1 && dataRetina != null) {
							$($imgLogo).attr('src', dataRetina);
						} else if (srcLogo != null) {
							$($imgLogo).attr('src', srcLogo);
						}
					}
				}

				if (current > latestScroll && current > menuH + off_Top) {
					if (!$header.hasClass('menu-hidden')) {
						$header.addClass('menu-hidden')
					}
				} else {
					if ($header.hasClass('menu-hidden')) {
						$header.removeClass('menu-hidden')
					}
				}

				latestScroll = current
			})


			//Submenu position
			$('.wrapper-container:not(.mobile-menu-open) .site-header .navbar-nav > .menu-item').each(function () {
				if ($('>.sub-menu', this).length <= 0) {
					return
				}

				let elm = $('>.sub-menu', this),
					off = elm.offset(),
					left = off.left,
					width = elm.width()

				let navW = $('.thim-nav-wrapper').width(),
					isEntirelyVisible = (left + width <= navW)

				if (!isEntirelyVisible) {
					elm.addClass('dropdown-menu-right')
				} else {
					let subMenu2 = elm.find('>.menu-item>.sub-menu')

					if (subMenu2.length <= 0) {
						return
					}

					let off = subMenu2.offset(),
						left = off.left,
						width = subMenu2.width()

					let isEntirelyVisible = (left + width <= navW)

					if (!isEntirelyVisible) {
						elm.addClass('dropdown-left-side')
					}
				}
			})

			let $headerLayout = $('header#masthead')
			let magicLine = function () {
				if ($(window).width() > 768) {
					//Magic Line
					var menu_active = $(
						'#masthead .navbar-nav>li.menu-item.current-menu-item,#masthead .navbar-nav>li.menu-item.current-menu-parent, #masthead .navbar-nav>li.menu-item.current-menu-ancestor')
					if (menu_active.length > 0) {
						menu_active.before('<span id="magic-line"></span>')
						var menu_active_child = menu_active.find(
								'>a,>span.disable_link,>span.tc-menu-inner'),
							menu_left = menu_active.position().left,
							menu_child_left = parseInt(menu_active_child.css('padding-left')),
							magic = $('#magic-line')

						magic.width(menu_active_child.width()).css('left', Math.round(menu_child_left + menu_left)).data('magic-width', magic.width()).data('magic-left', magic.position().left)

					} else {
						var first_menu = $(
							'#masthead .navbar-nav>li.menu-item:first-child')
						first_menu.before('<span id="magic-line"></span>')
						var magic = $('#magic-line')
						magic.data('magic-width', 0)
					}

					var nav_H = parseInt($('.site-header .navigation').outerHeight())
					magic.css('bottom', nav_H - (nav_H - 90) / 2 - 64)
					if ($headerLayout.hasClass('item_menu_active_top')) {
						magic.css('bottom', nav_H - 2)
					}
					$('#masthead .navbar-nav>li.menu-item').on({
						'mouseenter': function () {
							var elem = $(this).find('>a,>span.disable_link,>span.tc-menu-inner'),
								new_width = elem.width(),
								parent_left = elem.parent().position().left,
								left = parseInt(elem.css('padding-left'))
							if (!magic.data('magic-left')) {
								magic.css('left', Math.round(parent_left + left))
								magic.data('magic-left', 'auto')
							}
							magic.stop().animate({
								left : Math.round(parent_left + left),
								width: new_width,
							})
						},
						'mouseleave': function () {
							magic.stop().animate({
								left : magic.data('magic-left'),
								width: magic.data('magic-width'),
							})
						},
					})
				}
			}

			if (!$headerLayout.hasClass('noline_menu_active')) {
				magicLine()
			}

			var subMenuPosition = function (menuItem) {
				var $menuItem = menuItem,
					$container = $menuItem.closest('.container, .header_full'),
					$subMenu = $menuItem.find('>.sub-menu'),
					$menuItemWidth = $menuItem.width(),
					$containerWidth = $container.width(),
					$subMenuWidth = $subMenu.width(),
					$subMenuDistance = $subMenuWidth / 2,
					paddingContainer = 15

			}
		},

		thim_carousel: function () {
			if (jQuery().owlCarousel) {
				let is_rtl = $('body').hasClass('rtl') ? true : false ;
				$('.thim-gallery-images').owlCarousel({
					rtl: is_rtl,
					autoplay   : false,
					singleItem : true,
					stopOnHover: true,
					autoHeight : false,
					loop: true,
					loadedClass: 'owl-loaded owl-carousel',
				})

				$('.thim-carousel-wrapper').each(function () {

					var item_visible = $(this).data('visible') ? parseInt(
							$(this).data('visible')) : 4,
						item_desktopsmall = $(this).data('desktopsmall') ? parseInt(
							$(this).data('desktopsmall')) : item_visible,
						itemsTablet = $(this).data('itemtablet') ? parseInt(
							$(this).data('itemtablet')) : 2,
						itemsMobile = $(this).data('itemmobile') ? parseInt(
							$(this).data('itemmobile')) : 1,
						pagination = !!$(this).data('pagination'),
						navigation = !!$(this).data('navigation'),
						autoplay = $(this).data('autoplay') ? parseInt(
							$(this).data('autoplay')) : false,
						navigation_text = ($(this).data('navigation-text') &&
							$(this).data('navigation-text') === '2') ? [
							'<i class=\'fa fa-long-arrow-left \'></i>',
							'<i class=\'fa fa-long-arrow-right \'></i>',
						] : [
							'<i class=\'fa fa-chevron-left \'></i>',
							'<i class=\'fa fa-chevron-right \'></i>',
						]
					$(this).owlCarousel({
						items            : item_visible,
						// itemsDesktop     : [1200, item_visible],
						// itemsDesktopSmall: [1024, item_desktopsmall],
						// itemsTablet      : [768, itemsTablet],
						// itemsMobile      : [480, itemsMobile],
						nav       : navigation,
						dots       : pagination,
						loop: ($(this).children().length > item_visible) ? true: false,
						rewind: true,
						rtl: is_rtl,
						// dots       : true,
						loadedClass: 'owl-loaded owl-carousel',
						navContainerClass: 'owl-nav owl-buttons',
						dotsClass :'owl-dots owl-pagination',
						dotClass:'owl-page',
						responsive:{
							0:{
								items:itemsMobile,
								dots: true,
								nav: false
							},
							480:{
								items:itemsTablet
							},
							1024:{
								items:item_desktopsmall
							},
							1200:{
								items:item_visible
							}
						},
						lazyLoad         : true,
						autoplay         : autoplay,
						navText   : navigation_text,
						afterAction      : function () {
							var width_screen = $(window).width()
							var width_container = $('#main-home-content').width()
							var elementInstructorCourses = $('.thim-instructor-courses')
							var button_full_left = $('.thim_full_right.thim-event-layout-6')
							if (button_full_left.length) {
								var full_left = (jQuery(window).width() - button_full_left.width()) / 2;
								button_full_left.find('.owl-controls .owl-buttons').css("margin-left", "-" + full_left + "px")
								button_full_left.find('.owl-controls .owl-buttons').css({
									'margin-left' : '-' + full_left + 'px',
									'padding-left': full_left + 'px',
									'margin-right': full_left + 'px',
								})
							}
							if (elementInstructorCourses.length) {
								if (width_screen > width_container) {
									var margin_left_value = (width_screen - width_container) / 2
									$('.thim-instructor-courses .thim-course-slider-instructor .owl-controls .owl-buttons').css('left', margin_left_value + 'px')
								}
							}
						}
					})
					thim_eduma.addWrapOwlControls($(this));

				})

				$('.thim-course-slider-instructor').each(function () {
					var item_visible = $(this).data('visible') ? parseInt( $(this).data('visible')) : 4,
						item_desktopsmall = $(this).data('desktopsmall') ? parseInt(
							$(this).data('desktopsmall')) : item_visible,
						itemsTablet = $(this).data('itemtablet') ? parseInt(
							$(this).data('itemtablet')) : 2,
						itemsMobile = $(this).data('itemmobile') ? parseInt(
							$(this).data('itemmobile')) : 1,
						pagination = !!$(this).data('pagination'),
						navigation = !!$(this).data('navigation'),
						autoplay = $(this).data('autoplay') ? parseInt(
							$(this).data('autoplay')) : false,
						navigation_text = ($(this).data('navigation-text') &&
							$(this).data('navigation-text') === '2') ? [
							'<i class=\'fa fa-long-arrow-left \'></i>',
							'<i class=\'fa fa-long-arrow-right \'></i>',
						] : [
							'<i class=\'fa fa-chevron-left \'></i>',
							'<i class=\'fa fa-chevron-right \'></i>',
						]

					$(this).owlCarousel({
						items            : item_visible,
						rtl: is_rtl,
						// itemsDesktop     : [1400, item_desktopsmall],
						// itemsDesktopSmall: [1024, itemsTablet],
						// itemsTablet      : [768, itemsTablet],
						// itemsMobile      : [480, itemsMobile],
						responsive:{
							0:{
								items:itemsMobile
							},
							480:{
								items:itemsTablet
							},
							1024:{
								items:itemsTablet
							},
							1400:{
								items:item_desktopsmall
							}
						},
						nav       : navigation,
						dots       : pagination,
						loop: ($(this).children().length > item_visible) ? true: false,
						rewind: true,
						lazyLoad         : true,
						autoplay         : autoplay,
						navText   : navigation_text,
						loadedClass: 'owl-loaded owl-carousel',
						navContainerClass: 'owl-nav owl-buttons',
						dotsClass :'owl-dots owl-pagination',
						dotClass:'owl-page',
						afterAction      : function () {
							var width_screen = $(window).width()
							var width_container = $('#main-home-content').width()
							var elementInstructorCourses = $('.thim-instructor-courses')

							if (elementInstructorCourses.length) {
								if (width_screen > width_container) {
									var margin_left_value = (width_screen - width_container) / 2
									$('.thim-instructor-courses .thim-course-slider-instructor .owl-controls .owl-buttons').css('left', margin_left_value + 'px')
								}
							}
						}
					})
					thim_eduma.addWrapOwlControls($(this));
				})

				$('.thim-carousel-course-categories .thim-course-slider, .thim-carousel-course-categories-tabs .thim-course-slider').each(function () {

					var item_visible = $(this).data('visible') ? parseInt($(this).data('visible')) : 7,
						item_desktop = $(this).data('desktop') ? parseInt($(this).data('desktop')) : item_visible,
						item_desktopsmall = $(this).data('desktopsmall') ? parseInt($(this).data('desktopsmall')) : 6,
						item_tablet = $(this).data('tablet') ? parseInt($(this).data('tablet')) : 4,
						item_mobile = $(this).data('mobile') ? parseInt($(this).data('mobile')) : 2,
						pagination = !!$(this).data('pagination'),
						navigation = !!$(this).data('navigation'),
						autoplay = $(this).data('autoplay') ? parseInt($(this).data('autoplay')) : false
					$(this).owlCarousel({
						items            : item_visible,
						loop: ($(this).children().length > item_visible) ? true: false,
						rewind: true,
						rtl: is_rtl,
						responsive:{

							0:{
								items:item_mobile
							},
							480:{
								items:item_tablet
							},
							1024:{
								items:item_desktopsmall
							},
							1800:{
								items:item_desktop
							}
						},
						nav       : navigation,
						dots       : pagination,
						loadedClass: 'owl-loaded owl-carousel',
						autoplay         : autoplay,
						navContainerClass: 'owl-nav owl-buttons',
						dotsClass :'owl-dots owl-pagination',
						dotClass:'owl-page',
						navText   : [
							'<i class=\'fa fa-chevron-left \'></i>',
							'<i class=\'fa fa-chevron-right \'></i>',
						],
					})
					thim_eduma.addWrapOwlControls($(this));
				})
			}
		},

		thim_contentslider: function () {
			$('.thim-testimonial-slider').each(function () {
				var elem = $(this),
					item_visible = parseInt(elem.data('visible')),
					item_time = parseInt(elem.data('time')),
					autoplay = elem.data('auto') ? true : false,
					item_ratio = elem.data('ratio') ? elem.data('ratio') : 1.18,
					item_padding = elem.data('padding') ? elem.data('padding') : 15,
					item_activepadding = elem.data('activepadding') ? elem.data(
						'activepadding') : 0,
					item_width = elem.data('width') ? elem.data('width') : 100,
					mousewheel = !!elem.data('mousewheel')
				if (jQuery().thimContentSlider) {
					var testimonial_slider = $(this).thimContentSlider({
						items            : elem,
						itemsVisible     : item_visible,
						mouseWheel       : mousewheel,
						autoPlay         : autoplay,
						pauseTime        : item_time,
						itemMaxWidth     : item_width,
						itemMinWidth     : item_width,
						activeItemRatio  : item_ratio,
						activeItemPadding: item_activepadding,
						itemPadding      : item_padding,
					})
				}

			})
		},

		counter_box: function () {
			if (jQuery().waypoint) {
				jQuery('.counter-box').waypoint(function () {
					jQuery(this).find('.display-percentage').each(function () {
						var percentage = jQuery(this).data('percentage')
						jQuery(this).countTo({
							from           : 0,
							to             : percentage,
							refreshInterval: 40,
							speed          : 2000,
						})
					})
				}, {
					triggerOnce: true,
					offset     : '80%',
				})
			}
		},

		thim_backgroud_gradient: function () {
			var background_gradient = jQuery('.thim_overlay_gradient')
			var background_gradient_2 = jQuery('.thim_overlay_gradient_2')
			if (background_gradient.length) {
				$('.thim_overlay_gradient rs-sbg-px > rs-sbg-wrap > rs-sbg').addClass('thim-overlayed')
			}

			if (background_gradient_2.length) {
				$('.thim_overlay_gradient_2 rs-sbg-px > rs-sbg-wrap > rs-sbg').addClass('thim-overlayed')
			}
		},

		thim_single_image_popup: function () {
			if (jQuery().magnificPopup) {
				$('.thim-single-image-popup').magnificPopup({
					type: 'image',
					zoom: {
						enabled : true,
						duration: 300,
						easing  : 'ease-in-out',
					}
				})
			}
		},

		full_right: function () {
			$('.thim_full_right').each(function () {
				var full_right = (jQuery(window).width() - jQuery(this).width()) / 2;
				jQuery(this).children().css("margin-right", "-" + full_right + "px");
			});
			$('.thim_full_left').each(function () {
				var full_left = (jQuery(window).width() - jQuery(this).width()) / 2;
				jQuery(this).children().css("margin-left", "-" + full_left + "px");
			});
			$('.thim_coundown_full_left').each(function () {
				var full_left = (jQuery(window).width() - jQuery(this).width()) / 2;
				var number =   full_left + 'px';
				jQuery(this).find('.thim-widget-countdown-box').parent().css({"margin-left": '-' + number, "padding-left": number});
			});
		},
		thim_course_offline_popup_form_register : function() {
			if ($('#contact-form-registration >.wpcf7').length) {
				var el = $('#contact-form-registration >.wpcf7');
				el.append('<a href="#" class="thim-close fa fa-times"></a>');
			}
			$(document).on('click', '#contact-form-registration .wpcf7-form-control.wpcf7-submit', function () {
				$(document).on('mailsent.wpcf7', function (event) {
					setTimeout(function(){
						$('body').removeClass('thim-contact-popup-active');
						$('#contact-form-registration').removeClass('active');
					}, 3000);
				});
			});
			$(document).on('click', '.course-payment .thim-enroll-course-button', function (e) {
				e.preventDefault();
				$('body').addClass('thim-contact-popup-active');
				$('#contact-form-registration').addClass('active');
			});

			$(document).on('click', '#contact-form-registration', function (e) {
				if ($(e.target).attr('id') == 'contact-form-registration') {
					$('body').removeClass('thim-contact-popup-active');
					$('#contact-form-registration').removeClass('active');
				}
			});

			$(document).on('click', '#contact-form-registration .thim-close', function (e) {
				e.preventDefault();
				$('body').removeClass('thim-contact-popup-active');
				$('#contact-form-registration').removeClass('active');
			});
		},
		course_sidebar_right_offset_top : function(){
			var elementInfoTop = $('.course-info-top');
			if(elementInfoTop.length){
				var InfoTopHeight = elementInfoTop.innerHeight(),
					elementInfoRight = $('.thim-style-content-layout_style_3 .sticky-sidebar');
				elementInfoRight.css('margin-top', '-' + ( InfoTopHeight - 20 ) + 'px' );
			}
		},
		addWrapOwlControls: function ( el ) {
			const elOwlControls = el.find('.owl-controls');
			if ( ! elOwlControls.length ) {
				el.find('.owl-nav, .owl-dots').wrapAll("<div class='owl-controls'></div>");
			}
		}
	}

	$(document).ready(function () {
		thim_eduma.ready();

		$(window).resize(function () {
			thim_eduma.resize()
		})

	})

	$(window).on('load', function () {
		thim_eduma.load();
	})

	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/thim-carousel-post.default',
			thim_eduma.thim_carousel)
		elementorFrontend.hooks.addAction('frontend/element_ready/thim-twitter.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-courses.default',
			thim_eduma.thim_carousel);

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-list-event.default',
			thim_eduma.thim_carousel);

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-course-categories.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-our-team.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-gallery-images.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-list-instructors.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-testimonials.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-courses-collection.default',
			thim_eduma.thim_carousel)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-testimonials.default',
			thim_eduma.thim_contentslider)

		elementorFrontend.hooks.addAction('frontend/element_ready/thim-counters-box.default',
			thim_eduma.counter_box)

		elementorFrontend.hooks.addAction('frontend/element_ready/global', function($scope) {
			var $carousel = $scope.find('.owl-carousel')
			if ($carousel.length) {
				var carousel = $carousel.data('owlCarousel')
				carousel && carousel.reload()
			}
		})

	})
})(jQuery)
