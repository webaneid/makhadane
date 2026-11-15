<?php
/**
 * Comment and script handling utilities.
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Disable comment support in admin for posts and pages.
 *
 * @return void
 */
function codesnippets_disable_comments() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_init', 'codesnippets_disable_comments' );

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
