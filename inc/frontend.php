<?php
/**
 * Frontend filters.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Add automatic filtering and neccessary JavaScript.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */
class Lucid_Email_Encoder_Frontend {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	protected $_settings;

	/**
	 * Constructor, get options and add hooks.
	 */
	public function __construct() {
		$opt = $this->_settings = get_option( 'leejl_settings' );
		$encode_emails = ! empty( $opt['active_email_protection'] ) ? $opt['active_email_protection'] : '';
		$no_js_handling = ! empty( $opt['no_js_handling'] ) ? $opt['no_js_handling'] : false;

		// Always have decoding available, for manual use
		add_action( 'wp_head', array( $this, 'decode_function' ) );
		add_action( 'wp_footer', array( $this, 'decode_all' ) );

		// Don't do anything if encoding is disabled
		if ( 'entities' == $encode_emails || 'script' == $encode_emails ) :
			add_action( 'wp_head', array( $this, 'encode_emails' ) );
		endif;

		if ( $no_js_handling )
			add_action( 'wp_head', array( $this, 'email_message_handling' ), 1 );
	}

	/**
	 * Encode email addresses to protect them from harvesters.
	 */
	public function encode_emails() {
		$filters = apply_filters( 'leejl_encoding_filters', array(
			'the_content',
			'the_excerpt',
			'widget_text',
			'comment_text',
			'comment_excerpt'
		) );

		foreach ( $filters as $key => $filter ) :
			add_filter( $filter, array( $this, 'encode_callback' ), 999 );
		endforeach;
	}

	/**
	 * Encode email addresses to protect them from harvesters.
	 *
	 * @param string $content Content from filter to search for email addresses.
	 */
	public function encode_callback( $content ) {
		$encode_to_script = ( 'script' == $this->_settings['active_email_protection'] ) ? true : false;

		return Lucid_Email_Encoder::search_and_encode( $content, $encode_to_script );
	}

	/**
	 * Add necessary decoding functionality.
	 *
	 * Outputs minified code from js/email-decoder.min.js.
	 */
	public function decode_function() { ?>
		<script>var lucidEmailEncoder=function(e){"use strict";function n(e){return String.fromCharCode(("Z">=e?90:122)>=(e=e.charCodeAt(0)+13)?e:e-26)}function r(e){var r,t,o,d;r=e.innerHTML.replace(/£/g,"@"),t=r.match(c.encoded),t=null!==t?t[0]:"",o=" "+t.substring(7,t.length-1).replace(/[A-Za-z]/g,n),c.html.test(o)?(d=a.createElement("span"),d.innerHTML=o):d=a.createTextNode(o),e.parentNode.insertBefore(d,e)}function t(){for(var e=a.body.getElementsByTagName("script"),n=e.length-1;n>=0;n--)"lucid-email-encoder"===e[n].className&&r(e[n])}var a=e.document,c={encoded:/(var e=)[^;]+/,html:/</,href:/href=(?:"|')([^"']+)(?:"|')/,text:/(?:\>)([^<]+)<\/a>/};return{decodeAll:t,decodeEmail:r}}(window);</script>
	<?php }

	/**
	 * Decode all scripts.
	 */
	public function decode_all() { ?>
		<script>(function(){lucidEmailEncoder.decodeAll()})();</script>
	<?php }

	/**
	 * Handle display of the no-JS message by adding a .js class and a CSS rule.
	 */
	public function email_message_handling() { ?>
		<script>(function(d){var c=d.className;c.match(/\bjs\b/)||(d.className=c+' js')}(document.documentElement));</script>
		<style>.js .email-hidden-message{display:none;visibility:hidden}</style>
	<?php }
}