<?php
/**
 * Admin filters.
 *
 * @package Lucid\EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Loads some misc. functionality like scripts and styles.
 *
 * @package Lucid\EmailEncoder
 */
class Lucid_Email_Encoder_Admin {

	/**
	 * Constructor, get options and add hooks.
	 */
	public function __construct() {
		$basename = plugin_basename( Lucid_Email_Encoder_Core::$plugin_file );

		add_action( 'admin_notices', array( $this, 'toolbox_notice' ) );
		add_filter( "plugin_action_links_{$basename}", array( $this, 'add_action_links' ) );
		add_action( 'load-settings_page_leejl_settings', array( $this, 'add_settings_page_hooks' ) );
	}

	/**
	 * Show a notice if Lucid Toolbox isn't activated.
	 *
	 * @global string $pagenow Current admin page.
	 */
	public function toolbox_notice() {
		global $pagenow;

		if ( 'plugins.php' == $pagenow && ! defined( 'LUCID_TOOLBOX_VERSION' ) )
			printf( '<div class="error"><p>%s</p></div>', __( 'Lucid Toolbox is needed for Lucid Email Encoder to function properly.', 'lucid-slider' ) );
	}

	/**
	 * Add a settings page link to the plugin action links.
	 *
	 * @param array $links Default meta links.
	 * @return array
	 */
	public function add_action_links( $links ) {

		// Only add link if user have access to the page
		if ( current_user_can( 'manage_options' ) ) :
			$url = esc_attr( trailingslashit( get_admin_url() ) . 'options-general.php?page=leejl_settings' );

			// Generally bad practice to rely on core strings, but I feel it's
			// unlikely this is ever untranslated. If it happens, it's a simple
			// update.
			$text = __( 'Settings' );

			$links['settings'] = "<a href=\"{$url}\">{$text}</a>";
		endif;

		return $links;
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
		<p><?php _e( 'If a message is set, it will be added before the email addresses if the JavaScript protection is selected. The message is wrapped in <code>&lt;span class="email-hidden-message"&gt;</code>', 'leejl' ); ?></p>

		<?php return ob_get_clean();
	}

	/**
	 * Sidebar for the contextual help tabs.
	 */
	private function _get_sidebar_content() {
		ob_start(); ?>

		<p><strong><?php _e( 'For more information:', 'leejl' ); ?></strong></p>
		<p><a href="https://github.com/elusiveunit/lucid-email-encoder" target="_blank"><?php _e( 'Lucid Email Encoder on Github', 'leejl' ); ?></a></p>

		<?php return ob_get_clean();
	}
}