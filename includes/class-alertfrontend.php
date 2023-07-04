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
class AlertFrontEnd {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'alert_frontend' ) );
	}

	/**
	 * Function for admin menu
	 *
	 * @return mixed
	 */
	public function alert_frontend() {
		global $post;
		$options   = get_option( 'alert_options' );
		$post_type = get_post_type( $post );
		if ( is_singular( $post_type ) ) {
			if ( ! empty( $options ) && is_array( $options['alert_posts'] ) && array_key_exists( $post_type, array_flip( $options['alert_posts'] ) ) ) {
				if ( ! empty( $options ) && is_array( $options[ 'alert_' . $post_type ] ) && array_key_exists( $post->ID, array_flip( $options[ 'alert_' . $post_type ] ) ) ) {
					?>
					<script type="text/javascript">
						alert("<?php echo esc_attr( $options['alert_message'] ); ?>");
					</script>
					<?php
				}
			}
		}
	}

}

new AlertFrontEnd();
