/*global navigator, location, window, document, jQuery, $*/

var miki;
(function ($) {
	'use strict';

	miki = {
		init: function () {
			miki.layout();
			miki.hotkeys();
		},

		layout: function () {
			/* Resize textarea with window. */
			$(window).resize(function () {
				$('#form_content').height($(window).height() - 260);
			}).resize();

			/* Tabs for edit page. */
			$('.nav-tabs a').click(function (e) {

				var mode = this.hash.slice(1);
				if (mode !== 'editor') {
					$('#' + mode).load('preview/' + mode, {'content': $('#form_content').val()});
				}

				if (mode) {
					$('.nav-tabs .dropdown-toggle span').html($(this).html());
				}

				$(this).tab('show');
				e.preventDefault();
			});
		},
		hotkeys: function () {
			var key = 'ctrl';
			if (navigator.appVersion.indexOf("Mac") !== -1) {
				key = 'meta';
			}

			/* Default hotkeys (Save, Cancel, Edit, Home)*/
			$('#wmd-input').bind('keydown.' + key + '_s', function () { $('form').submit(); return false; });
			$(document).bind('keydown.alt_' + key + '_x', function () { location.href = $('#form_cancel').attr('href'); return false; });
			$(document).bind('keydown.' + key + '_e', function () { location.href = $('#page_edit').attr('href'); return false; });
			$(document).bind('keydown.alt_' + key + '_h', function () { location.href = $('#menu_home').attr('href'); return false; });

			/* T0D0-List Hotkeys */
			$('.container li').click(function (e) {
				if (e.altKey) {
					if ($(this).children('s').length) {
						$(this).children('s').contents().unwrap();
					} else {
						$(this).wrapInner('<s />');
					}

					//add '@done (2012-09-12 12:22)'?
					//save !?

					e.preventDefault();
				}
			});
		}
	};
}(jQuery));

$(document).ready(function () {
	'use strict';

	miki.init();
});