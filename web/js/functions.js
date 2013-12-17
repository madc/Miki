/*global navigator, location, window, document, jQuery, $*/

var miki;
(function ($) {
	'use strict';

	miki = {
		init: function () {
			miki.layout();
			miki.hotkeys();

			miki.editor.init();
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
			$('#form_content').bind('keydown.' + key + '_s', function () { $('form').submit(); return false; });
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
		},
		/** miki. Markdown Editor
		 *  (Move into seperate plugin)?
		 */
		editor: {
			init: function () {
				$('.editor-buttons button').click(function () {
					var el = $(this),
						editor = $('#form_content');

					switch (el.attr('role')) {
					case 'toggle-headline':
						//get line
						var line = miki.editor.getLine();
						console.log(line);
						//check if already headline
						//if not or <= #### -> add # (until ####)
						//else decrese until none
						break;
					case 'toggle-bold':
						miki.editor.wrap('**');
						break;
					case 'toggle-italic':
						miki.editor.wrap('_');
						break;
					case 'toggle-ul':
						miki.editor.wrap("\n" + '- ');
						break;
					case 'toggle-link':
						miki.editor.wrap('[', ']()');
						break;
					}

					// Return forus to editor
					editor.focus();
				});

				$('#form_content').keydown(function (e) {
					var keyCode = e.keyCode || e.which,
						editor = $('#form_content'),
						line = miki.editor.getLine();

					// Remove four spaces on return
					if (keyCode === 8) {
						if (editor.val().substring(line.selectStart - 4, line.selectStart) === '    ') {
							e.preventDefault();
							miki.editor.remove(line.selectStart - 4, line.selectStart);
						}
					}
					// Convert tabs into four spaces (http://stackoverflow.com/a/6637396/709769)
					if (keyCode === 9) {
						e.preventDefault();
						miki.editor.insert('    ');
					}
					// Remember UL on newline
					if (keyCode === 13) {
						if (line.lineContent.substr(0, 2) === '- ') {
							e.preventDefault();
							miki.editor.insert("\n" + '- ');
						}

						if (line.lineContent.substr(0, 3) === ' - ') {
							e.preventDefault();
							miki.editor.insert("\n" + ' - ');
						}
					}
				});
			},
			getLine: function (selectStart, selectEnd) { // http://stackoverflow.com/a/8045242/709769
				var editor = $('#form_content'),
					val = editor.val();

			    selectStart = selectStart !== undefined ? selectStart : editor[0].selectionStart;
			    selectEnd = selectEnd !== undefined ? selectEnd : editor[0].selectionEnd;

				var lineStart = val.lastIndexOf('\n', selectStart - 1) + 1,
					lineEnd = val.indexOf('\n', selectStart);

				if (lineEnd === -1) {
					lineEnd = val.length;
				}

				return {
					'line': val.substr(0, selectStart).split("\n").length,
					'lineStart': lineStart,
					'lineEnd': lineEnd,
					'lineContent': val.substr(lineStart, lineEnd - lineStart),
					'selectStart': selectStart,
					'selectEnd': selectEnd,
					'selectContent': val.substr(selectStart, selectEnd - selectStart)
				};
			},
			insert: function (char, selectStart, selectEnd) {
				var editor = $('#form_content');

			    selectStart = selectStart !== undefined ? selectStart : editor[0].selectionStart;
			    selectEnd = selectEnd !== undefined ? selectEnd : editor[0].selectionEnd;

				// Insert Char
				editor.val(editor.val().substring(0, selectStart) + char + editor.val().substring(selectEnd));

				// put caret at right position again
				editor[0].selectionStart =
					editor[0].selectionEnd = selectStart + char.length;
			},
			remove: function (selectStart, selectEnd) {
				var editor = $('#form_content');

			    selectStart = selectStart !== undefined ? selectStart : editor[0].selectionStart;
			    selectEnd = selectEnd !== undefined ? selectEnd : editor[0].selectionEnd;

				editor.val(editor.val().substring(0, selectStart) + editor.val().substring(selectEnd));

				// put caret at right position again
				editor[0].selectionStart =
					editor[0].selectionEnd = selectStart;
			},
			wrap: function (prefix, suffix, selectStart, selectEnd) {
				var editor = $('#form_content');

			    suffix = suffix !== undefined ? suffix : prefix;
			    selectStart = selectStart !== undefined ? selectStart : editor[0].selectionStart;
			    selectEnd = selectEnd !== undefined ? selectEnd : editor[0].selectionEnd;

				// Insert Char
				editor.val(editor.val().substring(0, selectStart) + prefix + editor.val().substring(selectStart, selectEnd) + suffix + editor.val().substring(selectEnd));

				// Put caret at right position again
				if (selectStart === selectEnd) {
					editor[0].selectionStart =
						editor[0].selectionEnd = selectStart + prefix.length;
				} else {
					editor[0].selectionStart = selectStart;
					editor[0].selectionEnd = selectEnd + prefix.length + suffix.length;
				}
			},
			unwrap: function () {
			}
		}
	};
}(jQuery));

$(document).ready(function () {
	'use strict';

	miki.init();
});