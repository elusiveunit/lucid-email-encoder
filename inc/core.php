<?php
/**
 * Core functionality and plugin setup.
 *
 * @package Lucid\EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Load translation and plugin parts depending on context.
 *
 * @package Lucid\EmailEncoder
 */
class Lucid_Email_Encoder_Core {

	/**
	 * Full path to plugin main file.
	 *
	 * @var string
	 */
	public static $plugin_file;

	/**
	 * Instances of some plugin classes.
	 *
	 * @var array
	 */
	private static $_instances = array();

	/**
	 * Constructor, add hooks.
	 *
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {
		self::$plugin_file = $file;
		$this->_load_toolbox();

		add_action( 'init', array( $this, 'load_translation' ), 1 );
		add_action( 'init', array( $this, 'load_plugin_parts' ) );
	}

	/**
	 * Activate Lucid Toolbox if needed.
	 */
	private function _load_toolbox() {

		// Only load in admin.
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) :
			require LEEJL_PLUGIN_PATH . 'inc/activate-toolbox.php';
			new Lucid_Email_Encoder_Activate_Toolbox( self::$plugin_file );
		endif;
	}

	/**
	 * Load translation.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'leejl', false, trailingslashit( dirname( plugin_basename( self::$plugin_file ) ) ) . 'lang/' );
	}

	/**
	 * Load the rest of the plugin.
	 */
	public function load_plugin_parts() {

		// Selectively load some parts, start with admin. Ajax the WordPress way
		// goes through admin-ajax, so is_admin alone isn't enough for proper
		// admin/template separation.
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) :

			// Settings
			require LEEJL_PLUGIN_PATH . 'inc/settings.php';

			// General admin
			require LEEJL_PLUGIN_PATH . 'inc/admin.php';
			self::$_instances['admin'] = new Lucid_Email_Encoder_Admin();

		// Frontend
		elseif ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) :

			// Encoder class
			require LEEJL_PLUGIN_PATH . 'classes/lucid-email-encoder.php';

			// Frontend
			require LEEJL_PLUGIN_PATH . 'inc/frontend.php';
			self::$_instances['frontend'] = new Lucid_Email_Encoder_Frontend();

		endif;
	}

	/**
	 * Get the class instance with specified ID.
	 *
	 * @see load_plugin_parts() For instance IDs.
	 * @param string $id ID of instance.
	 * @return object|bool Object instance if found, false otherwise.
	 */
	public static function get_instance( $id ) {
		return ( isset( self::$_instances[$id] ) ) ? self::$_instances[$id] : false;
	}
}