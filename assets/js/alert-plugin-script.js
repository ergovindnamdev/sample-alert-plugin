/**
 * Alert Plugin setup
 *
 * @package Simple_Alert_Plugin
 * @since   1.0.0
 */

jQuery( document ).ready(
	function () {
		jQuery( ".post_hide " ).hide();

		jQuery( ".postTypes_check" ).each(
			function () {
				jQuery( this ).click(
					function () {
						if (jQuery( this ).is( ":checked" )) {
							jQuery(
								".alert_" + jQuery( this ).val() + "_data_row.post_hide"
							).show();
						} else {
							jQuery(
								".alert_" + jQuery( this ).val() + "_data_row.post_hide"
							).hide();
						}
					}
				);

				if (jQuery( this ).is( ":checked" )) {
					jQuery(
						".alert_" + jQuery( this ).val() + "_data_row.post_hide"
					).show();
				}
			}
		);
	}
);
