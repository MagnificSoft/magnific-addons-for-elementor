jQuery(document).ready(function ($) {




	var priview_el = document.getElementById('elementor-preview-responsive-wrapper');

	var edited_element_id;


	// popup toolbar template



	// minimize button template
	var mae_popup_placeholder =
		'<div class="mae_popup_placeholder"></div>';


	// popup toolbar template
	var popup_toolbar =
		'<div class="popup_toolbar">\
			<div class="minimize_button"><i class="eicon-close"></i></div>\
			<div class="elementor-panel-heading-title popup_heading"></div>\
      </div>';

	// hook elementor code control to inject maximize button 
	if (MagnificAddons.mae_code_popup_enabled == '1') { 
		// var inject = '<# setup_custom_html_ace_editor(); #>';
		var maximize_button_template =
		'<div class="maximize_button_wrapper">\
			<div class="maximize_button code_control_maximize" data-popup-class="mae_code_popup">\
				<i class="eicon-lightbox"></i>\
			</div>\
      </div>';
		var template = $('#tmpl-elementor-control-code-content').html();
		$('#tmpl-elementor-control-code-content').html(maximize_button_template + template);
	}

	// hook elementor texarea control to inject maximize button 
	if (MagnificAddons.mae_textarea_popup_enabled == '1') { 
		// var inject = '<# setup_custom_html_ace_editor(); #>';
		var maximize_button_template =
		'<div class="maximize_button_wrapper">\
			<div class="maximize_button textarea_control_maximize" data-popup-class="mae_textarea_popup">\
				<i class="eicon-lightbox"></i>\
			</div>\
		</div>';
		var template = $('#tmpl-elementor-control-textarea-content').html();
		$('#tmpl-elementor-control-textarea-content').html(maximize_button_template + template);
	}

	// hook elementor wysiwyg control to inject maximize button 
	if (MagnificAddons.mae_text_popup_enabled == '1') { 
		var maximize_button_template =
		'<div class="maximize_button_wrapper">\
			<div class="maximize_button wysiwyg_control_maximize" data-popup-class="mae_wysiwyg_popup">\
				<i class="eicon-lightbox"></i>\
			</div>\
      </div>';
		var template = $('#tmpl-elementor-control-wysiwyg-content').html();
		$('#tmpl-elementor-control-wysiwyg-content').html(maximize_button_template + template);
	}







	$(window).load(function () {


		// DETECT ELEMENTOR UI THEME CHANGE
		$(document).on('change', 'select[data-setting="ui_theme"]', function () {
			// console.log('boom');
			val = $(this).val();
			if (val == 'dark') {
				$('head').append('<link id="-dark-mode" rel="stylesheet" href="' + MagnificAddons.mae_plugin_url + 'assets/-dark-mode.css" type="text/css" />');

			}
			if (val == 'light') {
				$('#-dark-mode-css').remove();
				$('#-dark-mode').remove();
			}
			if (val == 'auto') {
				if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
					$('head').append('<link id="-dark-mode" rel="stylesheet" href="' + MagnificAddons.mae_plugin_url + 'assets/-dark-mode.css" type="text/css" />');
				}
			}
		});



		// init resizable preview
		// $(priview_el).resizable({
		// 	start: function () {
		// 		$(priview_el).css('transition', 'none');
		// 		$('body').trigger('click');
		// 		$("#mae_preview_device_width").blur();
		// 		$("#mae_preview_device_height").blur();
		// 	},
		// 	stop: function () {
		// 		$(priview_el).css('transition', '');
		// 		var width = $(this).width();
		// 		var height = $(this).height();
		// 		$(priview_el).css('height', height);
		// 		if (width < 768) {
		// 			elementor.changeDeviceMode('mobile');
		// 		} else if (width > 767 && width < 1025) {
		// 			elementor.changeDeviceMode('tablet');
		// 		} else if (width > 1024) {
		// 			elementor.changeDeviceMode('desktop');
		// 		}
		// 		$('body').addClass('mae_resizable_mode');
		// 	},
		// 	resize: function (event, ui) {
		// 		ui.size.width += ui.size.width - ui.originalSize.width;
		// 		//ui.size.height += ui.size.height - ui.originalSize.height;
		// 	},
		// 	minHeight: 300,
		// 	minWidth: 300,
		// 	containment: "document",
		// 	handles: "s, e, se",
		// });



		// listen to body classList changes
		// (function () {
		// 	var el = document.querySelector('body');
		// 	var observer = new MutationObserver(function (mutations) {
		// 		var classList = mutations[0].target.classList;
		// 		if (classList.contains('elementor-device-desktop')) {

		// 			if (!$('body').hasClass('mae_responsive_panel_open')) {
		// 				$(el).removeClass('mae_resizable_mode');
		// 			}

		// 		}
		// 		if (classList.contains('elementor-device-tablet')) {
		// 			$(el).addClass('mae_resizable_mode');
		// 		}
		// 		if (classList.contains('elementor-device-mobile')) {
		// 			$(el).addClass('mae_resizable_mode');
		// 		}
		// 		if (classList.contains('mae_resizable_mode')) {

		// 		}


		// 		if (classList.contains('elementor-editor-preview')) {
		// 			//console.log('elementor-editor-preview');
		// 		}

		// 	})
		// 	observer.observe(el, {
		// 		attributes: true,
		// 		attributeFilter: ['class'],
		// 	})
		// })();


	});


	// setInterval(function() {
	//     var previousDeviceMode = elementor.channels.deviceMode.request('currentMode');
	//     console.log(previousDeviceMode);
	// }, 1000);





	// hook elementor panel footer to inject custom responsive control
	// (function(){
	//     var div = $('<div>').append($('#tmpl-elementor-panel-footer-content').html());
	//     div.find('#elementor-panel-footer-responsive .elementor-panel-footer-sub-menu-wrapper').append(responsive_html);
	//     var div_html = div.html();
	//     $('#tmpl-elementor-panel-footer-content').html(div_html);
	// })();





	// SET POPUP SETINGS
	if (typeof(Storage) !== "undefined") {
		 var popup_settings = JSON.parse(localStorage.getItem("popup_settings"));
	    if (popup_settings) {
	        $(':root')[0].style.setProperty('--popup_top', popup_settings.top);
	        $(':root')[0].style.setProperty('--popup_left', popup_settings.left);
	        $(':root')[0].style.setProperty('--popup_width', popup_settings.width);
	        $(':root')[0].style.setProperty('--popup_height', popup_settings.height);
	    }
	}







	// elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
	//   var $element = view.$el.find( '.elementor-selector' );
	//   console.log('widget');
	//   console.log($element);
	//   // model.setSetting('title', 'sample text');
	// });
	// elementor.hooks.addAction( 'panel/open_editor/column', function( panel, model, view ) {
	//   var $element = view.$el.find( '.elementor-selector' );
	//   console.log('column');
	//     console.log($element);
	// });



	// elementor.hooks.addAction( 'panel/open_editor/section', function( panel, model, view ) {
	//   var $element = view.$el.find( '.elementor-selector' );
	//   console.log('section');
	//   console.log($element);
	//   console.log(model.id);
	//   edited_element_id = model.id;
	// });







	// TEXT AND CODE WIDGETS POPUP
	$(document).on('click', '.maximize_button', function () {

		$('.minimize_button').trigger('click');

		// console.log(edited_element_id);

		var el = $(this).closest('.elementor-control').find('.elementor-control-content');

		var popup_class = $(this).attr('data-popup-class');

		// make popup
		$(el).addClass('mae_control_popup ' + popup_class).trigger('resize').prepend(popup_toolbar);

		$(mae_popup_placeholder).insertBefore(el);

		var section_title = $('#elementor-controls .elementor-control-type-section.elementor-open .elementor-panel-heading-title').text();
		$(el).find('.popup_heading').text(section_title);

		settings = {};

		// make druggable and resizable
		init_drag_and_resize('.mae_control_popup', settings);

		$(this).parent().hide();
	});









	$(document).on('click', '.minimize_button', function () {

		var parent = $(this).closest('.elementor-control');
		var el = $(parent).find('.elementor-control-content');

		$(parent).find('.mae_popup_placeholder').remove();

		$(el)[0].className = $(el)[0].className.replace(/\bmae.*?\b/g, '');

		$(el).trigger('resize');
		$(el).find('.popup_toolbar').remove();

		$(parent).find('.maximize_button_wrapper').show();

		$(el).draggable('destroy');
		$(el).resizable('destroy');
		$(el).removeAttr('style').show();

	});

	

	$(document).on('dblclick', '.popup_toolbar', function () {
		var el = $(this).parent();
		if($(el).hasClass('mae_popup_maximized')) {
			$(el).css({
				'top': 'var(--popup_top)',
				'left': 'var(--popup_left)',
				'width': 'var(--popup_width)',
				'height': 'var(--popup_height)',			
			});
			$(el).removeClass('mae_popup_maximized');
		} else {
			$(el).css({
				'top': '0px',
				'left': '0px',
				'width': '100vw',
				'height': '100vh',			
			});
			$(el).addClass('mae_popup_maximized');
		}
	});






	// add resizable and draggable to popup function
	function init_drag_and_resize(el, settings) {
		$(el).draggable({
			stop: function () {
				savePopupSettings(this);
			},
			cancel: ".ace_editor.ace-tm",
			containment: "window",
			iframeFix: true,
			cancel: ".elementor-wp-editor,.ace_editor,.mae_popup_maximized, textarea",
			zIndex: 1000,
		});
		$(el).resizable({
			start: function () {
				$('iframe').css('pointer-events', 'none');
			},
			stop: function () {
				$('iframe').css('pointer-events', '');
				savePopupSettings(this);
				$(this).removeClass('mae_popup_maximized');

			},
			minHeight: 320,
			minWidth: 500,
			containment: "document",
			handles: "all",
		});
	}








	// save popup settings to local storage
	function savePopupSettings(el) {

	   //  settings.top = $(el).css('top');
	   //  settings.left = $(el).css('left');
	   //  settings.height = $(el).css('height');
	   //  settings.width = $(el).css('width');

	   //  $(field).val(JSON.stringify(settings));


		var popup_settings = {
			'top': $(el).css('top'),
			'left': $(el).css('left'),
			'height': $(el).css('height'),
			'width': $(el).css('width'),
		}
		 
	    $(':root')[0].style.setProperty('--popup_top', popup_settings.top);
	    $(':root')[0].style.setProperty('--popup_left', popup_settings.left);
	    $(':root')[0].style.setProperty('--popup_width', popup_settings.width);
		 $(':root')[0].style.setProperty('--popup_height', popup_settings.height);
		 
	    if (typeof(Storage) !== "undefined") {
	        localStorage.setItem("popup_settings", JSON.stringify(popup_settings));
	    }

	}



	// COLLAPSE ALL CATEGORIES ON MIDDLE CLICK
	$(document).on('mousedown', '.elementor-panel-category', function(e) {

		if (e.which == 2) {

			var items = $(".elementor-panel-category-items");
			var categories = $(".elementor-panel-category");
			items.each(function(){
				$(this).slideToggle(280);
			});
			// console.log(items);
			// $(".elementor-panel-category-items").slideToggle(280);
			// $(".elementor-panel-category").toggleClass("elementor-active");

		 }
	 });





});








