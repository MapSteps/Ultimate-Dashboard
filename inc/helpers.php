<?php
/**
 * Helpers.
 *
 * @package Ultimate Dashboard PRO
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Get all dashboard widgets array.
 *
 * Returns all widgets that are registered in a complex array.
 *
 * @return array The dashboard widgets.
 */
function udb_get_db_widgets() {

	global $wp_meta_boxes;

	if ( ! isset( $wp_meta_boxes['dashboard'] ) || ! is_array( $wp_meta_boxes['dashboard'] ) ) {

		require_once ABSPATH . '/wp-admin/includes/dashboard.php';

		$current_screen = get_current_screen();

		set_current_screen( 'dashboard' );

		remove_action( 'wp_dashboard_setup', 'udb_remove_default_dashboard_widgets', 100 );

		wp_dashboard_setup();

		add_action( 'wp_dashboard_setup', 'udb_remove_default_dashboard_widgets', 100 );

		set_current_screen( $current_screen );

	}

	$widgets = $wp_meta_boxes['dashboard'];

	return $widgets;

}

/**
 * Get actual dashboard widgets.
 *
 * Strips down the array above to get the actual dashboard widgets array.
 *
 * @return array The dashboard widgets.
 */
function udb_get_widgets() {

	$widgets      = udb_get_db_widgets();
	$flat_widgets = array();

	foreach ( $widgets as $context => $priority ) {

		foreach ( $priority as $data ) {

			foreach ( $data as $id => $widget ) {

				$widget['title_stripped'] = wp_strip_all_tags( $widget['title'] );
				$widget['context']        = $context;

				$flat_widgets[ $id ] = $widget;

			}
		}
	}

	$widgets = wp_list_sort( $flat_widgets, array( 'title_stripped' => 'ASC' ), null, true );

	return $widgets;

}

/**
 * Get default widgets.
 *
 * From all existing widgets, get the default widgets.
 *
 * @return array The default widgets.
 */
function udb_get_default_widgets() {

	$widgets = udb_get_widgets();

	$default_widgets = array(
		'dashboard_primary'         => array(),
		'dashboard_quick_press'     => array(),
		'dashboard_right_now'       => array(),
		'dashboard_activity'        => array(),
		'dashboard_incoming_links'  => array(),
		'dashboard_plugins'         => array(),
		'dashboard_secondary'       => array(),
		'dashboard_recent_drafts'   => array(),
		'dashboard_recent_comments' => array(),
		'dashboard_php_nag'         => array(),
		'dashboard_site_health'     => array(),
	);

	$widgets = array_intersect_key( $widgets, $default_widgets );

	return $widgets;

}

/**
 * Get saved default widgets.
 *
 * @return array The saved default widgets.
 */
function udb_get_saved_default_widgets() {

	$widgets = udb_get_widgets();

	if ( get_option( 'udb_settings' ) ) {
		$settings = get_option( 'udb_settings' );
	} else {
		$settings = array();
	}

	$widgets = array_intersect_key( $widgets, $settings );

	return $widgets;

}

/**
 * Get 3rd party widgets.
 *
 * From all existing widgets, get the 3rd party widgets.
 *
 * @return array The 3rd party widgets.
 */
function udb_get_third_party_widgets() {

	$widgets = udb_get_widgets();

	$default_widgets = array(
		'dashboard_primary'         => array(),
		'dashboard_quick_press'     => array(),
		'dashboard_right_now'       => array(),
		'dashboard_activity'        => array(),
		'dashboard_incoming_links'  => array(),
		'dashboard_plugins'         => array(),
		'dashboard_secondary'       => array(),
		'dashboard_recent_drafts'   => array(),
		'dashboard_recent_comments' => array(),
		'dashboard_php_nag'         => array(),
		'dashboard_site_health'     => array(),
	);

	$udb_widgets = array();
	foreach ( $widgets as $key => $value ) {
		if ( strpos( $key, 'ms-udb' ) === 0 ) {
			$udb_widgets[ $key ] = $value;
		}
	}

	$widgets = array_diff_key( $widgets, $udb_widgets, $default_widgets );

	return $widgets;
}

/**
 * Image sanitization callback.
 *
 * Checks the image's file extension and mime type against a whitelist. If they're allowed,
 * send back the filename, otherwise, return the setting default.
 *
 * - Sanitization: image file extension
 * - Control: text, WP_Customize_Image_Control
 *
 * @see wp_check_filetype() https://developer.wordpress.org/reference/functions/wp_check_filetype/
 *
 * @version 1.2.2
 *
 * @param string               $image   Image filename.
 * @param WP_Customize_Setting $setting Setting instance.
 *
 * @return string The image filename if the extension is allowed; otherwise, the setting default.
 */
function udb_sanitize_image( $image, $setting ) {

	/**
	 * Array of valid image file types.
	 *
	 * The array includes image mime types that are included in wp_get_mime_types()
	 */
	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png',
		'bmp'          => 'image/bmp',
		'tif|tiff'     => 'image/tiff',
		'ico'          => 'image/x-icon',
	);

	// Allowed svg mime type in version 1.2.2.
	$allowed_mime   = get_allowed_mime_types();
	$svg_mime_check = isset( $allowed_mime['svg'] ) ? true : false;

	if ( $svg_mime_check ) {
		$allow_mime = array( 'svg' => 'image/svg+xml' );
		$mimes      = array_merge( $mimes, $allow_mime );
	}

	// Return an array with file extension and mime_type.
	$file = wp_check_filetype( $image, $mimes );

	// If $image has a valid mime_type, return it; otherwise, return the default.
	return esc_url_raw( ( $file['ext'] ? $image : $setting->default ) );

}
