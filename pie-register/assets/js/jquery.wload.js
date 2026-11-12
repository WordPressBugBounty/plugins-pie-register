/*
 * Pie Register: loading overlay.
 * Small first-party replacement for the unmaintained jquery.Wload plugin (since 4.0.0.0).
 * Keeps the same API and class names, so existing calls and add-ons keep working:
 *   jQuery( 'body' ).Wload( { text: ' Loading', el: '.optional-container' } );
 *   jQuery( 'body' ).Wload( 'hide', { time: 200 } );
 */
(function ($) {
	'use strict';

	function hide(target, time) {
		var remove = function () {
			$( target ).find( '.w_load_body' ).remove();
			$( target ).removeClass( 'w_load_body_parent' );
		};
		if ( time ) {
			setTimeout( remove, time );
		} else {
			remove();
		}
	}

	function show(target, text) {
		var $target  = $( target );
		var $overlay = $( '<div class="w_load_body" role="status" aria-live="polite"></div>' );
		var $content = $( '<div class="w_load_content"></div>' );

		$content.append( '<span class="w_load_icon" aria-hidden="true"></span>' );
		// Text only: the message is never interpreted as HTML.
		$content.append( $( '<span class="w_load_content_text"></span>' ).text( text ? String( text ) : '' ) );
		$overlay.append( $content );
		$target.addClass( 'w_load_body_parent' ).append( $overlay );
	}

	$.fn.Wload = function (options, other) {
		var el = this;

		if ( 'hide' === options ) {
			if ( other && 'object' === typeof other && other.el ) {
				el = other.el;
			}
			hide( el, other && other.time ? other.time : 0 );
			return this;
		}

		options = ( options && 'object' === typeof options ) ? options : {};
		show( options.el ? options.el : el, options.text );
		return this;
	};
})( jQuery );
