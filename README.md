# Lucid Email Encoder

Automatically protect email addresses from harvesters with HTML entities or JavaScript.

Originally based on [Email Address Encoder](http://wordpress.org/extend/plugins/email-address-encoder/) by Till Krüss, Lucid Email Encoder takes it a step further by also offering JavaScript encoding. The plugin automatically searches for email addresses and mailto links in content, comments and widgets, and converts them according to settings.

A test by Silvan Mühlemann [comparing ways to obfuscate e-mail addresses](http://techblog.tilllate.com/2008/07/20/ten-methods-to-obfuscate-e-mail-addresses-compared/), shows that ROT13 encryption offers strong protection. This plugin also converts the at sign (@) to a pound sign (£), to harden it further. A link like `<a href="mailto:hi@example.com">Email me</a>` would show up in the source as `<n uers="znvygb:uv£rknzcyr.pbz">Rznvy zr</n>`.

If you don't want a JavaScript dependency, entity encoding would change the above link to something like `<a href="mailto:&#104;&#105;&#64;&#101;&#x78;&#x61;&#x6d;&#x70;&#x6c;&#x65;&#x2e;&#x63;&#x6f;m">Email me</a>`.

**Requires [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox)**, which is a plugin with a set of classes used to speed up and automate common tasks. This is kept as a separate plugin for easier development and updates. **This plugin will try to install and/or activate Lucid Toolbox** on plugin activation, if it's not available. It simply unzips a bundled version to the directory one level above its install location, if it's not there already, and runs `activate_plugin`.

Lucid Email Encoder is currently available in the following languages:

* English
* Swedish

## Basic usage

Just install it and choose what protection to use. Defaults to HTML entities.

## Frequently Asked Questions

### My email addresses have spacing issues in old IE

Yes, this is a problem to which I have found no solution. If using JavaScript protection, email addresses and mailto links surrounded by other text, such as in a regular paragraph, will have an extra space added to the end in IE 7 and 8. In IE7, there will also be a space missing before mailto links. Examples:

* 'My email is hi@mysite.com, say hi!' will become 'My email is hi@mysite.com , say hi!'.
* 'Here is a mailto [link](#).' will become 'Here is a mailto[link](#) .' in IE7 (same as above in IE8).

### How can I add filtering where there is none?

There are several ways:

* Add an existing filter via `leejl_encoding_filters`, see 'Available hooks' section.
* Call the encoding function on a string: `Lucid_Email_Encoder::encode_to_script( 'My email: hi@mysite.com' );` for JavaScript or `Lucid_Email_Encoder::encode_string( 'My email: hi@mysite.com' );` for HTML entities. Note that `encode_string` ignores strings with HTML, since I'm not sure every browser handles HTML encoded as entities.
* Use the script generator on the settings page to get an encoded snippet.

## Available hooks

**leejl\_encoding\_filters**

Filters that run through the encoding function. Defaults to `the_content`, `the_excerpt`, `widget_text`, `comment_text` and `comment_excerpt`.

	/**
	 * Add filters to Lucid Email Encoder.
	 *
	 * @param array $filters Default filters.
	 * @return array
	 */
	function prefix_encoding_filters( $filters ) {
		$filters[] = 'additional_filter';

		return $filters;
	}
	add_filter( 'leejl_encoding_filters', 'prefix_encoding_filters' );

To replace the filter list, return your own array:

	// Only encode email addresses in comments
	return array( 'comment_text', 'comment_excerpt' );

-----

**leejl\_mailto\_regex**

Regular expression used to find mailto links.

	/**
	 * Change the regular expression for mailto links in Lucid Email Encoder.
	 *
	 * @param string $regex Default regular expression.
	 * @return string
	 */
	function prefix_mailto_regex( $regex ) {
		return '/My custom regex/';
	}
	add_filter( 'leejl_mailto_regex', 'prefix_mailto_regex' );

-----

**leejl\_email\_regex**

Regular expression used to find email addresses.

	/**
	 * Change the regular expression for email addresses in Lucid Email Encoder.
	 *
	 * @param string $regex Default regular expression.
	 * @return string
	 */
	function prefix_email_regex( $regex ) {
		return '/My custom regex/';
	}
	add_filter( 'leejl_email_regex', 'prefix_email_regex' );

## Changelog

### 2.3: Mar 27, 2013

* Initial public release.