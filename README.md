# Lucid Email Encoder

[![devDependency Status](https://david-dm.org/elusiveunit/lucid-email-encoder/dev-status.svg)](https://david-dm.org/elusiveunit/lucid-email-encoder#info=devDependencies)

Automatically protect email addresses from harvesters with HTML entities or JavaScript.

Originally based on [Email Address Encoder](http://wordpress.org/extend/plugins/email-address-encoder/) by Till Krüss, Lucid Email Encoder takes it a step further by also offering JavaScript encoding. The plugin automatically searches for email addresses and mailto links in content, comments and widgets, and converts them according to settings.

A test by Silvan Mühlemann [comparing ways to obfuscate e-mail addresses](http://techblog.tilllate.com/2008/07/20/ten-methods-to-obfuscate-e-mail-addresses-compared/), shows that ROT13 encryption offers strong protection. This plugin also converts the at sign (@) to a pound sign (£), to harden it further. A link like `<a href="mailto:hi@me.com">Say hi</a>` would show up in the source as `<n uers="znvygb:uv£zr.pbz">Fnl uv</n>`.

If you don't want a JavaScript dependency, entity encoding would change the above link to something like `<a href="&#x6d;&#x61;&#105;lto&#x3a;&#x68;&#x69;&#64;me&#x2e;&#x63;&#x6f;m">Say hi</a>`

**Requires [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox)**, which is a plugin with a set of classes used to speed up and automate common tasks. This is kept as a separate plugin for easier development and updates. **This plugin will try to install and/or activate Lucid Toolbox** on plugin activation, if it's not available. It simply unzips a bundled version to the directory one level above its install location, if it's not there already, and runs `activate_plugin`.

Lucid Email Encoder is currently available in the following languages:

* English
* Swedish

## Basic usage

Just install it and choose what protection to use. Defaults to HTML entities.

### How can I add filtering where there is none?

There are several ways:

* Add an existing filter via `leejl_encoding_filters`, see 'Available hooks' section.
* Call the encoding methods on a string:
	* `Lucid_Email_Encoder::encode_to_script( 'My email: hi@mysite.com' );` for JavaScript.
	* `Lucid_Email_Encoder::encode_string( 'My email: hi@mysite.com' );` for HTML entities. Note that `encode_string` ignores strings with HTML, since I'm not sure every browser handles HTML tags encoded as entities.
	* `Lucid_Email_Encoder::search_and_encode( $some_content );` for when addresses can be anywhere in a string but the entire string shouldn't be encoded. `search_and_encode` takes a second boolean parameter, which decides if it encodes to script (true, default) or entities (false).
* Use the filters matching the encoding methods, listed under *Filter alternatives to encoding methods*.
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

**Regex filters**

If you want to change the regexes used for searching content, there are two filters:

* **leejl\_mailto\_regex**, used to find mailto links.
* **leejl\_email\_regex**, used to find email addresses.

**Don't include delimiters** in your versions; the regexes must be combinable. Forward slashes (`/`) are used as delimiters, so escape them if they're used.

	/**
	 * Change the regular expression for email addresses in Lucid Email Encoder.
	 *
	 * @param string $regex Default regular expression.
	 * @return string
	 */
	function prefix_mailto_regex( $regex ) {
		return 'My custom slash \/ regex';
	}
	add_filter( 'leejl_email_regex', 'prefix_mailto_regex' );

-----

**Filter alternatives to encoding methods**

These are applied to content, rather than hooked into.

* **lucid\_email\_encoder\_search**
* **lucid\_email\_encoder\_script**
* **lucid\_email\_encoder\_string**

They can be applied as an alternative to directly calling `Lucid_Email_Encoder::[method]`. The benefit is that a `class_exists` check becomes unnecessary, while a drawback would be the tiny performance hit of a few extra function calls.

	echo apply_filters( 'lucid_email_encoder_search', $content_from_somewhere );

	<p>Some content with a mailto <?php echo apply_filters( 'lucid_email_encoder_script', '<a href="mailto:hi@me.com">link</a>' ); ?></p>

	<p>Some content with a mailto <a href="<?php echo apply_filters( 'lucid_email_encoder_string', 'mailto:hi@me.com' ); ?>">link</a></p>

## Changelog

### 2.5.2: Feb 18, 2015

* Remove: The workaround for `__FILE__` in symlinked plugins is no longer needed as of WordPress 3.9.

### 2.5.1: Dec 09, 2013

* Tweak/fix: Include [this](https://gist.github.com/aubreypwd/7828624) temporary workaround for the issue with `__FILE__` in symlinked plugins, see [trac ticket #16953](http://core.trac.wordpress.org/ticket/16953).

### 2.5.0: Oct 14, 2013

* New: Add filters matching the encoding methods: `lucid_email_encoder_search`, `lucid_email_encoder_script` and `lucid_email_encoder_string`.
* Tweak: Thoroughly walk through and tweak the regexes. They are now expanded with comments, more robust and generally cleaned up.
* Tweak: The regexes now use the **i** (which should've been included right from the start) and **x** (because of the change above) flags. Read more about the flags in the [PHP manual](http://php.net/manual/en/reference.pcre.pattern.modifiers.php), if you're unfamiliar with them.

### 2.4.1: Sep 17, 2013

* Fix: Change the dot (all) to 'anything but greater-than sign' in the mailto regex, to stop anchors not separated by a line break from getting included in the same block.

### 2.4.0: Aug 11, 2013

* Tweak: Simplify wrapping elements and decoding approach for JavaScript decoding. Previously, addresses got encoded and wrapped in script tags, optionally with the no-JS message preceeding it (span + script). The decoded address was inserted before the script tag and js/no-js classes with CSS were required to handle the message. Everything is now wrapped in in a span and its content is simply replaced when decoded. This also seem to have fixed the IE spacing issues.
* Tweak: The span CSS classes are now 'lucid-email-encoded' and 'lucid-email-decoded'.
* Tweak: Remove non-ASCII matching range from regex... don't copy and paste without careful inspection kids!

### 2.3.0: Mar 27, 2013

* Initial public release.