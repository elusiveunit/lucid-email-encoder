<?php
/**
 * Fired when the plugin is uninstalled. Deletes options.
 *
 * @package Lucid
 * @subpackage EmailEncoder
 */

// Exit if the uninstall is not called from WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	die();

// Don't wanna clutter the database with an option!
delete_option( 'leejl_settings' );