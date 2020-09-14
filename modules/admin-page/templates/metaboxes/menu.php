<?php
/**
 * Menu metabox.
 *
 * @package Ultimate Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module, $post ) {

	$menu_type   = get_post_meta( $post->ID, 'udb_menu_type', true );
	$menu_parent = get_post_meta( $post->ID, 'udb_menu_parent', true );
	$menu_order  = get_post_meta( $post->ID, 'udb_menu_order', true );
	$menu_order  = $menu_order ? absint( $menu_order ) : 10;
	$menu_icon   = get_post_meta( $post->ID, 'udb_menu_icon', true );

	$admin_menu = $GLOBALS['menu'];

	?>

	<div class="postbox-content has-lines">
		<div class="fields">
			<div class="field">
				<label class="label" for="udb_menu_type"><?php _e( 'Menu Type', 'ultimatedashboard' ); ?></label>
				<div class="control">
					<select name="udb_menu_type" id="udb_menu_type" class="is-full">
						<option value="parent" <?php selected( $menu_type, 'parent' ); ?>>
							<?php _e( 'Top-level Menu', 'ultimatedashboard' ); ?>
						</option>
						<option value="submenu" <?php selected( $menu_type, 'submenu' ); ?>>
							<?php _e( 'Submenu', 'ultimatedashboard' ); ?>
						</option>
					</select>
				</div>
			</div>

			<div class="field" data-show-if-field="udb_menu_type" data-show-if-value="submenu">
				<label class="label" for="udb_menu_parent"><?php _e( 'Parent Menu', 'ultimatedashboard' ); ?></label>
				<div class="control">
					<select name="udb_menu_parent" id="udb_menu_parent" class="is-full">
						<?php foreach ( $admin_menu as $menu ) : ?>
							<?php if ( ! empty( $menu[0] ) ) : ?>
								<option value="<?php echo esc_attr( $menu[2] ); ?>" <?php selected( $menu_parent, $menu[2] ); ?>>
									<?php echo $module->content()->strip_tags_content( $menu[0] ); ?>
								</option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="field">
				<label class="label" for="udb_menu_order"><?php _e( 'Menu Order', 'ultimatedashboard' ); ?></label>
				<div class="control">
					<input type="number" name="udb_menu_order" id="udb_menu_order" class="is-full" value="<?php echo esc_attr( $menu_order ); ?>" min="0" step="1">
				</div>
			</div>

			<?php require __DIR__ . '/../icon-selector.php'; ?>
		</div>
	</div>

	<?php

};