/**
 * Copyright Marc J. Schmidt. See the LICENSE file at the top-level
 * directory of this distribution and at
 * https://github.com/marcj/css-element-queries/blob/master/LICENSE.
 */
(function (root, factory) {
	if (typeof define === "function" && define.amd) {
		define(factory);
	} else if (typeof exports === "object") {
		module.exports = factory();
	} else {
		root.ResizeSensor = factory();
	}
}(this, function () {

	// Make sure it does not throw in a SSR (Server Side Rendering) situation
	if (typeof window === "undefined") {
		return null;
	}
	// Only used for the dirty checking, so the event callback count is limited to max 1 call per fps per sensor.
	// In combination with the event based resize sensor this saves cpu time, because the sensor is too fast and
	// would generate too many unnecessary events.
	var requestAnimationFrame = window.requestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		function (fn) {
			return window.setTimeout(fn, 20);
		};

	/**
	 * Iterate over each of the provided element(s).
	 *
	 * @param {HTMLElement|HTMLElement[]} elements
	 * @param {Function}                  callback
	 */
	function forEachElement(elements, callback) {
		var elementsType = Object.prototype.toString.call(elements);
		var isCollectionTyped = ('[object Array]' === elementsType ||
			('[object NodeList]' === elementsType) ||
			('[object HTMLCollection]' === elementsType) ||
			('[object Object]' === elementsType) ||
			('undefined' !== typeof jQuery && elements instanceof jQuery) //jquery
			||
			('undefined' !== typeof Elements && elements instanceof Elements) //mootools
		);
		var i = 0,
			j = elements.length;
		if (isCollectionTyped) {
			for (; i < j; i++) {
				callback(elements[i]);
			}
		} else {
			callback(elements);
		}
	}

	/**
	 * Class for dimension change detection.
	 *
	 * @param {Element|Element[]|Elements|jQuery} element
	 * @param {Function} callback
	 *
	 * @constructor
	 */
	var ResizeSensor = function (element, callback) {
		/**
		 *
		 * @constructor
		 */
		function EventQueue() {
			var q = [];
			this.add = function (ev) {
				q.push(ev);
			};

			var i, j;
			this.call = function () {
				for (i = 0, j = q.length; i < j; i++) {
					q[i].call();
				}
			};

			this.remove = function (ev) {
				var newQueue = [];
				for (i = 0, j = q.length; i < j; i++) {
					if (q[i] !== ev) newQueue.push(q[i]);
				}
				q = newQueue;
			}

			this.length = function () {
				return q.length;
			}
		}

		/**
		 *
		 * @param {HTMLElement} element
		 * @param {Function}    resized
		 */
		function attachResizeEvent(element, resized) {
			if (!element) return;
			if (element.resizedAttached) {
				element.resizedAttached.add(resized);
				return;
			}

			element.resizedAttached = new EventQueue();
			element.resizedAttached.add(resized);

			element.resizeSensor = document.createElement('div');
			element.resizeSensor.className = 'resize-sensor';
			var style = 'position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;';
			var styleChild = 'position: absolute; left: 0; top: 0; transition: 0s;';

			element.resizeSensor.style.cssText = style;
			element.resizeSensor.innerHTML =
				'<div class="resize-sensor-expand" style="' + style + '">' +
				'<div style="' + styleChild + '"></div>' +
				'</div>' +
				'<div class="resize-sensor-shrink" style="' + style + '">' +
				'<div style="' + styleChild + ' width: 200%; height: 200%"></div>' +
				'</div>';
			element.appendChild(element.resizeSensor);

			if (element.resizeSensor.offsetParent !== element) {
				element.style.position = 'relative';
			}

			var expand = element.resizeSensor.childNodes[0];
			var expandChild = expand.childNodes[0];
			var shrink = element.resizeSensor.childNodes[1];
			var dirty, rafId, newWidth, newHeight;
			var lastWidth = element.offsetWidth;
			var lastHeight = element.offsetHeight;

			var reset = function () {
				expandChild.style.width = '100000px';
				expandChild.style.height = '100000px';

				expand.scrollLeft = 100000;
				expand.scrollTop = 100000;

				shrink.scrollLeft = 100000;
				shrink.scrollTop = 100000;
			};

			reset();

			var onResized = function () {
				rafId = 0;

				if (!dirty) return;

				lastWidth = newWidth;
				lastHeight = newHeight;

				if (element.resizedAttached) {
					element.resizedAttached.call();
				}
			};

			var onScroll = function () {
				newWidth = element.offsetWidth;
				newHeight = element.offsetHeight;
				dirty = newWidth != lastWidth || newHeight != lastHeight;

				if (dirty && !rafId) {
					rafId = requestAnimationFrame(onResized);
				}

				reset();
			};

			var addEvent = function (el, name, cb) {
				if (el.attachEvent) {
					el.attachEvent('on' + name, cb);
				} else {
					el.addEventListener(name, cb);
				}
			};

			addEvent(expand, 'scroll', onScroll);
			addEvent(shrink, 'scroll', onScroll);
		}

		forEachElement(element, function (elem) {
			attachResizeEvent(elem, callback);
		});

		this.detach = function (ev) {
			ResizeSensor.detach(element, ev);
		};
	};

	ResizeSensor.detach = function (element, ev) {
		forEachElement(element, function (elem) {
			if (!elem) return
			if (elem.resizedAttached && typeof ev == "function") {
				elem.resizedAttached.remove(ev);
				if (elem.resizedAttached.length()) return;
			}
			if (elem.resizeSensor) {
				if (elem.contains(elem.resizeSensor)) {
					elem.removeChild(elem.resizeSensor);
				}
				delete elem.resizeSensor;
				delete elem.resizedAttached;
			}
		});
	};

	return ResizeSensor;

}));






