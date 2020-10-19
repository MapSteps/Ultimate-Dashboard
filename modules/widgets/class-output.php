<?php
/**
 * Branding output.
 *
 * @package Ultimate_Dashboard
 */

namespace Udb\Widgets;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use WP_Query;
use Udb\Base\Output as Base_Output;

/**
 * Class to setup widgets output.
 */
class Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ULTIMATE_DASHBOARD_PLUGIN_URL . '/modules/widgets';

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup widgets output.
	 */
	public function setup() {

		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'add_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( self::get_instance(), 'remove_default_dashboard_widgets' ), 100 );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_styles' ), 100 );

	}

	/**
	 * Add dashboard widgets.
	 *
	 * @param array $user_roles Current user roles.
	 */
	public function add_dashboard_widgets( $user_roles = array() ) {

		$current_user = wp_get_current_user();

		if ( empty( $user_roles ) ) {
			$user_roles = $current_user->roles;
		}

		$user_roles = apply_filters( 'udb_widget_user_roles', $user_roles ); // anywhere else we refer to "widget" or "widgets". Here we refer to dashboard.

		$args = array(
			'post_type'      => 'udb_widgets',
			'posts_per_page' => 100,
			'meta_key'       => 'udb_is_active',
			'meta_value'     => 1,
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) :

			$loop->the_post();

			$post_id     = get_the_ID();
			$title       = get_the_title();
			$icon        = get_post_meta( $post_id, 'udb_icon_key', true );
			$link        = get_post_meta( $post_id, 'udb_link', true );
			$target      = get_post_meta( $post_id, 'udb_link_target', true );
			$tooltip     = get_post_meta( $post_id, 'udb_tooltip', true );
			$position    = get_post_meta( $post_id, 'udb_position_key', true );
			$priority    = get_post_meta( $post_id, 'udb_priority_key', true );
			$widget_type = get_post_meta( $post_id, 'udb_widget_type', true );
			$output      = '';

			// Preventing edge case when widget_type is empty.
			if ( ! $widget_type ) {

				do_action( 'udb_compat_widget_type', $post_id );

			}

			$allow_access = apply_filters( 'udb_allow_widget_access', true, $post_id, $user_roles );

			if ( ! $allow_access ) {
				continue;
			}

			if ( 'html' === $widget_type ) {

				$html   = get_post_meta( $post_id, 'udb_html', true );
				$output = do_shortcode( '<div class="udb-html-wrapper">' . $html . '</div>' );

			} elseif ( 'text' === $widget_type ) { // Text widget output.

				$content       = get_post_meta( $post_id, 'udb_content', true );
				$contentheight = get_post_meta( $post_id, 'udb_content_height', true ) ? ' data-udb-content-height="' . get_post_meta( $post_id, 'udb_content_height', true ) . '"' : '';

				$output = do_shortcode( '<div class="udb-content-wrapper"' . $contentheight . '>' . wpautop( $content ) . '</div>' );

			} elseif ( 'icon' === $widget_type ) { // Icon widget output.

				$output = '<a href="' . $link . '" target="' . $target . '"><i class="' . $icon . '"></i></a>';

				if ( $tooltip ) {
					$output .= '<i class="udb-info"></i><div class="udb-tooltip"><span>' . $tooltip . '</span></div>';
				}
			}

			$output_args = array(
				'id'          => $post_id,
				'title'       => $title,
				'position'    => $position,
				'priority'    => $priority,
				'widget_type' => $widget_type,
			);

			$output = apply_filters( 'udb_widget_output', $output, $output_args );

			$output_callback = function() use ( $output ) {
				echo $output;
			};

			// Add metabox.
			add_meta_box( 'ms-udb' . $post_id, $title, $output_callback, 'dashboard', $position, $priority );

		endwhile;

	}

	/**
	 * Remove default WordPress dashboard widgets.
	 */
	public function remove_default_dashboard_widgets() {

		$saved_widgets   = $this->widget()->get_saved_default();
		$default_widgets = $this->widget()->get_default();
		$settings        = get_option( 'udb_settings' );

		if ( isset( $settings['remove-all'] ) ) {

			remove_action( 'welcome_panel', 'wp_welcome_panel' );

			foreach ( $default_widgets as $id => $widget ) {
				remove_meta_box( $id, 'dashboard', $widget['context'] );
			}
		} else {

			if ( isset( $settings['welcome_panel'] ) ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
			}

			foreach ( $saved_widgets as $id => $widget ) {
				remove_meta_box( $id, 'dashboard', $widget['context'] );
			}
		}

	}

	/**
	 * Add dashboard styles.
	 */
	public function dashboard_styles() {

		$css = '';

		ob_start();
		require __DIR__ . '/inc/widget-styles.css.php';
		$css = ob_get_clean();

		wp_add_inline_style( 'udb-dashboard', $css );

	}

}
