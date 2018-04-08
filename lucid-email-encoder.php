<?php
/**
 * Lucid Email Encoder plugin definition.
 *
 * Plugin Name: Lucid Email Encoder
 * Plugin URI: https://github.com/elusiveunit/lucid-email-encoder
 * Description: Encodes email addresses to HTML entities, or to ROT13 strings requiring JavaScript. Originally based on <em>Email Address Encoder</em> by Till Krüss.
 * Author: Jens Lindberg
 * Version: 2.5.5
 * License: GPL-2.0+
 * Text Domain: leejl
 * Domain Path: /lang
 *
 * @package Lucid\EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LEEJL_VERSION' ) )
	define( 'LEEJL_VERSION', '2.5.5' );

if ( ! defined( 'LEEJL_PLUGIN_URL' ) )
	define( 'LEEJL_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LEEJL_PLUGIN_PATH' ) )
	define( 'LEEJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Load and initialize the plugin parts
require LEEJL_PLUGIN_PATH . 'inc/core.php';
$lucid_email_encoder_core = new Lucid_Email_Encoder_Core( __FILE__ );