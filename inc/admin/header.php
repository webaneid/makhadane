<?php
/**
 * Admin Header/Topbar.
 *
 * Admin bar memakai tampilan bawaan WordPress (tanpa styling/tombol kustom).
 * File ini hanya mengatur kapan admin bar tampil di frontend.
 *
 * @package makhadane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Hide admin bar for non-admin users on frontend.
 *
 * Clean frontend experience untuk subscribers/customers.
 */
function ane_hide_admin_bar_frontend() {
	if ( ! current_user_can( 'manage_options' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'ane_hide_admin_bar_frontend' );
