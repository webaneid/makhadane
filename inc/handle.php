<?php
/**
 * Script handling utilities (komentar: lihat inc/comments-control.php).
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Dequeue unused jQuery UI core style on front-end.
 *
 * @return void
 */
function wp_remove_scripts() {
	if ( current_user_can( 'update_core' ) ) {
		return;
	}

	if ( is_page( 'homepage' ) ) {
		wp_dequeue_style( 'jquery-ui-core' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_remove_scripts', 99 );
