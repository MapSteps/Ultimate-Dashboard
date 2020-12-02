<?php
/**
 * CSS Enqueue.
 *
 * @package Ultimate Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_new_admin_page() || $module->screen()->is_edit_admin_page() ) {

		if ( apply_filters( 'udb_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Select2.
		wp_enqueue_style( 'select2', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/select2.min.css', array(), '4.1.0-beta.1' );

		// Icon picker.
		wp_enqueue_style( 'icon-picker', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/icon-picker.css', array(), ULTIMATE_DASHBOARD_PLUGIN_VERSION );

		// Heatbox.
		wp_enqueue_style( 'heatbox', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/heatbox.css', array(), ULTIMATE_DASHBOARD_PLUGIN_VERSION );

		// Toggle switch.
		wp_enqueue_style( 'udb-toggle-switch', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/toggle-switch.css', array(), ULTIMATE_DASHBOARD_PLUGIN_VERSION );

		// Edit admin page.
		wp_enqueue_style( 'udb-edit-admin-page', $module->url . '/assets/css/edit-admin-page.css', array(), ULTIMATE_DASHBOARD_PLUGIN_VERSION );


	} elseif ( $module->screen()->is_admin_page_list() ) {

		if ( apply_filters( 'udb_font_awesome', true ) ) {
			// Font Awesome.
			wp_enqueue_style( 'font-awesome', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/font-awesome.min.css', array(), '5.14.0' );
			wp_enqueue_style( 'font-awesome-shims', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/v4-shims.min.css', array(), '5.14.0' );
		}

		// Toggle switch.
		wp_enqueue_style( 'udb-toggle-switch', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/css/toggle-switch.css', array(), ULTIMATE_DASHBOARD_PLUGIN_VERSION );

	}

};
