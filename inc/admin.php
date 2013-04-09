<?php
/**
 * Admin filters.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Loads some misc. functionality like scripts and styles.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */
class Lucid_Email_Encoder_Admin {

	/**
	 * Constructor, get options and add hooks.
	 */
	public function __construct() {
		add_action( 'load-settings_page_leejl_settings', array( $this, 'add_settings_page_hooks' ) );
		add_action( 'admin_notices', array( $this, 'toolbox_notice' ) );
	}

	/**
	 * Show a notice if Lucid Toolbox isn't activated.
	 */
	public function toolbox_notice() {
		global $pagenow;
		
		if ( 'plugins.php' == $pagenow
		  &&  ! is_plugin_active( 'lucid-toolbox/lucid-toolbox.php' ) )
			printf( '<div class="error"><p>%s</p></div>', __( 'Lucid Toolbox is needed for Lucid Email Encoder to function properly.', 'leejl' ) );
	}

	/**
	 * Add hooks only to Lucid Email Encoder settings screen.
	 */
	public function add_settings_page_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
		add_action( 'admin_head', array( $this, 'help_tabs' ) );
	}

	/**
	 * Add scripts.
	 */
	public function admin_scripts() {
		wp_enqueue_script( 'leejl-script-generator', LEEJL_PLUGIN_URL . 'js/generate-script.min.js', array( 'jquery' ), null, true );
	}

	/**
	 * Add styles.
	 */
	public function admin_styles() { ?>
		<style>
			#leejl-generator-output {
				max-width: 900px;
				padding: 1em;
				border: 1px solid #ccc;
				border-radius: 3px;
				background: #f1f1f1;
				white-space: normal;
				word-wrap: break-word;
			}
			#leejl-generator-output.hidden {display: none; visibility: hidden;}
		</style>
		<?php
	}

	/**
	 * Add help tabs.
	 */
	public function help_tabs() {
		$screen = get_current_screen();

		$settings_tab_content = $this->_get_settings_tab_content();
		$screen->add_help_tab( array(
			'id' => 'leejl-settings-help-tab',
			'title' => __( 'Settings', 'leejl' ),
			'content' => $settings_tab_content
		) );

		$sidebar_content = $this->_get_sidebar_content();
		$screen->set_help_sidebar( $sidebar_content );
	}

	/**
	 * Settings help tab content.
	 */
	private function _get_settings_tab_content() {
		ob_start(); ?>

		<h3><?php _e( 'Automatic filtering', 'leejl' ); ?></h3>
		<p><?php _e( 'Choose if email addresses in certain content blocks should be filtered automatically. The default filters are <code>the_content</code>, <code>the_excerpt</code>, <code>widget_text</code>, <code>comment_text</code> and <code>comment_excerpt</code>. If disabled, the JavaScript decoding functions will still be available for manual use with the script generator.', 'leejl' ); ?></p>

		<h3><?php _e( 'Message for JavaScript', 'leejl' ); ?></h3>
		<p><?php _e( 'If a message is set, it will be added before the email addresses if the JavaScript protection is selected. This requires handling in the theme\'s CSS or the option below, to show and hide when appropriate. The message is wrapped in <code>&lt;span class="email-hidden-message"&gt;</code>', 'leejl' ); ?></p>

		<h3><?php _e( 'Handle JavaScript message', 'leejl' ); ?></h3>
		<p><?php _e( 'If activated, a .js class will be added to the html element (as long as none is found) and a CSS rule will be inserted:', 'leejl' ); ?></p>

		<p><code>&lt;script&gt;(function(d){var c=d.className;c.match(/\bjs\b/)||(d.className=c+' js')}(document.documentElement));&lt;/script&gt;</code><br>
		<code>&lt;style&gt;.js .email-hidden-message{display:none;visibility:hidden}&lt;/style&gt;</code></p>

		<p><?php _e( '<strong>Will not work if there is a js-like class already</strong> (like some-js, js-here). A more robust way would be to add the class \'no-js\' to the html element, add the CSS rule to the theme and use the snippet below in the &lt;head&gt; (preferably high up like below the title):', 'leejl' ); ?></p>

		<p><code>&lt;script&gt;(function(d){d.className=d.className.replace(/(\s|^)no-js(\s|$)/,'$1js$2')}(document.documentElement));&lt;/script&gt;</code></p>

		<p><?php printf( __( 'A third option, which is also very useful for theme development, is to use <a href="%s" target="_blank">Modernizr</a>.', 'leejl' ), 'http://modernizr.com/' ); ?></p>

		<?php return ob_get_clean();
	}

	/**
	 * Sidebar for the contextual help tabs.
	 */
	private function _get_sidebar_content() {
		ob_start(); ?>

		<p><strong><?php _e( 'For more information:', 'leejl' ); ?></strong></p>
		<p><a href="http://codex.wordpress.org/Media_Library_Screen" target="_blank"><?php _e( 'Documentation on Media Library', 'leejl' ); ?></a></p>
		<p><a href="http://wordpress.org/support/" target="_blank"><?php _e( 'Support Forums', 'leejl' ); ?></a></p>

		<?php return ob_get_clean();
	}
}