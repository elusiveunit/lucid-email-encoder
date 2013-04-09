/**
 * Generate 'manual' encoded email.
 */
var lucidEmailEncoderScriptGenerator = (function ( win, $, undefined ) {
	'use strict';

	var elems = {
		$generateField: $('#manual_generator'),
		$generateButton: $('#manual_generator-button'),
		$generateOutput: $('#leejl-generator-output')
	};

	/**
	 * Initialization.
	 */
	function init() {
		bindEvents();
	}

	/**
	 * Bind events.
	 */
	function bindEvents() {
		elems.$generateButton.on( 'click', function(e) {
			e.preventDefault();
			generateScript();
		});
	}

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
	 * Generate a 'manual call', encoded script tag.
	 *
	 * Text trim: http://blog.stevenlevithan.com/archives/faster-trim-javascript
	 */
	function generateScript() {
		var text = elems.$generateField.val().replace( /^\s\s*/, '' ).replace( /\s\s*$/, '' ),
		    script = '';

		if ( '' === text ) { return; }

		text = text.replace( /@/g, '£' ).replace( /[A-Za-z]/g, leejlRot13 );

		script = '<script class="lucid-email-encoder">(function(){var e=\'' + text + '\';}());</script>';

		elems.$generateOutput.text( script ).removeClass( 'hidden' );
	}

	/**
	 * Public API.
	 */
	return {
		init: init
	};

})( window, jQuery );

jQuery(document).ready(function() {
	'use strict';

	lucidEmailEncoderScriptGenerator.init();

});