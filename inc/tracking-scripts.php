<?php
/**
 * Tracking Scripts Integration
 *
 * Handles injection of third-party tracking scripts (Google Analytics, Meta Pixel, etc.)
 * via ACF Options fields.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ============================================================================
 * TRACKING SCRIPTS INJECTION
 * ============================================================================
 */

/**
 * Inject Google Analytics / GTM script to <head>.
 *
 * ACF Field: ane_ga_header (from ACF Options)
 * Location: wp_head (priority 10)
 *
 * @since 1.0.0
 */
function ane_gtm_header_content() {
	$script = get_field( 'ane_ga_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'ane_gtm_header_content', 10 );

/**
 * Inject custom header script (Search Console, etc.) to <head>.
 *
 * ACF Field: ane_sc_header (from ACF Options)
 * Location: wp_head (priority 11)
 *
 * @since 1.0.0
 */
function ane_sc_header_content() {
	$script = get_field( 'ane_sc_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'ane_sc_header_content', 11 );

/**
 * Inject Meta Pixel script to <head>.
 *
 * ACF Field: ane_metapixel_header (from ACF Options)
 * Location: wp_head (priority 12)
 *
 * @since 1.0.0
 */
function ane_metapixel_header_content() {
	$script = get_field( 'ane_metapixel_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'ane_metapixel_header_content', 12 );

/**
 * Inject Meta SDK script after <body> tag.
 *
 * ACF Field: ane_metasdk_body (from ACF Options)
 * Location: wp_body_open (immediately after <body>)
 *
 * @since 1.0.0
 */
function ane_meta_sdk_script() {
	$script = get_field( 'ane_metasdk_body', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_body_open', 'ane_meta_sdk_script' );

/**
 * Inject Google Analytics / GTM footer script.
 *
 * ACF Field: ane_ga_footer (from ACF Options)
 * Location: wp_footer (priority 100)
 *
 * @since 1.0.0
 */
function ane_gtm_footer_content() {
	$script = get_field( 'ane_ga_footer', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_footer', 'ane_gtm_footer_content', 100 );

