<?php
/**
 * Plugin Name:     Simple Alert Plugin
 * Plugin URI:      www.futurebridge.com
 * Description:     This is a Simple Alert Plugin
 * Author:          Futurebridge
 * Author URI:      www.futurebridge.com
 * Text Domain:     simple-alert-plugin
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Simple_Alert_Plugin
 */


defined('ABSPATH') || exit;

if (!defined('ALERT_PLUGIN_FILE')) {
    define('ALERT_PLUGIN_FILE', __FILE__);
}

// Include the main class.
if (!class_exists('AlertPlugin', false)) {
    include_once dirname(ALERT_PLUGIN_FILE) . '/includes/class-alert-plugin.php';
}

/**
 * Returns the main instance of Alert Plugin.
 *
 * @since  2.1
 * @return Main Instance
 */
function alertPlugin() {
    return AlertPlugin::instance();
}

// Global for backwards compatibility.
$GLOBALS['alert-plugins'] = alertPlugin();