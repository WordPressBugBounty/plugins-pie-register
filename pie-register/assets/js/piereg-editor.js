/*
 * Pie Register: rich text editor for HTML fields and e-mail messages.
 * Uses the editor that ships with WordPress (TinyMCE), which WordPress keeps up to date.
 * Replaces the bundled CKEditor 4. Every textarea with the class "ckeditor" or "piereg-editor"
 * is turned into an editor on page load, the same way CKEditor did.
 * Since v4.0.0.0
 */
(function ($) {
	'use strict';

	var counter = 0;
	var selector = 'textarea.ckeditor, textarea.piereg-editor';

	function available() {
		return !!( window.wp && window.wp.editor && 'function' === typeof window.wp.editor.initialize );
	}

	function settings() {
		return {
			tinymce: {
				// Keep the HTML exactly as written; the field's HTML is saved and shown as is.
				wpautop: false,
				toolbar1: 'formatselect,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo'
			},
			quicktags: true,
			mediaButtons: false
		};
	}

	function init(id) {
		if ( ! id || ! available() || ! document.getElementById( id ) ) {
			return;
		}
		if ( window.tinymce && window.tinymce.get( id ) ) {
			return;
		}
		window.wp.editor.initialize( id, settings() );
	}

	function remove(id) {
		if ( ! id || ! available() ) {
			return;
		}
		var editor = window.tinymce ? window.tinymce.get( id ) : null;
		if ( editor && ! editor.isHidden() ) {
			editor.save();
		}
		window.wp.editor.remove( id );
	}

	function each(context, callback) {
		$( context || document ).find( selector ).each( function () {
			if ( ! this.id ) {
				this.id = 'piereg_editor_' + ( ++counter );
			}
			callback( this.id );
		} );
	}

	window.pieregEditor = {
		init: init,
		remove: remove,
		initAll: function (context) { each( context, init ); },
		removeAll: function (context) { each( context, remove ); },
		// Copy every editor's content back to its textarea (before a form is submitted).
		save: function () {
			if ( window.tinymce ) {
				window.tinymce.triggerSave();
			}
		}
	};

	$( function () {
		window.pieregEditor.initAll();
	} );
})( jQuery );
