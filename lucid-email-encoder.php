<?php
/**
 * Lucid Email Encoder definition.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

/*
Plugin Name: Lucid Email Encoder
Description: Encodes email addresses to HTML entities, or with ROT13 requiring JavaScript. Originally based on <em>Email Address Encoder</em> by Till Krüss.
Author: Jens Lindberg
Author URI: http://profiles.wordpress.org/elusiveunit/
Version: 2.3.0
*/

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LEEJL_VERSION' ) )
	define( 'LEEJL_VERSION', '2.3.0' );

if ( ! defined( 'LEEJL_PLUGIN_URL' ) )
	define( 'LEEJL_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LEEJL_PLUGIN_PATH' ) )
	define( 'LEEJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Load and initialize the plugin parts
require LEEJL_PLUGIN_PATH . 'inc/core.php';
$lucid_email_encoder_core = new Lucid_Email_Encoder_Core( __FILE__ );