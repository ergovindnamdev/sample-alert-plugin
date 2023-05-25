<?php
/**
 * Alert Plugin setup
 *
 * @package Simple_Alert_Plugin
 * @since   1.0.0
 */
defined('ABSPATH') || exit;
/**
 * Main Alert Plugin Class.
 *
 * @class AlertPlugin
 */
class AlertPlugin
{
	/**
	 * The single instance of the class.
	 *
	 * @var AlertPlugin
	 * @since 2.1
	 */
	protected static $_instance = null;
	/**
	 * Main AlertPlugin Instance.
	 *
	 * Ensures only one instance of AlertPlugin is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return AlertPlugin - Main instance.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks()
	{
		register_activation_hook(ALERT_PLUGIN_FILE, array($this, 'activation_hook'));
		register_deactivation_hook(ALERT_PLUGIN_FILE, array($this, 'deactivation_hook'));
	}

	/**
	 * Plugin activation hook
	 */
	public function activation_hook()
	{

	}


	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes()
	{
		/**
		 * Class autoloader.
		 */
		include_once dirname(ALERT_PLUGIN_FILE) . '/includes/class-setting-sub-menus.php';
		include_once dirname(ALERT_PLUGIN_FILE) . '/includes/class-alert-frontend.php';

	}

	/**
	 * @return string
	 * Plugin URL
	 */
	public function plugin_url() {
		return untrailingslashit(plugins_url('/', ALERT_PLUGIN_FILE));
	}

	/**
	 * Plugin deactivation hook
	 */
	public function deactivation_hook()
	{

	}
}