<?php
/**
 * Login Customizer module.
 *
 * @package Ultimate_Dashboard
 */

namespace Udb\LoginCustomizer;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Udb\Base\Module as Base_Module;

/**
 * Class to setup login customizer module.
 */
class Module extends Base_Module {

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ULTIMATE_DASHBOARD_PLUGIN_URL . '/modules/login-customizer';

	}

	/**
	 * Setup login customizer module.
	 */
	public function setup() {

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );

		// Create custom page (custom rewrite, not a real page).
		add_action( 'init', array( $this, 'rewrite_tags' ) );
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_action( 'wp', array( $this, 'set_custom_page' ) );

		// Setup redirect.
		add_action( 'init', array( $this, 'redirect_frontend_page' ) );
		add_action( 'admin_init', array( $this, 'redirect_edit_page' ) );

		// Setup customizer.
		add_action( 'customize_register', array( $this, 'register_panels' ) );
		add_action( 'customize_register', array( $this, 'register_sections' ) );
		add_action( 'customize_register', array( $this, 'register_controls' ) );

		// Enqueue assets.
		add_action( 'customize_controls_print_styles', array( $this, 'control_styles' ), 99 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'control_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'preview_styles' ), 99 );
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ) );

		// The module output.
		require_once __DIR__ . '/class-output.php';
		$output = new Output();
		$output->setup();

	}

	/**
	 * Add "Login Customizer" submenu under "Ultimate Dashboard" menu item.
	 */
	public function submenu_page() {

		global $submenu;

		$udb_slug = 'edit.php?post_type=udb_widgets';

		// E.g: subscriber got error if we don't return.
		if ( ! isset( $submenu[ $udb_slug ] ) ) {
			return;
		}

		array_push(
			$submenu[ $udb_slug ],
			array(
				__( 'Login Customizer', 'ultimate-dashboard' ),
				apply_filters( 'udb_settings_capability', 'manage_options' ),
				esc_url( admin_url( 'customize.php?autofocus%5Bpanel%5D=udb_login_customizer_panel' ) ),
			)
		);

	}

	/**
	 * Register rewrite tags.
	 *
	 * @return void
	 */
	public function rewrite_tags() {

		add_rewrite_tag( '%udb-login-customizer%', '([^&]+)' );

	}

	/**
	 * Register rewrite rules.
	 *
	 * @return void
	 */
	public function rewrite_rules() {

		// Rewrite rule for "udb-login-customizer" page.
		add_rewrite_rule(
			'^udb-login-customizer/?',
			'index.php?pagename=udb-login-customizer',
			'top'
		);

		// Flush the rewrite rules if it hasn't been flushed.
		if ( ! get_option( 'udb_login_customizer_flush_url' ) ) {
			flush_rewrite_rules( false );
			update_option( 'udb_login_customizer_flush_url', 1 );
		}

	}

	/**
	 * Set page manually by modifying 404 page.
	 *
	 * @return void
	 */
	public function set_custom_page() {

		// Only modify 404 page.
		if ( ! is_404() ) {
			return;
		}

		$pagename = sanitize_text_field( get_query_var( 'pagename' ) );

		// Only set for intended page.
		if ( 'udb-login-customizer' !== $pagename ) {
			return;
		}

		// If user is not logged-in, then redirect.
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( home_url() );
			exit;
		} else {
			// Only allow user with 'manage_options' capability.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( home_url() );
				exit;
			}
		}

		status_header( 200 );
		load_template( __DIR__ . '/templates/udb-login-page.php', true );
		exit;

	}

	/**
	 * Redirect "Login Customizer" frontend page to WordPress customizer page.
	 */
	public function redirect_frontend_page() {

		if ( ! isset( $_GET['page'] ) || 'udb-login-page' !== $_GET['page'] ) {
			return;
		}

		// Pull the Login Designer page from options.
		$page = get_page_by_path( 'udb-login-page' );

		// Generate the redirect url.
		$redirect_url = add_query_arg(
			array(
				'autofocus[panel]' => 'udb_login_customizer_panel',
				'url'              => rawurlencode( get_permalink( $page ) ),
			),
			admin_url( 'customize.php' )
		);

		wp_safe_redirect( $redirect_url );

	}

	/**
	 * Redirect "Login Customizer" edit page to WordPress customizer page.
	 */
	public function redirect_edit_page() {

		global $pagenow;

		// Pull the Login Designer page from options.
		$page = get_page_by_path( 'udb-login-page' );

		if ( ! $page ) {
			return;
		}

		// Generate the redirect url.
		$redirect_url = add_query_arg(
			array(
				'autofocus[panel]' => 'udb_login_customizer_panel',
				'url'              => rawurlencode( get_permalink( $page ) ),
			),
			admin_url( 'customize.php' )
		);

		if ( 'post.php' === $pagenow && ( isset( $_GET['post'] ) && intval( $page->ID ) === intval( $_GET['post'] ) ) ) {
			wp_safe_redirect( $redirect_url );
		}

	}

	/**
	 * Register "Login Customizer" panel in WP Customizer.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_panels( $wp_customize ) {

		$wp_customize->add_panel(
			'udb_login_customizer_panel',
			array(
				'title'      => __( 'Login Customizer', 'ultimate-dashboard' ),
				'capability' => 'manage_options',
				'priority'   => 30,
			)
		);

	}

	/**
	 * Register login customizer's sections in WP Customizer.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_sections( $wp_customize ) {

		$add_sections = require_once __DIR__ . '/inc/add-sections.php';
		$add_sections( $wp_customize );

	}

	/**
	 * Register customizer controls.
	 *
	 * @param WP_Customize $wp_customize The WP_Customize instance.
	 */
	public function register_controls( $wp_customize ) {

		// Customize control classes.
		require __DIR__ . '/controls/class-udb-customize-control.php';
		require __DIR__ . '/controls/class-udb-customize-pro-control.php';
		require __DIR__ . '/controls/class-udb-customize-range-control.php';
		require __DIR__ . '/controls/class-udb-customize-image-control.php';
		require __DIR__ . '/controls/class-udb-customize-color-control.php';
		require __DIR__ . '/controls/class-udb-customize-login-template-control.php';

		$branding         = get_option( 'udb_branding', array() );
		$branding_enabled = isset( $branding['enabled'] ) ? true : false;
		$accent_color     = isset( $branding['accent_color'] ) ? $branding['accent_color'] : '';
		$has_accent_color = $branding_enabled && ! empty( $accent_color ) ? true : false;

		// Register login customizer's settings & controls in WP Customizer.
		require __DIR__ . '/sections/template.php';
		require __DIR__ . '/sections/logo.php';
		require __DIR__ . '/sections/bg.php';
		require __DIR__ . '/sections/layout.php';
		require __DIR__ . '/sections/fields.php';
		require __DIR__ . '/sections/labels.php';
		require __DIR__ . '/sections/button.php';
		require __DIR__ . '/sections/form-footer.php';

	}

	/**
	 * Enqueue the login customizer control styles.
	 */
	public function control_styles() {

		wp_enqueue_style( 'udb-login-customizer', $this->url . '/assets/css/controls.css', null, ULTIMATE_DASHBOARD_PLUGIN_VERSION );

	}

	/**
	 * Enqueue login customizer control scripts.
	 */
	public function control_scripts() {

		wp_enqueue_script( 'udb-login-customizer-control', $this->url . '/assets/js/controls.js', array( 'customize-controls' ), ULTIMATE_DASHBOARD_PLUGIN_VERSION, true );

		wp_enqueue_script( 'udb-login-customizer-events', $this->url . '/assets/js/preview.js', array( 'customize-controls' ), ULTIMATE_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'customize-controls',
			'udbLoginCustomizer',
			$this->create_js_object()
		);

	}

	/**
	 * Login customizer's localized JS object.
	 *
	 * @return array The login customizer's localized JS object.
	 */
	public function create_js_object() {

		return array(
			'homeUrl'      => home_url(),
			'loginPageUrl' => home_url( 'udb-login-customizer' ),
			'pluginUrl'    => rtrim( ULTIMATE_DASHBOARD_PLUGIN_URL, '/' ),
			'moduleUrl'    => ULTIMATE_DASHBOARD_PLUGIN_URL . '/modules/login-customizer',
			'assetUrl'     => $this->url . '/assets',
			'wpLogoUrl'    => admin_url( 'images/wordpress-logo.svg?ver=' . ULTIMATE_DASHBOARD_PLUGIN_VERSION ),
		);

	}

	/**
	 * Enqueue styles to login customizer preview styles.
	 */
	public function preview_styles() {

		if ( ! is_customize_preview() ) {
			return;
		}

		wp_enqueue_style( 'udb-login-customizer-hint', $this->url . '/assets/css/hint.css', ULTIMATE_DASHBOARD_PLUGIN_VERSION, 'all' );

		wp_enqueue_style( 'udb-login-customizer-preview', $this->url . '/assets/css/preview.css', ULTIMATE_DASHBOARD_PLUGIN_VERSION, 'all' );

	}

	/**
	 * Enqueue scripts to login customizer preview scripts.
	 */
	public function preview_scripts() {

		wp_enqueue_script( 'udb-login-customizer-preview', $this->url . '/assets/js/preview.js', array( 'customize-preview' ), ULTIMATE_DASHBOARD_PLUGIN_VERSION, true );

		wp_enqueue_script( 'udb-login-customizer-hints', $this->url . '/assets/js/hints.js', array( 'customize-preview' ), ULTIMATE_DASHBOARD_PLUGIN_VERSION, true );

		wp_localize_script(
			'customize-preview',
			'udbLoginCustomizer',
			$this->create_js_object()
		);

	}

}
