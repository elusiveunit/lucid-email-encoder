<?php
/**
 * Email encoder class definition.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Encode email addresses to protect them from harvesters.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */
class Lucid_Email_Encoder {

	/**
	 * HTML class name for the encoded address element.
	 *
	 * @var string
	 */
	private static $encoded_class = 'lucid-email-encoded';

	/**
	 * HTML class name for the element containing the no-JS message.
	 *
	 * @var string
	 */
	private static $message_class = 'email-hidden-message';

	/**
	 * If addresses should be encoded to JavaScript ROT13.
	 *
	 * Simply used as a way to pass the search_and_encode param to the regex
	 * replace callback.
	 *
	 * @var bool
	 */
	private static $encode_to_script = true;

	/**
	 * Searches for email addresses in given $string and encodes them.
	 *
	 * Regular expression is based on based on John Gruber's Markdown.
	 * http://daringfireball.net/projects/markdown/
	 *
	 * @param string $string Text with email addresses to encode.
	 * @param bool $encode_to_script Output script tags with document.write.
	 * @return string $string Given text with encoded email addresses
	 */
	public static function search_and_encode( $string, $encode_to_script = true ) {

		// Abort if $string doesn't contain an @-sign.
		if ( false === strpos( $string, '@' ) ) return $string;

		// Find an anchor with 'href="mailto:' inside.
		$mailto_link_regex = apply_filters( 'leejl_mailto_regex', '(?:<a(?:[^>]*?(?:href=(?:\s)?(?:"|\')mailto:).*?)<\/a>)' );

		// Find an email address with an optional mailto: in front.
		$email_adr_regex = apply_filters( 'leejl_email_regex', '(?:(?:mailto:)?(?:[-\!\#\$%\&\*\+\/=\?\^_`\.\{\|\}~\w]+|".*?")\@(?:[-a-z0-9]+(?:\.[-a-z0-9]+)*\.[a-z]+|\[[\d.a-fA-F:]+\]))' );

		// If searching for links, include href to find emails inside anchors
		// without mailto, for later handling.
		if ( $encode_to_script )
			$email_adr_regex = '(href=")?' . $email_adr_regex;

		// If searching for links, find a mailto link or a plain email address.
		if ( $encode_to_script ) :
			$regex = "/{$mailto_link_regex}|{$email_adr_regex}/";

		// Otherwise just search for a plain email address.
		else :
			$regex = "/{$email_adr_regex}/";
		endif;

		return preg_replace_callback( $regex, array( 'Lucid_Email_Encoder', 'replace_callback' ), $string );
	}

	/**
	 * Run an encoding function on email address matches.
	 *
	 * Callback for preg_replace_callback in search_and_encode.
	 *
	 * Having a match in the first position means the attribute capture group
	 * got filled, so the address is in an attribute. Adding script tags inside
	 * attributes will break things, so we'll just return the original match.
	 *
	 * @see search_and_encode()
	 * @param array $matches Regex matches from preg_replace_callback.
	 * @return string The replacement for the match.
	 */
	public static function replace_callback( $matches ) {
		if ( isset( $matches[1] ) )
			return $matches[0];

		if ( self::$encode_to_script )
			return Lucid_Email_Encoder::encode_to_script( $matches[0] );
		else
			return Lucid_Email_Encoder::encode_string( $matches[0], false );
	}

	/**
	 * A message to display before the email address for people with Javascript
	 * disabled. Control display with a simple no-js/js class on the html
	 * element.
	 *
	 * @return string Message if one is set, empty otherwise.
	 */
	public static function get_message() {
		$opt = get_option( 'leejl_settings' );
		$no_js_text = ( ! empty( $opt['no_js_text'] ) ) ? $opt['no_js_text'] : false;
		$no_js_message = '';

		if ( $no_js_text )
			$no_js_message = '<span class="' . self::$message_class . '">' . htmlspecialchars( $no_js_text ) . '</span>';

		return $no_js_message;
	}

