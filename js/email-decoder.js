/**
 * Email decoding. Due to small size, this is minified and embedded directly
 * into the <head>.
 */
var lucidEmailEncoder = (function ( win, undefined ) {
	'use strict';

	var doc = win.document,
	regex = {
		encoded: /(var e=)[^;]+/,
		href: /href=(?:"|')([^"']+)(?:"|')/,
		text: /(?:\>)([^<]+)<\/a>/
	},
	encodedClass = 'lucid-email-encoded',
	decodedClass = 'lucid-email-decoded';

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
	 * @param {node} parentNode Parent of script node.
	 */
	function decodeEmail( scriptNode, parentNode ) {
		var script, encoded, decoded, elem;

		script = scriptNode.innerHTML.replace( /£/g, '@' );

		encoded = script.match( regex.encoded );
		encoded = ( null !== encoded ) ? encoded[0] : '';

		decoded = encoded.substring( 7, encoded.length - 1 ).replace( /[A-Za-z]/g, leejlRot13 );

		// Replace the encoded content with the decoded address
		parentNode.className = decodedClass;
		parentNode.innerHTML = decoded;
	}

	/**
	 * Decode all lucid email scripts.
	 */
	function decodeAll() {
		var scripts = doc.body.getElementsByTagName( 'script' ),
		    parent;

		for ( var i = scripts.length - 1; i >= 0; i-- ) {
			parent = scripts[i].parentNode;

			if ( scripts[i].parentNode.className === encodedClass ) {
				decodeEmail( scripts[i], parent );
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