<?php
/**
 * Media Upload Functions
 *
 * Enable additional file types for upload including SVG, WebP, and other formats.
 *
 * @package makhadane
 * @since 4.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enable SVG and additional image format uploads
 *
 * @param array $mimes Existing MIME types.
 * @return array Modified MIME types.
 */
function ane_enable_additional_mime_types( $mimes ) {
	// Image formats
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';
	$mimes['ico']  = 'image/x-icon';
	$mimes['gif']  = 'image/gif';
	$mimes['avif'] = 'image/avif';

	// Already supported but ensure they're there
	$mimes['jpg|jpeg|jpe'] = 'image/jpeg';
	$mimes['png']          = 'image/png';

	return $mimes;
}
add_filter( 'upload_mimes', 'ane_enable_additional_mime_types' );

/**
 * Check file type for SVG uploads
 *
 * WordPress by default blocks SVG for security reasons.
 * This function validates SVG files before upload.
 *
 * @param array  $file     File array.
 * @param string $filename File name.
 * @param array  $mimes    MIME types.
 * @return array Modified file array.
 */
function ane_check_filetype_and_ext( $data, $file, $filename, $mimes ) {
	$filetype = wp_check_filetype( $filename, $mimes );

	return array(
		'ext'             => $filetype['ext'],
		'type'            => $filetype['type'],
		'proper_filename' => $data['proper_filename'],
	);
}
add_filter( 'wp_check_filetype_and_ext', 'ane_check_filetype_and_ext', 10, 4 );

/**
 * Fix SVG thumbnail display in media library
 *
 * @param string $response   Response data.
 * @param object $attachment Attachment object.
 * @param array  $meta       Attachment metadata.
 * @return string Modified response.
 */
function ane_fix_svg_thumbnail_display( $response, $attachment, $meta ) {
	if ( 'image/svg+xml' === $response['mime'] ) {
		$response['image'] = array(
			'src'    => $response['url'],
			'width'  => 150,
			'height' => 150,
		);

		$response['sizes'] = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => 150,
				'height'      => 150,
				'orientation' => 'landscape',
			),
		);
	}

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'ane_fix_svg_thumbnail_display', 10, 3 );

/**
 * Display SVG in media library
 *
 * @param string $content HTML content.
 * @param int    $post_id Post ID.
 * @return string Modified content.
 */
function ane_display_svg_in_media_library( $content, $post_id ) {
	$attachment = get_post( $post_id );

	if ( ! $attachment ) {
		return $content;
	}

	if ( 'image/svg+xml' === get_post_mime_type( $attachment ) ) {
		$url     = wp_get_attachment_url( $post_id );
		$content = '<img src="' . esc_url( $url ) . '" style="width: 100%; height: auto;" />';
	}

	return $content;
}
add_filter( 'wp_get_attachment_image_src', 'ane_display_svg_in_media_library', 10, 2 );

/**
 * Add SVG support to ACF
 *
 * Enable SVG upload in ACF image fields.
 *
 * @return void
 */
function ane_add_svg_support_to_acf() {
	?>
	<style>
		/* Fix SVG display in ACF */
		.acf-image-uploader img[src$=".svg"] {
			width: 100% !important;
			height: auto !important;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'ane_add_svg_support_to_acf' );

/**
 * Sanitize SVG uploads for security
 *
 * Basic SVG sanitization to prevent XSS attacks.
 * For production, consider using a library like enshrined/svg-sanitize.
 *
 * @param array $file Upload file array.
 * @return array Modified file array.
 */
function ane_sanitize_svg_upload( $file ) {
	// Only process SVG files
	if ( 'image/svg+xml' !== $file['type'] ) {
		return $file;
	}

	// Get file content
	$svg_content = file_get_contents( $file['tmp_name'] );

	// Basic sanitization - remove script tags and event handlers
	$svg_content = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $svg_content );
	$svg_content = preg_replace( '/\bon\w+\s*=\s*["\'].*?["\']/i', '', $svg_content );

	// Save sanitized content
	file_put_contents( $file['tmp_name'], $svg_content );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'ane_sanitize_svg_upload' );

/**
 * Add WebP support for thumbnails
 *
 * Ensure WebP images generate thumbnails properly.
 *
 * @param array $sizes Image sizes.
 * @return array Modified sizes.
 */
function ane_webp_thumbnail_support( $sizes ) {
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'ane_webp_thumbnail_support' );

/**
 * Display file format info in media library columns
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function ane_add_media_format_column( $columns ) {
	$columns['format'] = __( 'Format', 'makhadane' );
	return $columns;
}
add_filter( 'manage_media_columns', 'ane_add_media_format_column' );

/**
 * Display file format in media library
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 * @return void
 */
function ane_display_media_format_column( $column_name, $post_id ) {
	if ( 'format' === $column_name ) {
		$mime_type = get_post_mime_type( $post_id );
		$format    = '';

		switch ( $mime_type ) {
			case 'image/svg+xml':
				$format = '<span style="background: #9b59b6; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">SVG</span>';
				break;
			case 'image/webp':
				$format = '<span style="background: #3498db; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">WebP</span>';
				break;
			case 'image/gif':
				$format = '<span style="background: #e67e22; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">GIF</span>';
				break;
			case 'image/jpeg':
				$format = '<span style="background: #2ecc71; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">JPG</span>';
				break;
			case 'image/png':
				$format = '<span style="background: #1abc9c; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">PNG</span>';
				break;
			case 'image/avif':
				$format = '<span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">AVIF</span>';
				break;
			default:
				$format = strtoupper( str_replace( 'image/', '', $mime_type ) );
		}

		echo wp_kses_post( $format );
	}
}
add_action( 'manage_media_custom_column', 'ane_display_media_format_column', 10, 2 );
