<?php
/**
 * Alert Plugin setup
 *
 * @package Simple_Alert_Plugin
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Alert Plugin Class.
 *
 * @class AlertPlugin
 */
class SettingSubMenu {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'alert_settings_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Function for admin menu
	 *
	 * @return mixed
	 */
	public function admin_menu() {
		add_options_page( 'Alert Plugin Setting', 'Alert Plugin', 'manage_options', 'alert_plugins', array( $this, 'alert_plugin_fun' ), '' );
	}

	/**
	 * Function for add scripts
	 *
	 * @return mixed
	 */
	public function admin_scripts() {
		global $pagenow;
		if ( 'options-general.php' === $pagenow && ( isset( $_GET['page'] ) && 'alert_plugins' === $_GET['page'] ) ) {
			wp_enqueue_script( 'alert-plugin-script', alertPlugin()->plugin_url() . '/assets/js/alert-plugin-script.js', array( 'jquery' ), '1.0', true );
		}
	}

	/**
	 * Function for Alert Plugin Settings
	 *
	 * @return mixed
	 */
	public function alert_plugin_fun() {
		// check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check if the user have submitted the settings.
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'alert_group_messages', 'alert_group_message', __( 'Settings Saved', 'simple-alert-plugin' ), 'updated' );
		}
		?>
		<div class="wrap">
			<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
			</h1>
			<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wporg".
		settings_fields( 'alert_group' );
		// output setting sections and their fields.
		// (sections are registered for "wporg", each field is registered to a specific section).
		do_settings_sections( 'alert_group' );
		// output save settings button.
		submit_button( 'Save Settings' );
		?>
			</form>
		</div>
		<?php
	}


	/**
	 * Setting initialization.
	 */
	public function alert_settings_init() {
		// Regiter Settings for Alert Plugin.
		register_setting( 'alert_group', 'alert_options' );

		// Add settings section.
		add_settings_section(
			'alert_plugin_setting_section',
			'',
			'',
			'alert_group'
		);

		// Add settings section field.
		add_settings_field(
			'alert_plugin_message',
			__( 'Alert Message', 'simple-alert-plugin' ),
			array( $this, 'alert_plugin_message_fun' ),
			'alert_group',
			'alert_plugin_setting_section',
			array(
				'label_for'               => 'alert_message',
				'class'                   => 'alert_message_row',
				'alert_group_custom_data' => 'custom',
			)
		);

		// Add settings section field.
		add_settings_field(
			'alert_plugin_posts',
			__( 'Post Types', 'simple-alert-plugin' ),
			array( $this, 'alert_plugin_posts_fun' ),
			'alert_group',
			'alert_plugin_setting_section',
			array(
				'label_for'               => 'alert_posts',
				'class'                   => 'alert_post_type_row',
				'alert_group_custom_data' => 'custom',
			)
		);

		// Add Fields dynamically for post types.
		$post_types = $this->get_wp_post_types();
		foreach ( $post_types as $post_type ) {
			add_settings_field(
				'alert_plugin_' . $post_type . '_data',
				ucfirst( strtolower( $post_type ) ) . ' Data',
				function ( $args ) {
					$this->dynamic_posts_data( $args );
				},
				'alert_group',
				'alert_plugin_setting_section',
				array(
					'label_for'               => 'alert_' . $post_type,
					'class'                   => 'alert_' . $post_type . '_data_row post_hide',
					'alert_group_custom_data' => 'custom',
					'post_type'               => $post_type,
				)
			);
		}
	}

	/**
	 * Function for dynamically display post type data
	 *
	 * @param  array $args Need to pass post type.
	 * @return void
	 */
	public function dynamic_posts_data( $args ) {
		$post_type = $args['post_type'];
		$options   = get_option( 'alert_options' );

		$post_types = get_posts(
			array(
				'numberposts'      => -1,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => $post_type,
				'post_status'      => array( 'publish', 'inherit' ),
				'suppress_filters' => true,
			)
		);

		foreach ( $post_types as $postdata ) {
			$checked = '';
			if ( ! empty( $options ) && is_array( $options[ 'alert_' . $post_type ] ) > 0 && array_key_exists( $postdata->ID, array_flip( $options[ 'alert_' . $post_type ] ) ) ) {
				$checked = "checked='checked'";
			}
			?>
			<p >
			<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="alert_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="<?php echo esc_attr( $postdata->ID ); ?>" <?php echo esc_html( $checked ); ?>>
			<?php echo esc_attr( ucfirst( strtolower( $postdata->post_title ) ) ); ?>
			</p>    
			<?php

		}

	}

	/**
	 * Function for alert message section
	 *
	 * @param  array $args this is for alert message.
	 * @return void
	 */
	public function alert_plugin_message_fun( $args ) {
		$options = get_option( 'alert_options' );
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="alert_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			value="<?php ( ! empty( $options ) && '' !== $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : ''; ?>">
		<?php
	}

	/**
	 * Functin for display post types
	 *
	 * @param  array $args this is for display post types.
	 * @return void
	 */
	public function alert_plugin_posts_fun( $args ) {

		$options    = get_option( 'alert_options' );
		$post_types = $this->get_wp_postTypes();
		foreach ( $post_types as $post_type ) {
			$checked = '';
			if ( ! empty( $options ) && is_array( $options['alert_posts'] ) > 0 && array_key_exists( $post_type, array_flip( $options['alert_posts'] ) ) ) {
				$checked = "checked='checked'";
			}
			?>
			<p>
			<input class="postTypes_check" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="alert_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="<?php echo esc_attr( $post_type ); ?>" <?php echo esc_html( $checked ); ?>>
			<?php echo esc_attr( ucfirst( strtolower( $post_type ) ) ); ?>
			</p>
			<?php

		}
	}

	/**
	 * Function to get the post types
	 *
	 * @return string
	 */
	private function get_wp_post_types() {
		$args = array(
			'public' => true,
		);

		$post_types = get_post_types( $args, 'names' );
		return $post_types;
	}


}

new SettingSubMenu();
