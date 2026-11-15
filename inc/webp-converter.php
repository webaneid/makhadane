<?php
/**
 * Konversi otomatis gambar yang diunggah (JPG/PNG) ke format WebP.
 *
 * Alur kerja (lihat ADR-0004):
 * 1. Saat file selesai diunggah (`wp_handle_upload` / `wp_handle_sideload`),
 *    file `.jpg`/`.jpeg`/`.png` langsung dikonversi menjadi `.webp` dengan
 *    ukuran original tetap dipertahankan.
 * 2. Setelah file `.webp` terverifikasi valid di disk, file mentah `.jpg`/`.png`
 *    asli dihapus menggunakan `wp_delete_file()`.
 * 3. Path & MIME type yang dikembalikan ke WordPress sudah berupa `image/webp`,
 *    sehingga ketika `wp_generate_attachment_metadata()` berjalan sesudahnya,
 *    seluruh ukuran potongan gambar (`thumbnail`, `medium`, `large`, `kotak`,
 *    dst.) otomatis dipotong dan disimpan dalam format `.webp`.
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAKHADANE_WEBP_QUALITY' ) ) {
	define( 'MAKHADANE_WEBP_QUALITY', 78 );
}

/**
 * Ambil nilai kualitas kompresi WebP (1-100).
 *
 * @return int
 */
function makhadane_get_webp_quality() {
	$quality = (int) apply_filters( 'makhadane_webp_quality', MAKHADANE_WEBP_QUALITY );
	if ( $quality < 1 || $quality > 100 ) {
		return MAKHADANE_WEBP_QUALITY;
	}

	return $quality;
}

/**
 * Set kualitas default `WP_Image_Editor` untuk output `image/webp`
 * (berlaku untuk ukuran original maupun seluruh sub-size hasil crop).
 *
 * @param int    $quality   Kualitas default editor (biasanya 82/86).
 * @param string $mime_type MIME type target.
 * @return int
 */
function makhadane_set_webp_editor_quality( $quality, $mime_type ) {
	if ( 'image/webp' === $mime_type ) {
		return makhadane_get_webp_quality();
	}

	return $quality;
}
add_filter( 'wp_editor_set_quality', 'makhadane_set_webp_editor_quality', 10, 2 );

/**
 * Fallback konversi PNG 8-bit indexed palette ke WebP menggunakan ekstensi GD
 * dengan mempertahankan transparansi alpha.
 *
 * Beberapa versi PHP GD menolak menyimpan PNG palette ke WebP jika belum diubah
 * menjadi truecolor (`imagepalettetotruecolor`).
 *
 * @param string $source_path Path file PNG sumber.
 * @param string $dest_path   Path file WebP tujuan.
 * @param int    $quality     Kualitas WebP (1-100).
 * @return bool True jika berhasil disimpan dan ukuran file > 0.
 */
function makhadane_convert_palette_png_to_webp( $source_path, $dest_path, $quality ) {
	if (
		! function_exists( 'imagecreatefrompng' ) ||
		! function_exists( 'imagepalettetotruecolor' ) ||
		! function_exists( 'imagewebp' )
	) {
		return false;
	}

	$image = @imagecreatefrompng( $source_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $image ) {
		return false;
	}

	imagepalettetotruecolor( $image );
	imagealphablending( $image, true );
	imagesavealpha( $image, true );

	$result = @imagewebp( $image, $dest_path, $quality ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	imagedestroy( $image );

	if ( ! $result || ! file_exists( $dest_path ) || filesize( $dest_path ) <= 0 ) {
		if ( file_exists( $dest_path ) ) {
			wp_delete_file( $dest_path );
		}
		return false;
	}

	return true;
}

/**
 * Konversi otomatis gambar yang baru diunggah (`image/jpeg`, `image/png`) ke `.webp`
 * dan hapus file asli non-WebP.
 *
 * @param array<string, mixed> $upload  Array hasil upload (`file`, `url`, `type`, atau `error`).
 * @param string               $context Konteks upload (`upload` atau `sideload`).
 * @return array<string, mixed>
 */
function makhadane_convert_upload_to_webp( $upload, $context = 'upload' ) {
	unset( $context );

	if (
		! is_array( $upload ) ||
		! empty( $upload['error'] ) ||
		empty( $upload['file'] ) ||
		empty( $upload['url'] ) ||
		empty( $upload['type'] )
	) {
		return $upload;
	}

	$file_path = (string) $upload['file'];
	$file_url  = (string) $upload['url'];
	$mime_type = (string) $upload['type'];

	/**
	 * Filter daftar MIME type gambar yang otomatis dikonversi ke WebP saat upload.
	 *
	 * @param array<int, string> $convertible_mimes Daftar MIME type.
	 */
	$convertible_mimes = (array) apply_filters(
		'makhadane_webp_convertible_mimes',
		array(
			'image/jpeg',
			'image/jpg',
			'image/png',
		)
	);

	if ( ! in_array( $mime_type, $convertible_mimes, true ) ) {
		return $upload;
	}

	if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
		return $upload;
	}

	if ( ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $upload;
	}

	$dir       = dirname( $file_path );
	$path_info = pathinfo( $file_path );
	$base_name = isset( $path_info['filename'] ) ? $path_info['filename'] : '';

	if ( '' === $base_name ) {
		return $upload;
	}

	$webp_filename = wp_unique_filename( $dir, $base_name . '.webp' );
	$webp_path     = trailingslashit( $dir ) . $webp_filename;
	$quality       = makhadane_get_webp_quality();
	$converted     = false;
	$final_path    = $webp_path;

	$editor = wp_get_image_editor( $file_path );
	if ( ! is_wp_error( $editor ) ) {
		$editor->set_quality( $quality );
		$saved = $editor->save( $webp_path, 'image/webp' );

		if (
			! is_wp_error( $saved ) &&
			! empty( $saved['path'] ) &&
			file_exists( $saved['path'] ) &&
			filesize( $saved['path'] ) > 0
		) {
			$converted  = true;
			$final_path = (string) $saved['path'];
		}
	}

	// Fallback khusus untuk PNG 8-bit palette jika WP_Image_Editor gagal menyimpannya.
	if ( ! $converted && 'image/png' === $mime_type ) {
		if ( makhadane_convert_palette_png_to_webp( $file_path, $webp_path, $quality ) ) {
			$converted  = true;
			$final_path = $webp_path;
		}
	}

	if ( ! $converted || ! file_exists( $final_path ) || filesize( $final_path ) <= 0 ) {
		return $upload;
	}

	// Samakan permission file baru dengan file upload asli sebelum file asli dihapus.
	$stat = @stat( dirname( $final_path ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( is_array( $stat ) && isset( $stat['mode'] ) ) {
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@chmod( $final_path, $perms ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod
	}

	// Hapus file asli (.jpg/.png) agar hanya versi .webp yang tersimpan di server.
	if ( $final_path !== $file_path ) {
		wp_delete_file( $file_path );
	}

	$final_filename = basename( $final_path );
	$upload['file'] = $final_path;
	$upload['url']  = trailingslashit( dirname( $file_url ) ) . $final_filename;
	$upload['type'] = 'image/webp';

	return $upload;
}
add_filter( 'wp_handle_upload', 'makhadane_convert_upload_to_webp', 10, 2 );
add_filter( 'wp_handle_sideload', 'makhadane_convert_upload_to_webp', 10, 2 );
