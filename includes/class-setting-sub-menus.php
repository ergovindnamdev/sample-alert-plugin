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
class SettingSubMenu
{
    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'alert_settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    /**
     * @return mixed
     * Function for admin menu
     */
    public function admin_menu() {
        add_options_page("Alert Plugin Setting", "Alert Plugin", "manage_options", "alert_plugins", array($this, "alert_plugin_fun"), "");
    }

    /**
     * @return mixed
     * Function for add scripts
     */
    public function admin_scripts() {
        global $pagenow;
        if($pagenow == "options-general.php" && $_GET["page"] == "alert_plugins") {;
            wp_enqueue_script('alert-plugin-script', alertPlugin()->plugin_url() . '/assets/js/alert-plugin-script.js', array('jquery'), '1.0', true);
        }
    }
    
    /**
     * @return mixed 
     * Function for Alert Plugin Settings
     */
    public function alert_plugin_fun() {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // check if the user have submitted the settings
        if (isset($_GET['settings-updated'])) {
            add_settings_error('alert_group_messages', 'alert_group_message', __('Settings Saved', 'simple-alert-plugin'), 'updated');
        }
        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wporg"
                settings_fields('alert_group');
                // output setting sections and their fields
                // (sections are registered for "wporg", each field is registered to a specific section)
                do_settings_sections('alert_group');
                // output save settings button
                submit_button('Save Settings');
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
        register_setting('alert_group', 'alert_options');

        // Add settings section
        add_settings_section(
            'alert_plugin_setting_section',
            "",
            '',
            'alert_group'
        );

        // Add settings section field
        add_settings_field(
            'alert_plugin_message',
            __('Alert Message', 'simple-alert-plugin'),
            array($this, 'alert_plugin_message_fun'),
            'alert_group',
            'alert_plugin_setting_section',
            array(
                'label_for' => 'alert_message',
                'class' => 'alert_message_row',
                'alert_group_custom_data' => 'custom',
            )
        );

        // Add settings section field
        add_settings_field(
            'alert_plugin_posts',
            __('Post Types', 'simple-alert-plugin'),
            array($this, 'alert_plugin_posts_fun'),
            'alert_group',
            'alert_plugin_setting_section',
            array(
                'label_for' => 'alert_posts',
                'class' => 'alert_post_type_row',
                'alert_group_custom_data' => 'custom',
            )
        );

        // Add Fields dynamically for post types
        $postTypes = $this->get_wp_postTypes();
        foreach ($postTypes as $postType) {
            add_settings_field(
                'alert_plugin_'.$postType.'_data',
                __( ucfirst(strtolower($postType)).' Data', 'simple-alert-plugin'),
                function($args){
                    $this->dynamic_posts_data($args);
                },
                'alert_group',
                'alert_plugin_setting_section',
                array(
                    'label_for' => 'alert_'.$postType,
                    'class' => 'alert_'.$postType.'_data_row post_hide',
                    'alert_group_custom_data' => 'custom',
                    'post_type' => $postType
                )
            );
        }
    }

    /**
     * @args array
     * @return string
     * Function for dynamically display post type data
     */
    public function dynamic_posts_data($args) {
        $postType = $args['post_type'];
        $options = get_option('alert_options');
        
        $postTypes = get_posts(array(
            'numberposts'      => -1,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => $postType,
            'post_status'      => array('publish', 'inherit'),
            'suppress_filters' => true,
        ));
        
         foreach ($postTypes as $postdata) {
             $checked = "";
             if (!empty($options) && is_array($options['alert_'.$postType]) > 0 && array_key_exists($postdata->ID, array_flip($options['alert_'.$postType]))) {
                 $checked = "checked='checked'";
             }
            ?>
            <p >
            <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>"
                name="alert_options[<?php echo esc_attr($args['label_for']); ?>][]" value="<?php echo esc_attr($postdata->ID) ?>" <?php echo $checked; ?>>
            <?php echo esc_attr(ucfirst(strtolower($postdata->post_title))); ?>
            </p>    
            <?php

        }
       
    }

    /**
     * @args array
     * @return string
     * Function for alert message section
     */
    public function alert_plugin_message_fun($args) {
        $options = get_option('alert_options');
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>"
            name="alert_options[<?php echo esc_attr($args['label_for']); ?>]"
            value="<?php (!empty($options) && $options[$args['label_for']] !== "") ? _e($options[$args['label_for']], 'simple-alert-plugin') : "" ?>">
        <?php
    }

    /**
     * @args array
     * @return string
     * Functin for display post types
     */
    public function alert_plugin_posts_fun($args) {

        $options = get_option('alert_options');
        $postTypes = $this->get_wp_postTypes();
        foreach ($postTypes as $postType) {
            $checked = "";
            if (!empty($options) && is_array($options['alert_posts']) > 0 && array_key_exists($postType, array_flip($options['alert_posts']))) {
                $checked = "checked='checked'";
            }
            ?>
            <p>
            <input class="postTypes_check" type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>"
                name="alert_options[<?php echo esc_attr($args['label_for']); ?>][]" value="<?php echo esc_attr($postType) ?>" <?php echo $checked; ?>>
            <?php echo esc_attr(ucfirst(strtolower($postType))) ?>
            </p>
            <?php

        }
    }

    /**
     * @return string
     * Function to get the post types
     */
    private function get_wp_postTypes() {
        $args = array(
            'public' => true,
        );

        return $post_types = get_post_types($args, "names");
    }


}

new SettingSubMenu();