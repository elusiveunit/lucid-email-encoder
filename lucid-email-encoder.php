<?php
/**
 * Lucid Email Encoder plugin definition.
 *
 * Plugin Name: Lucid Email Encoder
 * Plugin URI: https://github.com/elusiveunit/lucid-email-encoder
 * Description: Encodes email addresses to HTML entities, or to ROT13 strings requiring JavaScript. Originally based on <em>Email Address Encoder</em> by Till Krüss.
 * Author: Jens Lindberg
 * Version: 2.5.1
 * License: GPL-2.0+
 * Text Domain: leejl
 * Domain Path: /lang
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Symlink workaround, see http://core.trac.wordpress.org/ticket/16953
// The root check is to stop a fatal error on activation
$leejl_plugin_file = __FILE__;
$leejl_document_root = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT'] );
if ( isset( $plugin ) && false !== strpos( $plugin, $leejl_document_root ) )
	$leejl_plugin_file = $plugin;
elseif ( isset( $network_plugin ) && false !== strpos( $network_plugin, $leejl_document_root ) )
	$leejl_plugin_file = $network_plugin;

// Plugin constants
if ( ! defined( 'LEEJL_VERSION' ) )
	define( 'LEEJL_VERSION', '2.5.1' );

if ( ! defined( 'LEEJL_PLUGIN_URL' ) )
	define( 'LEEJL_PLUGIN_URL', trailingslashit( plugin_dir_url( $leejl_plugin_file ) ) );

if ( ! defined( 'LEEJL_PLUGIN_PATH' ) )
	define( 'LEEJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( $leejl_plugin_file ) ) );

// Load and initialize the plugin parts
require LEEJL_PLUGIN_PATH . 'inc/core.php';
$lucid_email_encoder_core = new Lucid_Email_Encoder_Core( $leejl_plugin_file );