	/**
	 * ROT13 encode a string and place it in a script tag.
	 *
	 * @param string $string Text with email addresses to encode.
	 * @return string Script tag with encoded text.
	 */
	public static function encode_to_script( $string ) {
		$no_js_message = self::get_message();

		$email_script = '<span class="' . self::$encoded_class . '">';
		$email_script .= self::get_message();
		$email_script .= '<script>(function(){';
		$email_script .= "var e='" . str_rot13( str_replace( '@', '£', $string ) ) . "';";
		$email_script .= '}());</script></span>';

		return $email_script;
	}

	/**
	 * Split a string, encode random parts and return JavaScript that joins it.
	 *
	 * Older version that uses document.write. There is probably no use case for
	 * it and it will most likely be removed in the future.
	 *
	 * @param string $string Text with email addresses to encode.
	 * @return string Script tag with encoded text split for joining with
	 *   JavaScript.
	 */
	public static function encode_to_script_old( $string ) {

		// If a mailto is found, one can assume it is a link. Links get split in
		// bigger parts since they have more characters, so there isn't too much
		// concatenation going on.
		if ( false !== strpos( $string, 'mailto' ) ) :
			$parts = str_split( $string, rand( 8, 10 ) );

			// Get all keys since strings with HTML characters will be skipped
			$get_keys = count( $parts );
		else :
			$parts = str_split( $string, rand( 5, 7 ) );

			// Get half of the keys, rounded up
			$get_keys = round( ( count( $parts ) / 2 ) + 0.2 );
		endif;

		// Encode random parts of the string
		$random_keys = array_rand( $parts, $get_keys );
		foreach ( $random_keys as $key => $random_key ) :
			$parts[$random_key] = self::encode_string( $parts[$random_key] );
		endforeach;

		$no_js_message = self::get_message();

		// JavaScript string concatenation format
		$write = implode( "' + '", $parts );

		// Break up HTML so it definitely won't get parsed
		$write = str_replace( '<a', "<' + 'a", $write );
		$write = str_replace( '</a', "<' + '/a", $write );

		$email_script = "<script>document.write('" . $write . "');</script>";

		return $no_js_message . $email_script;
	}

	/**
	 * Encodes each character of the given string as either a decimal
	 * or hexadecimal entity.
	 *
	 * Based on Michel Fortin's PHP Markdown.
	 * (http://michelf.com/projects/php-markdown/)
	 * Which is based on John Gruber's original Markdown.
	 * (http://daringfireball.net/projects/markdown/)
	 * Whose code is based on a filter by Matthew Wickline, posted to
	 * the BBEdit-Talk with some optimizations by Milian Wolff.
	 *
	 * @param string $string Text with email addresses to encode.
	 * @param bool $skip_html Don't encode strings with HTML characters like <.
	 * @return string Encoded string.
	 */
	public static function encode_string( $string, $skip_html = true ) {

		// Skip strings that contain HTML characters.
		if ( $skip_html && preg_match( '/[<>=\"\'\/]/', $string ) ) return $string;

		$chars = str_split( $string );
		$seed = mt_rand( 0, (int) abs( crc32( $string ) / strlen( $string ) ) );

		foreach ( $chars as $key => $char ) :
			$ord = ord( $char );

			if ( $ord < 128 ) : // Ignore non-ascii chars
				$rnd = ( $seed * ( 1 + $key ) ) % 100; // Pseudo "random function"

				// Plain character (not encoded), if not @-sign
				if ( $rnd > 60 && $char != '@' ) :
					// Do nothing

				// Hexadecimal
				elseif ( $rnd < 45 ) :
					$chars[$key] = '&#x' . dechex( $ord ) . ';';

				// Decimal (ascii)
				else :
					$chars[$key] = '&#' . $ord . ';';
				endif;
			endif;
		endforeach;

		return implode( '', $chars );
	}
}