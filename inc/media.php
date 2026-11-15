<?php
/**
 * Media Upload Functions.
 *
 * Enable additional file types for upload including SVG, WebP, and other formats.
 *
 * @package makhadane
 * @since 4.1.2
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Enable SVG (administrator only) and additional image format uploads.
 *
 * @param array<string, string> $mimes Existing MIME types.
 * @return array<string, string> Modified MIME types.
 */
function ane_enable_additional_mime_types( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	$mimes['webp']         = 'image/webp';
	$mimes['ico']          = 'image/x-icon';
	$mimes['gif']          = 'image/gif';
	$mimes['avif']         = 'image/avif';
	$mimes['jpg|jpeg|jpe'] = 'image/jpeg';
	$mimes['png']          = 'image/png';

	return $mimes;
}
add_filter( 'upload_mimes', 'ane_enable_additional_mime_types' );

/**
 * Check file type for SVG uploads (restricted to administrators).
 *
 * @param array<string, mixed> $data     Values for the extension, mime type, and corrected filename.
 * @param string               $file     Full path to the file.
 * @param string               $filename The name of the file.
 * @param string[]|null        $mimes    Array of mime types keyed by their file extension regex.
 * @return array<string, mixed> Modified file data array.
 */
function ane_check_filetype_and_ext( $data, $file, $filename, $mimes ) {
	unset( $file, $mimes );

	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$ext = strtolower( (string) pathinfo( (string) $filename, PATHINFO_EXTENSION ) );
	if ( 'svg' === $ext && current_user_can( 'manage_options' ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'ane_check_filetype_and_ext', 10, 4 );

/**
 * Fix SVG thumbnail display in media library modal.
 *
 * @param array<string, mixed> $response   Response data.
 * @param WP_Post              $attachment Attachment object.
 * @param array<string, mixed> $meta       Attachment metadata.
 * @return array<string, mixed> Modified response.
 */
function ane_fix_svg_thumbnail_display( $response, $attachment, $meta ) {
	unset( $attachment, $meta );

	if ( isset( $response['mime'], $response['url'] ) && 'image/svg+xml' === $response['mime'] ) {
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
 * Provide fallback dimensions for SVG in wp_get_attachment_image_src.
 *
 * @param array<int, mixed>|false $image         Array of image data, or boolean false if no image is available.
 * @param int                     $attachment_id Image attachment ID.
 * @return array<int, mixed>|false
 */
function ane_display_svg_in_media_library( $image, $attachment_id ) {
	if ( is_array( $image ) && ! empty( $image[0] ) && ! empty( $image[1] ) ) {
		return $image;
	}

	$attachment = get_post( (int) $attachment_id );
	if ( ! $attachment || 'image/svg+xml' !== get_post_mime_type( $attachment ) ) {
		return $image;
	}

	$url = wp_get_attachment_url( (int) $attachment_id );
	if ( ! is_string( $url ) || '' === $url ) {
		return $image;
	}

	return array( $url, 150, 150, false );
}
add_filter( 'wp_get_attachment_image_src', 'ane_display_svg_in_media_library', 10, 2 );

/**
 * Add SVG support to ACF image fields in admin.
 *
 * @return void
 */
function ane_add_svg_support_to_acf() {
	?>
	<style>
		.acf-image-uploader img[src$=".svg"] {
			width: 100% !important;
			height: auto !important;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'ane_add_svg_support_to_acf' );

/**
 * Sanitize SVG uploads to prevent XSS and XXE attacks.
 *
 * @param array<string, mixed> $file Upload file array.
 * @return array<string, mixed> Modified file array.
 */
function ane_sanitize_svg_upload( $file ) {
	$name = isset( $file['name'] ) ? (string) $file['name'] : '';
	$type = isset( $file['type'] ) ? (string) $file['type'] : '';
	$ext  = strtolower( (string) pathinfo( $name, PATHINFO_EXTENSION ) );

	if ( 'svg' !== $ext && 'image/svg+xml' !== $type ) {
		return $file;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		$file['error'] = __( 'Hanya administrator yang diizinkan mengunggah file SVG.', 'makhadane' );
		return $file;
	}

	$tmp_name = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';
	if ( '' === $tmp_name || ! is_readable( $tmp_name ) ) {
		return $file;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$content = file_get_contents( $tmp_name );
	if ( ! is_string( $content ) || '' === trim( $content ) || false === stripos( $content, '<svg' ) ) {
		$file['error'] = __( 'File SVG tidak valid.', 'makhadane' );
		return $file;
	}

	// Bersihkan deklarasi DOCTYPE/ENTITY eksternal (XXE), tag berbahaya, event handler on*, dan javascript:/data: URI.
	$clean = (string) preg_replace( '/<!DOCTYPE[^>]*>/i', '', $content );
	$clean = (string) preg_replace( '/<!ENTITY[^>]*>/i', '', $clean );
	$clean = (string) preg_replace( '#<\s*(script|foreignObject|iframe|object|embed|applet)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $clean );
	$clean = (string) preg_replace( '#<\s*(script|foreignObject|iframe|object|embed|applet)\b[^>]*/?\s*>#is', '', $clean );
	$clean = (string) preg_replace( '/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean );
	$clean = (string) preg_replace( '/(href|xlink:href|src)\s*=\s*(?:"\s*(?:javascript|data):[^"]*"|\'\s*(?:javascript|data):[^\']*\')/i', '', $clean );

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	file_put_contents( $tmp_name, $clean );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'ane_sanitize_svg_upload' );

/**
 * Add WebP support for thumbnails.
 *
 * @param array<string, mixed> $sizes Image sizes.
 * @return array<string, mixed> Modified sizes.
 */
function ane_webp_thumbnail_support( $sizes ) {
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'ane_webp_thumbnail_support' );

/**
 * Display file format info in media library columns.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string> Modified columns.
 */
function ane_add_media_format_column( $columns ) {
	$columns['format'] = __( 'Format', 'makhadane' );
	return $columns;
}
add_filter( 'manage_media_columns', 'ane_add_media_format_column' );

/**
 * Display file format in media library.
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 * @return void
 */
function ane_display_media_format_column( $column_name, $post_id ) {
	if ( 'format' === $column_name ) {
		$mime_type = (string) get_post_mime_type( $post_id );
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
