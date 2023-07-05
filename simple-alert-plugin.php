<?php
/**
 * Plugin Name:     Simple Alert Plugin
 * Plugin URI:      
 * Description:     This is a Simple Alert Plugin
 * Author:          Govind Namdev
 * Author URI:      
 * Text Domain:     simple-alert-plugin
 * Domain Path:     /languages
 * Version:         0.1.0
 * Requires PHP:    7.2
 * Requires WP:     6.0.0
 *
 * @package Simple_Alert_Plugin
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ALERT_PLUGIN_FILE' ) ) {
	define( 'ALERT_PLUGIN_FILE', __FILE__ );
}

// Include the main class.
if ( ! class_exists( 'AlertPlugin', false ) ) {
	include_once dirname( ALERT_PLUGIN_FILE ) . '/includes/class-alertplugin.php';
}

/**
 * Returns the main instance of Alert Plugin.
 *
 * @since  2.1
 * @return Main Instance
 */
function alert_plugin() {
	return AlertPlugin::instance();
}

// Global for backwards compatibility.
$GLOBALS['alert-plugins'] = alert_plugin();
