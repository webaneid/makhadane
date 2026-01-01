<?php
/**
 * ============================================================================
 * FACEBOOK COMMENTS INTEGRATION
 * ============================================================================
 */

/**
 * Load Facebook SDK script.
 *
 * Loads Facebook JavaScript SDK for Facebook Comments Social Plugin.
 * Only loads if Facebook Comments is enabled and App ID is available.
 *
 * IMPORTANT: SDK must be loaded right after opening <body> tag (wp_body_open).
 *
 * Reference: https://developers.facebook.com/docs/plugins/comments/
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


function ane_facebook_sdk_script() {
	// Check if Facebook Comments enabled.
	$fb_comments_enabled = get_field( 'ane_facebook_comments_enable', 'option' );
	$fb_app_id           = get_field( 'ane_facebook_app_id', 'option' );

	if ( ! $fb_comments_enabled || empty( $fb_app_id ) ) {
		return;
	}

	// Only load on single posts.
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	// Get locale setting (default: id_ID).
	$fb_locale = get_field( 'ane_facebook_comments_locale', 'option' ) ?: 'id_ID';
	?>
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo esc_attr( $fb_locale ); ?>/sdk.js#xfbml=1&version=v24.0&appId=<?php echo esc_attr( $fb_app_id ); ?>"></script>
	<?php
}
add_action( 'wp_body_open', 'ane_facebook_sdk_script', 1 );

/**
 * Check if Facebook Comments is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_facebook_comments_enabled() {
	$enabled = get_field( 'ane_facebook_comments_enable', 'option' );
	$app_id  = get_field( 'ane_facebook_app_id', 'option' );

	return $enabled && ! empty( $app_id );
}

/**
 * Get Facebook App ID.
 *
 * @return string Facebook App ID or empty string.
 * @since 1.0.0
 */
function ane_get_facebook_app_id() {
	return get_field( 'ane_facebook_app_id', 'option' ) ?: '';
}

/**
 * Get Facebook Comments number of posts to display.
 *
 * @return int Number of comments to show (default 10).
 * @since 1.0.0
 */
function ane_get_facebook_comments_num_posts() {
	$num = get_field( 'ane_facebook_comments_num_posts', 'option' );
	return $num ? absint( $num ) : 10;
}

/**
 * Register ACF Field Group for Facebook Comments settings.
 *
 * Creates fields for Facebook Comments integration:
 * - Enable/Disable toggle
 * - Facebook App ID
 * - Locale/Language
 * - Number of comments to display
 *
 * @since 1.0.0
 */
function ane_register_facebook_comments_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_ane_facebook_comments',
			'title'    => __( 'Facebook Comments', 'elemenane' ),
			'fields'   => array(
				// Enable Facebook Comments.
				array(
					'key'           => 'field_ane_facebook_comments_enable',
					'label'         => __( 'Enable Facebook Comments', 'elemenane' ),
					'name'          => 'ane_facebook_comments_enable',
					'type'          => 'true_false',
					'instructions'  => __( 'Enable to replace WordPress native comments with Facebook Comments Social Plugin.', 'elemenane' ),
					'ui'            => 1,
					'default_value' => 0,
				),
				// Facebook App ID.
				array(
					'key'               => 'field_ane_facebook_app_id',
					'label'             => __( 'Facebook App ID', 'elemenane' ),
					'name'              => 'ane_facebook_app_id',
					'type'              => 'text',
					'instructions'      => __( 'Enter your Facebook App ID. Get it from: https://developers.facebook.com/apps/', 'elemenane' ),
					'placeholder'       => '352619118625171',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				// Locale/Language.
				array(
					'key'               => 'field_ane_facebook_comments_locale',
					'label'             => __( 'Language / Locale', 'elemenane' ),
					'name'              => 'ane_facebook_comments_locale',
					'type'              => 'select',
					'instructions'      => __( 'Select language for Facebook Comments plugin.', 'elemenane' ),
					'choices'           => array(
						'id_ID' => 'Indonesian (id_ID)',
						'en_US' => 'English (en_US)',
						'en_GB' => 'English UK (en_GB)',
						'ms_MY' => 'Malay (ms_MY)',
						'ar_AR' => 'Arabic (ar_AR)',
						'zh_CN' => 'Chinese Simplified (zh_CN)',
						'ja_JP' => 'Japanese (ja_JP)',
						'ko_KR' => 'Korean (ko_KR)',
					),
					'default_value'     => 'id_ID',
					'allow_null'        => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				// Number of posts.
				array(
					'key'               => 'field_ane_facebook_comments_num_posts',
					'label'             => __( 'Number of Comments', 'elemenane' ),
					'name'              => 'ane_facebook_comments_num_posts',
					'type'              => 'number',
					'instructions'      => __( 'Number of comments to display (default: 10).', 'elemenane' ),
					'default_value'     => 10,
					'min'               => 1,
					'max'               => 100,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ane-general-setting',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'ane_register_facebook_comments_fields' );

/**
 * Generate Facebook Comments embed code.
 *
 * Note: Facebook Comments requires Facebook SDK to be loaded.
 * This is handled in inc/tracking-scripts.php via ACF settings.
 *
 * Reference: https://developers.facebook.com/docs/plugins/comments/
 *
 * @return string HTML markup for Facebook Comments plugin
 */
function ane_load_facebook_comment() {
	global $post;

	if ( ! $post ) {
		return '';
	}

	// Check if Facebook Comments is enabled.
	if ( ! function_exists( 'ane_is_facebook_comments_enabled' ) || ! ane_is_facebook_comments_enabled() ) {
		return '';
	}

	$permalink = esc_url( get_permalink( $post->ID ) );

	// Get number of comments from ACF settings.
	$num_posts = function_exists( 'ane_get_facebook_comments_num_posts' ) ? ane_get_facebook_comments_num_posts() : 10;

	$content  = '<div class="fb-comments-wrapper">';
	$content .= '<div class="fb-comments" ';
	$content .= 'data-href="' . $permalink . '" ';
	$content .= 'data-width="" ';
	$content .= 'data-numposts="' . absint( $num_posts ) . '" ';
	$content .= 'data-order-by="social" ';
	$content .= 'data-colorscheme="light" ';
	$content .= 'data-lazy="true">';
	$content .= '</div>';
	$content .= '</div>';

	return $content;
}