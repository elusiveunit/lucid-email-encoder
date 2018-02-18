<?php
/**
 * Plugin settings.
 *
 * @package Lucid\EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Settings class
if ( defined( 'LUCID_TOOLBOX_CLASS' ) && ! class_exists( 'Lucid_Settings' ) )
	require LUCID_TOOLBOX_CLASS . 'lucid-settings.php';

/**
 * Add a custom settings page with options and code generation.
 *
 * @package Lucid\EmailEncoder
 */
class Lucid_Email_Encoder_Settings {

	/**
	 * The settings instance.
	 *
	 * @var Lucid_Settings
	 */
	protected $_s;

	/**
	 * Constructor, initialize settings.
	 */
	public function __construct() {
		if ( ! class_exists( 'Lucid_Settings' ) )
			return;

		// Create settings
		$this->_s = new Lucid_Settings( 'leejl_settings', __( 'Lucid Email Encoder', 'leejl' ) );
		$this->_s->submenu( 'Lucid Email Encoder', array(
			'title' => __( 'Lucid Email Encoder', 'leejl' )
		) );

		// Add sections and fields
		$this->_protection();
		$this->_generator();

		// Init settings
		$this->_s->init();
	}

	/**
	 * Protection section.
	 */
	protected function _protection() {
		$this->_s->section( 'email_protection_section', array(
			'heading' => __( 'Email protection', 'leejl' ),
			'output' => '<p>' . __( 'Read more about the options in the help tab on this page.', 'leejl' ) . '</p>'
		) );

		$this->_s->field(
			'active_email_protection',
			__( 'Automatic filtering', 'leejl' ),
			array(
				'type' => 'radios',
				'section' => 'email_protection_section',
				'sanitize' => 'no_html',
				'description' => __( 'The JavaScript option encodes entire mailto links, in addition to regular email addresses.', 'leejl' ),
				'options' => array(
					'none'     => _x( 'None', 'Automatic filtering', 'leejl' ),
					'entities' => __( 'Encode random parts to HTML entities', 'leejl' ),
					'script'   => __( 'Encode with JavaScript', 'leejl' )
				),
				'default' => 'entities'
			)
		);

		$this->_s->field(
			'no_js_text',
			__( 'Message for JavaScript', 'leejl' ),
			array(
				'type' => 'text',
				'section' => 'email_protection_section',
				'description' => __( 'Wrapped in &lt;span class="email-hidden-message"&gt;', 'leejl' )
			)
		);
	}

	/**
	 * Code generator section.
	 */
	protected function _generator() {
		$this->_s->section( 'manual_encoding_section', array(
			'heading' => __( 'Manual script tags', 'leejl' ),
			'output' => '<p>' . __( 'If the address is displayed in a place not filtered by Lucid Email Encoder, or if you want to include surrounding HTML, you can generate the necessary script here.', 'leejl' ) . '</p>'
		) );

		$this->_s->field(
			'manual_generator',
			__( 'Generate manual encoding', 'leejl' ),
			array(
				'type' => 'button_field',
				'button_text' => __( 'Generate', 'leejl' ),
				'section' => 'manual_encoding_section',
				'sanitize' => 'empty' // Strip everything
			)
		);

		$this->_s->html(
			'manual_generator',
			'<pre id="leejl-generator-output" class="hidden"></pre>'
		);
	}
}
