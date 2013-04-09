/**
 * Email decoding. Due to small size, this is minified and embedded directly
 * into the <head>.
 */
var lucidEmailEncoder = (function ( win, undefined ) {
	'use strict';

	var doc = win.document,
	regex = {
		encoded: /(var e=)[^;]+/,
		html: /</,
		href: /href=(?:"|')([^"']+)(?:"|')/,
		text: /(?:\>)([^<]+)<\/a>/
	};

	/**
	 * Perform the ROT13 transform on a character.
	 *
	 * @param {string} Character.
	 * @return {string}
	 */
	function leejlRot13(c) {
		return String.fromCharCode( ( c <= 'Z' ? 90 : 122 ) >= ( c = c.charCodeAt(0) + 13 ) ? c : c - 26 );
	}

	/**
	 * Decode a lucid email script.
	 *
	 * @param {node} scriptNode Script node containing encoded string.
	 */
	function decodeEmail( scriptNode ) {
		var script, encoded, decoded, elem;

		script = scriptNode.innerHTML.replace( /£/g, '@' );

		encoded = script.match( regex.encoded );
		encoded = ( null !== encoded ) ? encoded[0] : '';

		// Space needed for old IE
		decoded = ' ' + encoded.substring( 7, encoded.length - 1 ).replace( /[A-Za-z]/g, leejlRot13 );

		// If decoded string has HTML, do innerHTML. Otherwise just create a text node
		if ( regex.html.test( decoded ) ) {
			elem = doc.createElement( 'span' );
			elem.innerHTML = decoded;
		} else {
			elem = doc.createTextNode( decoded );
		}

		scriptNode.parentNode.insertBefore( elem, scriptNode );
	}

	/**
	 * Decode all lucid email scripts.
	 */
	function decodeAll() {
		var scripts = doc.body.getElementsByTagName( 'script' );

		for ( var i = scripts.length - 1; i >= 0; i-- ) {
			if ( 'lucid-email-encoder' === scripts[i].className ) {
				decodeEmail( scripts[i] );
			}
		}
	}

	/**
	 * Public API.
	 */
	return {
		decodeAll: decodeAll,
		decodeEmail: decodeEmail
	};
})( window );