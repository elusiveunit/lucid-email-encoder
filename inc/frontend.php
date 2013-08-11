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

		// Always have decoding available, for manual use
		add_action( 'wp_head', array( $this, 'decode_function' ) );
		add_action( 'wp_footer', array( $this, 'decode_all' ) );

		// Don't do anything if encoding is disabled
		if ( 'entities' == $encode_emails || 'script' == $encode_emails ) :
			add_action( 'wp_head', array( $this, 'encode_emails' ) );
		endif;
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
		$encode_to_script = ( 'script' == $this->_settings['active_email_protection'] );

		return Lucid_Email_Encoder::search_and_encode( $content, $encode_to_script );
	}

	/**
	 * Add necessary decoding functionality.
	 *
	 * Outputs minified code from js/email-decoder.min.js.
	 */
	public function decode_function() { ?>
		<script>var lucidEmailEncoder=function(e){"use strict";function n(e){return String.fromCharCode(("Z">=e?90:122)>=(e=e.charCodeAt(0)+13)?e:e-26)}function r(e,r){var a,d,t;a=e.innerHTML.replace(/£/g,"@"),d=a.match(c.encoded),d=null!==d?d[0]:"",t=d.substring(7,d.length-1).replace(/[A-Za-z]/g,n),r.className=o,r.innerHTML=t}function a(){for(var e,n=d.body.getElementsByTagName("script"),a=n.length-1;a>=0;a--)e=n[a].parentNode,n[a].parentNode.className===t&&r(n[a],e)}var d=e.document,c={encoded:/(var e=)[^;]+/,href:/href=(?:"|')([^"']+)(?:"|')/,text:/(?:\>)([^<]+)<\/a>/},t="lucid-email-encoded",o="lucid-email-decoded";return{decodeAll:a,decodeEmail:r}}(window);</script>
	<?php }

	/**
	 * Decode all scripts.
	 */
	public function decode_all() { ?>
		<script>(function(){lucidEmailEncoder.decodeAll()})();</script>
	<?php }
}