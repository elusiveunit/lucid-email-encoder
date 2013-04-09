<?php
/**
 * Plugin settings.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Settings class
if ( defined( 'LUCID_TOOLBOX_CLASS' ) && ! class_exists( 'Lucid_Settings' ) )
	require LUCID_TOOLBOX_CLASS . 'lucid-settings.php';
elseif ( ! class_exists( 'Lucid_Settings' ) )
	return;

$leejl_settings = new Lucid_Settings( 'leejl_settings', __( 'Lucid Email Encoder', 'leejl' ) );

$leejl_settings->submenu( 'Lucid Email Encoder', array(
	'title' => __( 'Lucid Email Encoder', 'leejl' )
) );

/* -Protection section
-----------------------------------------------------------------------------*/
$leejl_settings->section( 'email_protection_section', array(
	'heading' => __( 'Email protection', 'leejl' ),
	'output' => '<p>' . __( 'Read more about the options in the help tab on this page.', 'leejl' ) . '</p>'
) );

$leejl_settings->field(
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

$leejl_settings->field(
	'no_js_text',
	__( 'Message for JavaScript', 'leejl' ),
	array(
		'type' => 'text',
		'section' => 'email_protection_section',
		'description' => __( 'Wrapped in &lt;span class="email-hidden-message"&gt;', 'leejl' )
	)
);

$leejl_settings->field(
	'no_js_handling',
	__( 'Handle JavaScript message', 'leejl' ),
	array(
		'type' => 'checkbox',
		'sanitize' => 'checkbox',
		'section' => 'email_protection_section',
		'inline_label' => __( 'Output JavaScript and CSS for the message', 'leejl' ),
		'default' => 0
	)
);

/* -Generator section
-----------------------------------------------------------------------------*/
$leejl_settings->section( 'manual_encoding_section', array(
	'heading' => __( 'Manual script tags', 'leejl' ),
	'output' => '<p>' . __( 'If the address is displayed in a place not filtered by Lucid Email Encoder, or if you want to include surrounding HTML, you can generate the necessary script here.', 'leejl' ) . '</p>'
) );

$leejl_settings->field(
	'manual_generator',
	__( 'Generate manual encoding', 'leejl' ),
	array(
		'type' => 'button_field',
		'button_text' => __( 'Generate', 'leejl' ),
		'section' => 'manual_encoding_section',
		'sanitize' => 'empty' // Strip everything
	)
);

$leejl_settings->html(
	'manual_generator',
	'<pre id="leejl-generator-output" class="hidden"></pre>'
);

$leejl_settings->init();
