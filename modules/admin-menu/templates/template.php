<?php
/**
 * Admin menu page template.
 *
 * @package Ultimate Dashboard PRO
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

$wp_roles   = wp_roles();
$role_names = $wp_roles->role_names;
?>

<div class="wrap heatbox-wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! udb_is_pro_active() ) : ?>

		<div class="udb-pro-admin-menu-nag">
			<p><?php _e( 'This feature is available in Ultimate Dashboard PRO.', 'ultimate-dashboard' ); ?></p>
			<a href="https://ultimatedashboard.io/pro/?utm_source=plugin&utm_medium=admin_menu_link&utm_campaign=udb" class="button button-large button-primary" target="_blank">
				<?php _e( 'Get Ultimate Dashboard PRO', 'ultimate-dashboard' ); ?>
			</a>
		</div>

	<?php endif; ?>

	<form action="options.php" method="post" class="udb-admin-menu--edit-form">

		<div class="heatbox udb-admin-menu-box">

			<div class="udb-admin-menu-box--header">
				<h2 class="udb-admin-menu-box--title">
					<?php _e( 'Admin Menu Editor', 'ultimate-dashboard' ); ?>
				</h2>
				<div class="udb-admin-menu-box--search-box is-hidden">
					<input type="text" placeholder="Select a user" class="search-user">
				</div>
				<ul class="udb-admin-menu-box--header-tabs">
					<li class="udb-admin-menu-box--header-tab" data-header-tab="users">
						<?php _e( 'Users', 'ultimate-dashboard' ); ?>
					</li>
					<li class="udb-admin-menu-box--header-tab is-active" data-header-tab="roles">
						<?php _e( 'Roles', 'ultimate-dashboard' ); ?>
					</li>
				</ul>
			</div>

			<div class="udb-admin-menu--tabs udb-admin-menu--role-tabs">
				<ul class="udb-admin-menu--tab-menu udb-admin-menu--role-menu">
					<?php foreach ( $role_names as $role_key => $role_name ) : ?>

						<li class="udb-admin-menu--tab-menu-item<?php echo ( 'administrator' === $role_key ? ' is-active' : '' ); ?>" data-udb-tab-content="udb-admin-menu--<?php echo esc_html( $role_key ); ?>-edit-area" data-role="<?php echo esc_attr( $role_key ); ?>">
							<button type="button">
								<?php echo esc_html( ucwords( $role_name ) ); ?>
							</button>
						</li>

					<?php endforeach; ?>
				</ul>

				<div class="udb-admin-menu--tab-content udb-admin-menu--edit-area">
					<?php foreach ( $role_names as $role_key => $role_name ) : ?>

						<div id="udb-admin-menu--<?php echo esc_attr( $role_key ); ?>-edit-area" class="udb-admin-menu--tab-content-item udb-admin-menu--role-workspace<?php echo ( 'administrator' === $role_key ? ' is-active' : '' ); ?>" data-role="<?php echo esc_attr( $role_key ); ?>">
							<ul class="udb-admin-menu--menu-list">
								<!-- to be re-written via js -->
							</ul>
						</div>

					<?php endforeach; ?>
				</div><!-- .udb-admin-menu--tab-content -->
			</div><!-- .udb-admin-menu--role-tabs -->

			<div class="udb-admin-menu--tabs udb-admin-menu--user-tabs is-hidden">
				<ul class="udb-admin-menu--tab-menu udb-admin-menu--user-menu">
					<!-- to be filled via JS -->
				</ul>

				<div class="udb-admin-menu--tab-content udb-admin-menu--edit-area">
					<!-- to be filled via JS -->
				</div><!-- .udb-admin-menu--tab-content -->
			</div><!-- .udb-admin-menu--user-tabs -->

			<div class="heatbox-footer">

				<?php if ( ! udb_is_pro_active() ) : ?>

					<div class="udb-pro-settings-page-notice udb-pro-admin-menu-notice">
						<p><?php _e( 'This feature is available in Ultimate Dashboard PRO.', 'ultimate-dashboard' ); ?></p>
						<a href="https://ultimatedashboard.io/pro/?utm_source=plugin&utm_medium=admin_menu_link&utm_campaign=udb" class="button button-large button-primary" target="_blank">
							<?php _e( 'Get Ultimate Dashboard PRO', 'ultimate-dashboard' ); ?>
						</a>
					</div>

				<?php endif; ?>

				<?php do_action( 'udb_admin_menu_form_footer' ); ?>

			</div>
		</div>

	</form>

</div>
