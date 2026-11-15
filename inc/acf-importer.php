<?php
/**
 * Pengecekan plugin ACF Pro, auto-fallback Field Group dari JSON, dan fitur
 * Import / Sinkronisasi Field ACF di halaman Makhad Ane Settings.
 *
 * File sumber JSON disimpan di: inc/acf-json/field-groups.json
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

/**
 * Periksa apakah plugin ACF / ACF Pro sudah aktif.
 *
 * @return bool
 */
function makhadane_is_acf_active() {
	return class_exists( 'ACF' ) && function_exists( 'acf_add_local_field_group' );
}

/**
 * Tampilkan peringatan di wp-admin jika plugin ACF Pro belum diaktifkan.
 *
 * @return void
 */
function makhadane_acf_missing_notice() {
	if ( makhadane_is_acf_active() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Makhad Ane:', 'makhadane' ); ?></strong>
			<?php esc_html_e( 'Theme ini membutuhkan plugin Advanced Custom Fields PRO (ACF Pro) agar seluruh pengaturan dan fitur halaman dapat berjalan. Silakan pasang dan aktifkan ACF Pro terlebih dahulu.', 'makhadane' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'makhadane_acf_missing_notice' );

/**
 * Ambil seluruh definisi Field Group ACF dari file JSON di folder inc/acf-json/.
 *
 * @return array<int, array<string, mixed>> Daftar field group yang valid.
 */
function makhadane_get_bundled_acf_groups() {
	$json_dir = get_template_directory() . '/inc/acf-json';
	if ( ! is_dir( $json_dir ) ) {
		return array();
	}

	$files = glob( $json_dir . '/*.json' );
	if ( ! is_array( $files ) || empty( $files ) ) {
		return array();
	}

	$groups = array();
	foreach ( $files as $file ) {
		if ( ! is_readable( $file ) ) {
			continue;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw = file_get_contents( $file );
		if ( ! is_string( $raw ) || '' === $raw ) {
			continue;
		}

		$decoded = json_decode( $raw, true );
		if ( ! is_array( $decoded ) ) {
			continue;
		}

		// Format export ACF bisa berupa array daftar group ([ {key: ...}, ... ]) atau 1 objek group ({key: ...}).
		if ( isset( $decoded['key'] ) && is_string( $decoded['key'] ) ) {
			$groups[ $decoded['key'] ] = $decoded;
		} else {
			foreach ( $decoded as $item ) {
				if ( is_array( $item ) && isset( $item['key'] ) && is_string( $item['key'] ) ) {
					$groups[ $item['key'] ] = $item;
				}
			}
		}
	}

	return array_values( $groups );
}

/**
 * Hitung hash MD5 gabungan dari seluruh file JSON ACF di inc/acf-json/.
 *
 * Dipakai untuk mendeteksi perubahan skema JSON secara otomatis (saat pengembangan
 * redesign maupun setelah auto-update theme) dan menyinkronkannya ke database
 * hanya 1 kali per perubahan file.
 *
 * @return string Hash MD5 atau string kosong.
 */
function makhadane_get_bundled_acf_hash() {
	$json_dir = get_template_directory() . '/inc/acf-json';
	if ( ! is_dir( $json_dir ) ) {
		return '';
	}

	$files = glob( $json_dir . '/*.json' );
	if ( ! is_array( $files ) || empty( $files ) ) {
		return '';
	}

	sort( $files );
	$combined = '';
	foreach ( $files as $file ) {
		if ( is_readable( $file ) ) {
			$file_hash = md5_file( $file );
			if ( is_string( $file_hash ) ) {
				$combined .= basename( $file ) . ':' . $file_hash . ';';
			}
		}
	}

	return '' !== $combined ? md5( $combined ) : '';
}

/**
 * Cari ID post `acf-field-group` di database berdasarkan key group (mis. group_6032a4955792a).
 *
 * @param string $group_key Key unik ACF field group.
 * @return int ID post di database, atau 0 jika belum ter-import di database.
 */
function makhadane_get_db_acf_group_id( $group_key ) {
	if ( '' === $group_key ) {
		return 0;
	}

	$posts = get_posts(
		array(
			'post_type'              => 'acf-field-group',
			'name'                   => $group_key,
			'post_status'            => array( 'publish', 'acf-disabled', 'draft' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $posts ) && isset( $posts[0] ) ) {
		return (int) $posts[0];
	}

	return 0;
}

/**
 * Import Field Group ACF dari inc/acf-json/ ke dalam database WordPress.
 *
 * Bersifat idempotent: jika grup sudah ada di database dan $overwrite = true,
 * maka ID post existing disertakan agar ACF meng-update grup tersebut tanpa
 * membuat duplikasi.
 *
 * @param bool $overwrite True untuk menimpa/menyinkronkan ulang grup yang sudah ada di DB.
 * @return int Jumlah field group yang berhasil di-import / diperbarui.
 */
function makhadane_import_acf_json_groups( $overwrite = false ) {
	if ( ! makhadane_is_acf_active() || ! function_exists( 'acf_import_field_group' ) ) {
		return 0;
	}

	$groups   = makhadane_get_bundled_acf_groups();
	$imported = 0;

	foreach ( $groups as $group ) {
		$key = isset( $group['key'] ) && is_string( $group['key'] ) ? $group['key'] : '';
		if ( '' === $key ) {
			continue;
		}

		$existing_id = makhadane_get_db_acf_group_id( $key );
		if ( $existing_id > 0 && ! $overwrite ) {
			continue;
		}

		if ( $existing_id > 0 ) {
			$group['ID'] = $existing_id;
		} else {
			unset( $group['ID'] );
		}

		acf_import_field_group( $group );
		++$imported;
	}

	$hash = makhadane_get_bundled_acf_hash();
	if ( '' !== $hash ) {
		update_option( 'makhadane_acf_json_hash', $hash, true );
	}

	return $imported;
}

/**
 * Otomatis perbarui Field Group ACF di database setiap kali file JSON di
 * inc/acf-json/ mengalami perubahan (mis. penambahan field baru saat redesign).
 *
 * Hanya menulis ke database 1 kali ketika hash MD5 JSON berubah.
 *
 * @return void
 */
function makhadane_maybe_sync_acf_json_on_change() {
	if ( ! makhadane_is_acf_active() || ! function_exists( 'acf_import_field_group' ) ) {
		return;
	}

	$current_hash = makhadane_get_bundled_acf_hash();
	if ( '' === $current_hash ) {
		return;
	}

	if ( get_option( 'makhadane_acf_json_hash' ) !== $current_hash ) {
		makhadane_import_acf_json_groups( true );
	}
}
add_action( 'acf/init', 'makhadane_maybe_sync_acf_json_on_change', 15 );

/**
 * Otomatis import Field Group ACF yang belum ada di DB saat theme diaktifkan.
 *
 * @return void
 */
function makhadane_auto_import_acf_on_activation() {
	if ( makhadane_is_acf_active() ) {
		makhadane_import_acf_json_groups( false );
	}
}
add_action( 'after_switch_theme', 'makhadane_auto_import_acf_on_activation' );

/**
 * Daftarkan Field Group yang belum ada di DB secara lokal (fallback instan)
 * serta tambahkan tab & tombol "Import JSON ACF" di halaman Makhad Ane Settings.
 *
 * @return void
 */
function makhadane_register_acf_json_and_settings_tab() {
	if ( ! makhadane_is_acf_active() ) {
		return;
	}

	$groups        = makhadane_get_bundled_acf_groups();
	$total_groups  = count( $groups );
	$in_db_count   = 0;
	$missing_names = array();

	foreach ( $groups as $group ) {
		$key   = isset( $group['key'] ) && is_string( $group['key'] ) ? $group['key'] : '';
		$title = isset( $group['title'] ) && is_string( $group['title'] ) ? $group['title'] : $key;

		if ( '' === $key ) {
			continue;
		}

		$db_id = makhadane_get_db_acf_group_id( $key );
		if ( $db_id > 0 ) {
			++$in_db_count;
		} else {
			$missing_names[] = $title;
			// Fallback lokal agar field langsung aktif walau belum di-import ke DB.
			acf_add_local_field_group( $group );
		}
	}

	$import_missing_url = wp_nonce_url(
		admin_url( 'admin.php?page=ane-general-setting&makhadane_acf_import=missing' ),
		'makhadane_acf_import_action'
	);

	$import_all_url = wp_nonce_url(
		admin_url( 'admin.php?page=ane-general-setting&makhadane_acf_import=overwrite' ),
		'makhadane_acf_import_action'
	);

	$status_badge = ( $in_db_count === $total_groups && $total_groups > 0 )
		? '<span style="color:#1b5e20;font-weight:700;">' . sprintf(
			/* translators: 1: jumlah grup di DB, 2: total grup di JSON */
			esc_html__( 'Lengkap (%1$d / %2$d Field Group tersimpan di Database)', 'makhadane' ),
			$in_db_count,
			$total_groups
		) . '</span>'
		: '<span style="color:#e65100;font-weight:700;">' . sprintf(
			/* translators: 1: jumlah grup di DB, 2: total grup di JSON */
			esc_html__( '%1$d / %2$d Field Group di Database (sisanya berjalan via Auto-Fallback JSON)', 'makhadane' ),
			$in_db_count,
			$total_groups
		) . '</span>';

	$missing_html = '';
	if ( ! empty( $missing_names ) ) {
		$missing_html = '<br><strong>' . esc_html__( 'Belum di-import ke DB:', 'makhadane' ) . '</strong> '
			. esc_html( implode( ', ', $missing_names ) );
	}

	$buttons_html = '<div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">';
	if ( $in_db_count < $total_groups ) {
		$buttons_html .= '<a href="' . esc_url( $import_missing_url ) . '" class="button button-primary">'
			. esc_html__( 'Import Field yang Belum Ada', 'makhadane' )
			. '</a>';
	}
	$buttons_html .= '<a href="' . esc_url( $import_all_url ) . '" class="button button-secondary" onclick="return confirm(\''
		. esc_js(
			sprintf(
				/* translators: %d: total jumlah field group di JSON */
				__( 'Sinkronkan ulang semua %d Field Group ACF dari file JSON theme ke database?', 'makhadane' ),
				$total_groups
			)
		)
		. '\');">'
		. esc_html__( 'Import / Sinkronkan Semua JSON ACF', 'makhadane' )
		. '</a></div>';

	acf_add_local_field_group(
		array(
			'key'                   => 'group_makhadane_acf_json_importer',
			'title'                 => __( 'Sinkronisasi Field ACF (JSON)', 'makhadane' ),
			'fields'                => array(
				array(
					'key'       => 'field_makhadane_acf_json_tab',
					'label'     => __( 'Import ACF JSON', 'makhadane' ),
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'     => 'field_makhadane_acf_json_info',
					'label'   => __( 'Status Field Group ACF Theme', 'makhadane' ),
					'type'    => 'message',
					'message' => sprintf(
						'<strong>%s</strong> <span style="color:#1b5e20;font-weight:700;">%s</span><br><strong>%s</strong> %s%s<br><br>%s%s',
						esc_html__( 'Plugin ACF Pro:', 'makhadane' ),
						esc_html__( 'Aktif', 'makhadane' ),
						esc_html__( 'Status Database:', 'makhadane' ),
						$status_badge,
						$missing_html,
						sprintf(
							/* translators: %d: total jumlah field group di JSON */
							esc_html__( 'File konfigurasi %d Field Group ACF tersimpan di dalam theme (inc/acf-json/field-groups.json). Klik tombol di bawah untuk meng-import atau menyinkronkan Field Group ke dalam database WordPress.', 'makhadane' ),
							$total_groups
						),
						$buttons_html
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ane-general-setting',
					),
				),
			),
			'menu_order'            => 97,
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'makhadane_register_acf_json_and_settings_tab', 20 );

/**
 * Tangani aksi klik tombol "Import JSON ACF" di halaman Makhad Ane Settings.
 *
 * @return void
 */
function makhadane_handle_acf_json_import() {
	if ( empty( $_GET['makhadane_acf_import'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Anda tidak memiliki izin untuk melakukan tindakan ini.', 'makhadane' ) );
	}

	check_admin_referer( 'makhadane_acf_import_action' );

	$mode      = sanitize_key( wp_unslash( $_GET['makhadane_acf_import'] ) );
	$overwrite = ( 'overwrite' === $mode );
	$count     = makhadane_import_acf_json_groups( $overwrite );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'                   => 'ane-general-setting',
				'makhadane_acf_imported' => $count,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'makhadane_handle_acf_json_import' );

/**
 * Tampilkan notifikasi sukses setelah proses Import JSON ACF selesai.
 *
 * @return void
 */
function makhadane_acf_import_admin_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Hanya menampilkan pesan status hasil redirect.
	if ( ! isset( $_GET['makhadane_acf_imported'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$count = absint( wp_unslash( $_GET['makhadane_acf_imported'] ) );
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<?php
			if ( $count > 0 ) {
				printf(
					/* translators: %d: jumlah field group ACF yang berhasil di-import */
					esc_html__( 'Berhasil meng-import / menyinkronkan %d Field Group ACF dari JSON theme ke database.', 'makhadane' ),
					(int) $count
				);
			} else {
				esc_html_e( 'Seluruh Field Group ACF sudah terpasang di database (tidak ada grup baru yang perlu ditambahkan).', 'makhadane' );
			}
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'makhadane_acf_import_admin_notice' );