// RESPONSIVE CUSTOM CSS FOR EDITOR
jQuery(window).on('elementor:init', function () {

	(function ($) {
		"use strict";

		// console.log(elementor);

		function addCustomCss(css, context) {

			if (!context) {
				return;
			}

			var model = context.model;
			var customCSS = '';
			var breakpoints = elementorFrontend.config.breakpoints;

			// console.log(breakpoints);

			var responsive_custom_css_repeater = model.get('settings').get('responsive_custom_css_repeater');

			var selector = '.elementor-element.elementor-element-' + model.get('id');
			if ('document' === model.get('elType')) {
				selector = elementor.config.document.settings.cssWrapperSelector;
			}

			if (responsive_custom_css_repeater !== undefined) {
				$.each(responsive_custom_css_repeater.models, function (index, model) {



					var item_custom_css = model.get('item_custom_css');
					var item_breakpoint = model.get('item_breakpoint');
					var media_query = '';


					if (item_breakpoint == 'desktop') {
						media_query = '@media(min-width:' + breakpoints.lg + 'px)';
					} else if (item_breakpoint == 'tablet') {
						media_query = '@media(min-width:' + breakpoints.md + 'px) and (max-width:' + (breakpoints.lg - 1) + 'px)';
					} else if (item_breakpoint == 'mobile') {
						media_query = '@media(max-width:' + (breakpoints.md - 1) + 'px)';
					} else if (item_breakpoint == 'desktop+tablet') {
						media_query = '@media(min-width:' + breakpoints.md + 'px)';
					} else if (item_breakpoint == 'tablet+mobile') {
						media_query = '@media(max-width:' + (breakpoints.lg - 1) + 'px)';
					}

					customCSS += media_query + '{' + item_custom_css + '}';

				});
			}

			if (customCSS) {
				css += customCSS.replace(/selector/g, selector);
			}

			// console.log(css);

			return css;
		}



		function addPageCustomCss() {

			var customCSS = elementor.settings.page.model.get('custom_css');

			if (customCSS) {
				customCSS = customCSS.replace(/selector/g, elementor.config.document.settings.cssWrapperSelector);
				elementor.settings.page.getControlsCSS().elements.$stylesheetElement.append(customCSS);
			}
		}

		elementor.hooks.addFilter('editor/style/styleText', addCustomCss, 100);
		elementor.settings.page.model.on('change', addPageCustomCss);
		elementor.on('preview:loaded', addPageCustomCss);

	})(jQuery);

});