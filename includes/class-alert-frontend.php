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
class AlertFrontEnd
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('wp_head', array($this, 'alert_frontend'));
    }

    /**
     * @return mixed
     * Function for admin menu
     */
    public function alert_frontend()
    {
        global $post;
        $options = get_option('alert_options');
        $postType = get_post_type($post);
        if (is_singular($postType)) {
            if (!empty($options) && is_array($options['alert_posts']) && array_key_exists($postType, array_flip($options['alert_posts']))) {
                if (!empty($options) && is_array($options['alert_' . $postType]) && array_key_exists($post->ID, array_flip($options['alert_' . $postType]))) {
                    ?>
                    <script type="text/javascript">
                        alert("<?php echo esc_attr($options['alert_message']); ?>");
                    </script>
                    <?php
                } 
            } 
        }
    }

}

new AlertFrontEnd();