(function($) {



	//
	// SECTION SLIDER
	//
	var MAEAdvancedSwiperHandler = function($scope, $) {


		var el_id = $scope.attr('data-id');


		// var el_data_settings = JSON.parse($scope.attr('data-settings'));
		// var target_sanitalized = el_data_settings.mae_as_target.replace(/[^a-zA-Z0-9]/g,'');

		// console.log(window['as_' + el_id + target_sanitalized + '_settings']);

		// if(typeof window['as_' + el_id + target_sanitalized + '_settings'] === 'undefined') return false;
		if (typeof window['as_' + el_id + '_settings'] === 'undefined') return false;


		// var received_settings = JSON.parse(window['as_' + el_id + target_sanitalized + '_settings']); 
		var received_settings = JSON.parse(window['as_' + el_id + '_settings']);



		if (received_settings.element == '' || received_settings.element == '#' || received_settings.element == '.') return false;

		// console.log(received_settings);

		var slider_container = received_settings.element;


		// create or destroy slider
		if (received_settings.enabled == '1') { // create slider



			// SETTINGS
			var settings = {
				speed: received_settings.speed,
				effect: received_settings.effect,
				centeredSlides: received_settings.centeredSlides,
				centeredSlidesBounds: received_settings.centeredSlidesBounds
			}

			// autoplay
			if (received_settings.autoplay == '1') {
				settings.autoplay = {
					delay: received_settings.autoplay_delay,
					disableOnInteraction: true,
				}
			}
			// loop
			if (received_settings.loop == '1') {
				settings.loop = true;
			}
			// parallax
			if (received_settings.parallax == '1') {
				settings.parallax = true;
			}

			settings.init = false;

			// slider
			if (received_settings.front != '1') { // frontend
				// remove old swiper instance
				removeSwiper(slider_container, received_settings.optimized_dom);
			}




			// HTML
			// copy elementor-container 
			if (received_settings.navigation.enabled ||
				received_settings.pagination.enabled ||
				received_settings.scrollbar.enabled) {
				var container_classes = $(slider_container + ' > .elementor-container').attr('class');
			}
			if (received_settings.optimized_dom) { // elementor DOM optimized 

				// add swiper classes
				$(slider_container + ' > .elementor-container').addClass('swiper-container');
				$(slider_container + ' .swiper-container > .elementor-row').addClass('swiper-wrapper');
				$(slider_container + ' .swiper-container > .elementor-row > .elementor-column').addClass('swiper-slide');

			} else { // elementor DOM legacy mode 

				$(slider_container).addClass('as_legacy_dom');

				// add swiper classes
				$(slider_container + ' > .elementor-container').addClass('swiper-container');
				$(slider_container + ' > .elementor-container').wrapInner('<div class="elementor-row"></div>');
				$(slider_container + ' .swiper-container > .elementor-row').addClass('swiper-wrapper');
				$(slider_container + ' .swiper-container > .elementor-row > .elementor-column').addClass('swiper-slide');

			}


			// NAVIGATION
			if (received_settings.navigation.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_navigation">' + received_settings.navigation.prev_button + received_settings.navigation.next_button + '</div>');

				settings.navigation = {
					nextEl: slider_container + ' .advanced-swiper-button-next',
					prevEl: slider_container + ' .advanced-swiper-button-prev',
				}

			}


			// PAGINATION
			if (received_settings.pagination.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_pagination"><div class="mae_as_bullets"></div></div>');

				settings.pagination = {
					el: slider_container + ' .mae_as_pagination .mae_as_bullets',
					clickable: true,
					type: received_settings.pagination.type,
				}

			}


			// SCROLLBAR
			if (received_settings.scrollbar.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_scrollbar"></div>');

				settings.scrollbar = {
					el: slider_container + ' .mae_as_scrollbar',
					draggable: true,
				}

			}


			// BREAKPOINTS
			if (received_settings.effect == 'slide' || received_settings.effect == 'coverflow') {
				elementorBreakpoints = elementorFrontend.config.breakpoints
				settings.slidesPerView = received_settings.breakpoints.desktop.slidesPerView;
				settings.spaceBetween = received_settings.breakpoints.desktop.spaceBetween;
				settings.handleElementorBreakpoints = true;
				settings.breakpoints = {
					[elementorBreakpoints.lg]: {
						slidesPerView: received_settings.breakpoints.tablet.slidesPerView,
						spaceBetween: received_settings.breakpoints.tablet.spaceBetween
					},
					[elementorBreakpoints.md]: {
						slidesPerView: received_settings.breakpoints.mobile.slidesPerView,
						spaceBetween: received_settings.breakpoints.mobile.spaceBetween
					}
				}
			}


			// COVERFLOW EFFECT
			if (received_settings.effect == 'coverflow') {
				settings.coverflowEffect = {
					slideShadows: received_settings.coverflow.slideShadows,
				};
			}


			// FADE EFFECT
			if (received_settings.effect == 'fade') {
				settings.fadeEffect = {
					crossFade: received_settings.fade.crossfade,
				};
			}

			// INIT IN HIDDEN
			settings.observer = true;
			settings.observeParents = true;

			// custom settings
			if (received_settings.custom) {
				Object.assign(settings, eval('({' + received_settings.custom + '})'));
			}

			// console.log(settings);
			// init new swiper
			var swiper_selector = $(slider_container + ' .swiper-container')[0]; // target only first founded elements to avoid conflicts (elementor sticky header fix)
			//console.log(swiper_selector);
			SectionSwiper = new Swiper(swiper_selector, settings, );



			// setupSliderBreakpoints(received_settings.breakpoints);
			SectionSwiper.on('init', function() {
				$(slider_container).css({
					'opacity': '1',
					'transition': 'opacity 50ms'
				});
			});
			SectionSwiper.init();

			// elementor animation fix
			SectionSwiper.on('transitionStart', function(e) {
				$(slider_container + ' .elementor-invisible').css('visibility', 'visible');
			});

			// stop autoplay on hover
			if (received_settings.pause_on_hover == '1') {
				SectionSwiper.el.addEventListener("mouseenter", function(event) {
					SectionSwiper.autoplay.stop();
				}, false);
				SectionSwiper.el.addEventListener("mouseleave", function(event) {
					SectionSwiper.autoplay.start();
				}, false);
			}
			if (received_settings.autoplay) {
				// autoplay after resize
				$(window).resize(function(e) {
					SectionSwiper.autoplay.start();
					// setupSliderBreakpoints(received_settings.breakpoints);
				});
			}


		} else { // remove slider
			// console.log('remove');
			// console.log(slider_container);
			if (slider_container) {
				removeSwiper(slider_container, received_settings.optimized_dom);
			}

		}



		// function to completely remove swiper slider form section
		function removeSwiper(el, optimized_dom) {

			// console.log('remove');

			// remove arrows container
			$(el + ' .mae_as_navigation').remove();
			$(el + ' .mae_as_pagination').remove();
			$(el + ' .mae_as_scrollbar').remove();


			// check is swiper container present
			var prevSectionSlider = $(el + ' .swiper-container');
			if (prevSectionSlider.length) {

				// get element swiper data
				var swiperInstance = prevSectionSlider[0].swiper;

				// if element has swiper instance
				if (swiperInstance) {

					// destroy swiper instance
					swiperInstance.destroy();

					// remove swiper classes
					// get all elements with `swiper` in class name
					var $el_with_swiper_classes = $(el + " [class*=swiper]");

					// loop through elements to remome `swiper` classes 
					$.each($el_with_swiper_classes, function() {
						var $current_el_with_swiper_class = $(this);
						var current_element_class_list = $(this).attr("class");
						current_element_class_list = current_element_class_list.split(/ +/);
						$.each(current_element_class_list, function(key, value) {
							var current_swiper_class = value;
							if (current_swiper_class.indexOf("swiper") !== -1) {
								$current_el_with_swiper_class.removeClass(current_swiper_class);
							}
						});
					});

					// unwrap if legasy DOM
					if (!optimized_dom) {
						$(el + ' > .elementor-container > .elementor-row').contents().unwrap();
						$(el).removeClass('as_legacy_dom');
					}

				}

			}

		}



	};
	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/mae_advanced_swiper.default', MAEAdvancedSwiperHandler);
	});








	//
	// SECTION SLIDER
	//
	var MAEPostsSwiperHandler = function($scope, $) {

		var el_id = $scope.attr('data-id');


		// var el_data_settings = JSON.parse($scope.attr('data-settings'));
		// var target_sanitalized = el_data_settings.mae_as_target.replace(/[^a-zA-Z0-9]/g,'');

		// console.log(window['as_' + el_id + target_sanitalized + '_settings']);

		// if(typeof window['as_' + el_id + target_sanitalized + '_settings'] === 'undefined') return false;
		if (typeof window['as_' + el_id + '_settings'] === 'undefined') return false;


		// var received_settings = JSON.parse(window['as_' + el_id + target_sanitalized + '_settings']); 
		var received_settings = JSON.parse(window['as_' + el_id + '_settings']);



		if (received_settings.element == '' || received_settings.element == '#' || received_settings.element == '.') return false;

		// console.log(received_settings);

		var slider_container = received_settings.element;


		// create or destroy slider
		if (received_settings.enabled == '1') { // create slider



			// SETTINGS
			var settings = {
				speed: received_settings.speed,
				effect: received_settings.effect,
				centeredSlides: received_settings.centeredSlides,
				centeredSlidesBounds: received_settings.centeredSlidesBounds
			}

			// autoplay
			if (received_settings.autoplay == '1') {
				settings.autoplay = {
					delay: received_settings.autoplay_delay,
					disableOnInteraction: true,
				}
			}
			// loop
			if (received_settings.loop == '1') {
				settings.loop = true;
			}
			// parallax
			if (received_settings.parallax == '1') {
				settings.parallax = true;
			}

			settings.init = false;

			// slider
			if (received_settings.front != '1') { // frontend
				// remove old swiper instance
				removeSwiper(slider_container, received_settings.optimized_dom);
			}




			// HTML
			// copy elementor-container 
			if (received_settings.navigation.enabled ||
				received_settings.pagination.enabled ||
				received_settings.scrollbar.enabled) {
				var container_classes = $(slider_container + ' > .elementor-widget-container').attr('class');
			}


			// add swiper classes
			$(slider_container + ' > .elementor-widget-container').addClass('swiper-container');
			$(slider_container + ' .elementor-widget-container > .elementor-posts-container').addClass('swiper-wrapper');
			$(slider_container + ' .elementor-widget-container > .elementor-posts-container > article').addClass('swiper-slide');




			// NAVIGATION
			if (received_settings.navigation.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_navigation">' + received_settings.navigation.prev_button + received_settings.navigation.next_button + '</div>');

				settings.navigation = {
					nextEl: slider_container + ' .advanced-swiper-button-next',
					prevEl: slider_container + ' .advanced-swiper-button-prev',
				}

			}


			// PAGINATION
			if (received_settings.pagination.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_pagination"><div class="mae_as_bullets"></div></div>');

				settings.pagination = {
					el: slider_container + ' .mae_as_pagination .mae_as_bullets',
					clickable: true,
					type: received_settings.pagination.type,
				}

			}


			// SCROLLBAR
			if (received_settings.scrollbar.enabled) {

				$(slider_container).append('<div class="' + container_classes + ' mae_as_scrollbar"></div>');

				settings.scrollbar = {
					el: slider_container + ' .mae_as_scrollbar',
					draggable: true,
				}

			}


			// BREAKPOINTS
			if (received_settings.effect == 'slide' || received_settings.effect == 'coverflow') {
				elementorBreakpoints = elementorFrontend.config.breakpoints
				settings.slidesPerView = received_settings.breakpoints.desktop.slidesPerView;
				settings.spaceBetween = received_settings.breakpoints.desktop.spaceBetween;
				settings.handleElementorBreakpoints = true;
				settings.breakpoints = {
					[elementorBreakpoints.lg]: {
						slidesPerView: received_settings.breakpoints.tablet.slidesPerView,
						spaceBetween: received_settings.breakpoints.tablet.spaceBetween
					},
					[elementorBreakpoints.md]: {
						slidesPerView: received_settings.breakpoints.mobile.slidesPerView,
						spaceBetween: received_settings.breakpoints.mobile.spaceBetween
					}
				}
			}


			// COVERFLOW EFFECT
			if (received_settings.effect == 'coverflow') {
				settings.coverflowEffect = {
					slideShadows: received_settings.coverflow.slideShadows,
				};
			}


			// FADE EFFECT
			if (received_settings.effect == 'fade') {
				settings.fadeEffect = {
					crossFade: received_settings.fade.crossfade,
				};
			}

			// INIT IN HIDDEN
			settings.observer = true;
			settings.observeParents = true;




			// custom settings
			if (received_settings.custom) {
				Object.assign(settings, eval('({' + received_settings.custom + '})'));
			}



			// console.log(settings);
			// init new swiper
			var swiper_selector = $(slider_container + ' .swiper-container')[0]; // target only first founded elements to avoid conflicts (elementor sticky header fix)
			//console.log(swiper_selector);
			SectionSwiper = new Swiper(swiper_selector, settings, );



			// setupSliderBreakpoints(received_settings.breakpoints);
			SectionSwiper.on('init', function() {
				$(slider_container).css({
					'opacity': '1',
					'transition': 'opacity 50ms'
				});
			});
			SectionSwiper.init();

			// elementor animation fix
			SectionSwiper.on('transitionStart', function(e) {
				$(slider_container + ' .elementor-invisible').css('visibility', 'visible');
			});

			// stop autoplay on hover
			if (received_settings.pause_on_hover == '1') {
				SectionSwiper.el.addEventListener("mouseenter", function(event) {
					SectionSwiper.autoplay.stop();
				}, false);
				SectionSwiper.el.addEventListener("mouseleave", function(event) {
					SectionSwiper.autoplay.start();
				}, false);
			}
			if (received_settings.autoplay) {
				// autoplay after resize
				$(window).resize(function(e) {
					SectionSwiper.autoplay.start();
					// setupSliderBreakpoints(received_settings.breakpoints);
				});
			}


		} else { // remove slider
			// console.log('remove');
			// console.log(slider_container);
			if (slider_container) {
				removeSwiper(slider_container, received_settings.optimized_dom);
			}

		}


		// function to completely remove swiper slider form section
		function removeSwiper(el, optimized_dom) {

			// console.log('remove');

			// remove arrows container
			$(el + ' .mae_as_navigation').remove();
			$(el + ' .mae_as_pagination').remove();
			$(el + ' .mae_as_scrollbar').remove();


			// check is swiper container present
			var prevSectionSlider = $(el + ' .swiper-container');
			if (prevSectionSlider.length) {

				// get element swiper data
				var swiperInstance = prevSectionSlider[0].swiper;

				// if element has swiper instance
				if (swiperInstance) {

					// destroy swiper instance
					swiperInstance.destroy();

					// remove swiper classes
					// get all elements with `swiper` in class name
					var $el_with_swiper_classes = $(el + " [class*=swiper]");

					// loop through elements to remome `swiper` classes 
					$.each($el_with_swiper_classes, function() {
						var $current_el_with_swiper_class = $(this);
						var current_element_class_list = $(this).attr("class");
						current_element_class_list = current_element_class_list.split(/ +/);
						$.each(current_element_class_list, function(key, value) {
							var current_swiper_class = value;
							if (current_swiper_class.indexOf("swiper") !== -1) {
								$current_el_with_swiper_class.removeClass(current_swiper_class);
							}
						});
					});

					// unwrap if legasy DOM
					if (!optimized_dom) {
						$(el + ' > .elementor-container > .elementor-row').contents().unwrap();
						$(el).removeClass('as_legacy_dom');
					}

				}

			}

		}



	};
	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/mae_posts_swiper.default', MAEPostsSwiperHandler);
	});




	class MAETemplatePopup extends elementorModules.frontend.handlers.Base {

		getDefaultSettings() {
			return {
				selectors: {
					toggle: '.mae-toggle-button',
					content: '.mae-toggle-content',
				},
			};
		}

		getDefaultElements() {
			var selectors = this.getSettings('selectors');
			return {
				$toggle: this.$element.find(selectors.toggle).first(),
				$content: this.$element.find(selectors.content).first(),
			};
		}

		bindEvents() {
			this.elements.$toggle.on('click', this.toggleClick.bind(this));
			// this.$element.on('mouseenter', this.mouseIn.bind(this));
			// this.$element.on('mouseleave', this.mouseOut.bind(this));
			$(window).on('resize', this.windowResize.bind(this));
		}

		toggleClick(e) {
			if (this.elements.$toggle.hasClass('active')) {
				this.hideContent();
			} else {
				this.elements.$content.show();
				this.showContent();
			}
		}

		// mouseIn(e) {
		//    this.elements.$content.show();
		//    this.showContent();
		// }

		// mouseOut(e) {
		//    var self = this;
		//    setTimeout(function () {
		//       self.hideContent();
		//    }, 300);
		// }

		documentClick(e) {
			if (!this.$element.is(e.target) && this.$element.has(e.target).length === 0) {
				this.hideContent();
			}
		}

		showContent() {
			this.elements.$toggle.addClass('active');
			this.elements.$content.addClass('active');
			this.setContentWidth();

			$(document).on('click.' + this.getID(), this.documentClick.bind(this));

			this.elements.$content.trigger('mae_content_visible'); // add event for elements inside
		}

		windowResize() {
			if (this.elements.$content.hasClass('active')) {
				this.setContentWidth();
				this.check_bounding();
			}
		}

		hideContent() {
			this.elements.$toggle.removeClass('active');
			this.elements.$content.removeClass('active');
			$(document).off('click.' + this.getID());
			this.elements.$content.trigger('mae_content_hidden'); // add event for elements inside

		}

		onInit() {
			super.onInit();
			if (this.isEdit) {
				this.addEditHandler();
			}
			this.elements.$content.find('img[loading="lazy"]').removeAttr('loading');
		}

		setContentWidth() {

			var type = this.getElementSettings('content_width_type');
			var window_width = $(window).width();
			var breakpoint = this.getElementSettings('fullwidth_on');

			if (breakpoint && window_width <= breakpoint) {
				var container = $('body');
				this.elements.$content.addClass('fullwidth-breakpoint');
			} else {
				// clear styles
				this.elements.$content.css({
					'left': '',
					'width': ''
				});

				this.elements.$content.removeClass('fullwidth-breakpoint');

				switch (type) {
					case 'full':
						var container = $('body');
						break;
					case 'section':
						var container = this.$element.closest('section').children('.elementor-container');
						break;
					case 'column':
						var container = this.$element.closest('.elementor-column');
						break;
					case 'custom':
						return;
					default:
						return;
				}
			}



			var toggle_left = this.elements.$toggle.offset().left;
			var container_left = $(container).offset().left;
			var left = toggle_left - container_left;
			var container_width = $(container).width();

			// console.log(container_width);

			// set styles
			this.elements.$content.css({
				'left': -left,
				'width': container_width
			});

		}

		check_bounding() {
			var bounding = this.elements.$content[0].getBoundingClientRect();

			// console.log(bounding);
			var overflow_right = (window.innerWidth || document.documentElement.clientWidth) - bounding.right;

			if (overflow_right < 0) {
				this.elements.$content.width(bounding.width - Math.abs(overflow_right));
			} else {
				// this.elements.$content.css({'width': ''});
			}



		}

		addEditHandler() {
			var elementor_document = this.findElement('.elementor').prepend('<div class="elementor-document-handle" style="z-index: 999999;"><i class="eicon-edit"></i>Edit template</div>');
			this.findElement('.elementor-document-handle').on('click', function() {
				window.elementorCommon.api.internal('panel/state-loading');
				window.elementorCommon.api.run('editor/documents/switch', {
					id: elementor_document.attr('data-elementor-id')
				}).then(function() {
					return window.elementorCommon.api.internal('panel/state-ready');
				});
			})
		}

	}
	$(window).on('elementor/frontend/init', () => {
		const addHandler = ($element) => {
			elementorFrontend.elementsHandler.addHandler(MAETemplatePopup, {
				$element,
			});
		};
		elementorFrontend.hooks.addAction('frontend/element_ready/mae_template_popup.default', addHandler);
		// elementorFrontend.hooks.addAction( 'frontend/element_ready/mae_lang_dropdown.default', addHandler );

	});


	// Current Year

	class MAECurrentYear extends elementorModules.frontend.handlers.Base {
		onInit() {
			var current_year = new Date().getFullYear();
			$('.mae_current_year').text(current_year);
		}
	}
	$(window).on('elementor/frontend/init', () => {

		const addHandler = ($element) => {
			elementorFrontend.elementsHandler.addHandler(MAECurrentYear, {
				$element,
			});
		};
		elementorFrontend.hooks.addAction('frontend/element_ready/mae_current_year.default', addHandler);

	});
})(jQuery);