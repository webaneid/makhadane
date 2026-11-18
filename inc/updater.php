<?php
/**
 * GitHub Auto-Updater — Makhad Ane.
 *
 * Mengecek rilis terbaru di repository GitHub privat/publik, menampilkan
 * notifikasi update di wp-admin, serta menangani unduhan dan instalasi paket ZIP rilis.
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAKHADANE_UPDATER_REPO' ) ) {
	define( 'MAKHADANE_UPDATER_REPO', 'webaneid/makhadane' );
}

if ( ! defined( 'MAKHADANE_UPDATER_SLUG' ) ) {
	define( 'MAKHADANE_UPDATER_SLUG', 'makhadane' );
}

/**
 * Ambil GitHub Personal Access Token untuk autentikasi rilis privat.
 *
 * Prioritas:
 * 1. Konstanta MAKHADANE_UPDATER_TOKEN di wp-config.php.
 * 2. Field ACF option `ane_updater_token`.
 *
 * @return string Token GitHub atau string kosong.
 */
function makhadane_updater_get_token() {
	if ( defined( 'MAKHADANE_UPDATER_TOKEN' ) && is_string( MAKHADANE_UPDATER_TOKEN ) ) {
		$config_token = trim( MAKHADANE_UPDATER_TOKEN );
		if ( '' !== $config_token ) {
			return $config_token;
		}
	}

	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	return trim( (string) get_field( 'ane_updater_token', 'option' ) );
}

/**
 * Ambil versi theme yang sedang terpasang dari header style.css.
 *
 * @return string Versi theme.
 */
function makhadane_updater_get_theme_version() {
	$theme   = wp_get_theme( MAKHADANE_UPDATER_SLUG );
	$version = $theme->get( 'Version' );

	return is_string( $version ) ? $version : '0.0.0';
}

/**
 * Ambil metadata rilis terbaru dari GitHub API (dengan cache transient 12 jam).
 *
 * @return array<string, string>|false Array data rilis atau false jika gagal/belum dikonfigurasi.
 */
function makhadane_updater_get_release() {
	$cached = get_transient( 'makhadane_github_release' );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : false;
	}

	$token   = makhadane_updater_get_token();
	$headers = array(
		'Accept'     => 'application/vnd.github.v3+json',
		'User-Agent' => 'Makhadane-Updater',
	);

	if ( '' !== $token ) {
		$headers['Authorization'] = 'token ' . $token;
	}

	$url = 'https://api.github.com/repos/' . MAKHADANE_UPDATER_REPO . '/releases/latest';

	$response = wp_remote_get(
		$url,
		array(
			'headers' => $headers,
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( 'makhadane_github_release', 'error', 15 * MINUTE_IN_SECONDS );
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ) );
	if ( ! is_object( $body ) || empty( $body->tag_name ) ) {
		return false;
	}

	// Cari asset .zip dari rilis (gunakan API URL jika pakai token privat, atau browser_download_url jika publik).
	$asset_url = '';
	if ( ! empty( $body->assets ) && is_array( $body->assets ) ) {
		foreach ( $body->assets as $asset ) {
			if ( is_object( $asset ) && ! empty( $asset->name ) && ! empty( $asset->url ) ) {
				if ( '.zip' === substr( (string) $asset->name, -4 ) ) {
					if ( '' === $token && ! empty( $asset->browser_download_url ) ) {
						$asset_url = (string) $asset->browser_download_url;
					} else {
						$asset_url = (string) $asset->url;
					}
					break;
				}
			}
		}
	}

	// Jangan fallback ke zipball (nama folder tidak sesuai slug + isu autentikasi).
	if ( '' === $asset_url ) {
		return false;
	}

	$release = array(
		'version'   => ltrim( (string) $body->tag_name, 'vV' ),
		'tag'       => (string) $body->tag_name,
		'url'       => isset( $body->html_url ) ? (string) $body->html_url : '',
		'package'   => $asset_url,
		'body'      => isset( $body->body ) ? (string) $body->body : '',
		'published' => isset( $body->published_at ) ? (string) $body->published_at : '',
	);

	set_transient( 'makhadane_github_release', $release, 12 * HOUR_IN_SECONDS );

	return $release;
}

/**
 * Suntikkan informasi update ke transient `update_themes` WordPress.
 *
 * @param object $transient Objek transient update_themes.
 * @return object Objek transient yang sudah diperbarui.
 */
function makhadane_updater_check( $transient ) {
	if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
		return $transient;
	}

	$release = makhadane_updater_get_release();
	if ( ! is_array( $release ) ) {
		return $transient;
	}

	$current_version = makhadane_updater_get_theme_version();

	if ( version_compare( $release['version'], $current_version, '>' ) ) {
		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}

		$transient->response[ MAKHADANE_UPDATER_SLUG ] = array(
			'theme'       => MAKHADANE_UPDATER_SLUG,
			'new_version' => $release['version'],
			'url'         => $release['url'],
			'package'     => $release['package'],
		);
	}

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'makhadane_updater_check' );

/**
 * Sediakan detail informasi theme & changelog pada popup wp-admin.
 *
 * @param false|object|array<string, mixed> $result Nilai hasil default.
 * @param string                            $action Nama aksi themes_api.
 * @param object                            $args   Argumen request.
 * @return false|object|array<string, mixed>
 */
function makhadane_updater_info( $result, $action, $args ) {
	if ( 'theme_information' !== $action || ! is_object( $args ) || ! isset( $args->slug ) || MAKHADANE_UPDATER_SLUG !== $args->slug ) {
		return $result;
	}

	$release = makhadane_updater_get_release();
	if ( ! is_array( $release ) ) {
		return $result;
	}

	$theme = wp_get_theme( MAKHADANE_UPDATER_SLUG );

	return (object) array(
		'name'          => $theme->get( 'Name' ),
		'slug'          => MAKHADANE_UPDATER_SLUG,
		'version'       => $release['version'],
		'author'        => $theme->get( 'Author' ),
		'homepage'      => $theme->get( 'ThemeURI' ),
		'requires'      => '6.0',
		'tested'        => '6.7',
		'requires_php'  => '8.0',
		'downloaded'    => 0,
		'last_updated'  => $release['published'],
		'sections'      => array(
			'description' => (string) $theme->get( 'Description' ),
			'changelog'   => nl2br( esc_html( $release['body'] ) ),
		),
		'download_link' => $release['package'],
	);
}
add_filter( 'themes_api', 'makhadane_updater_info', 20, 3 );

/**
 * Sisipkan header Authorization khusus untuk unduhan asset rilis repo ini.
 *
 * @param array<string, mixed> $args Argumen HTTP request.
 * @param string               $url  URL tujuan request.
 * @return array<string, mixed>
 */
function makhadane_updater_auth_header( $args, $url ) {
	$parts  = wp_parse_url( $url );
	$scheme = isset( $parts['scheme'] ) ? strtolower( (string) $parts['scheme'] ) : '';
	$host   = isset( $parts['host'] ) ? strtolower( (string) $parts['host'] ) : '';
	$path   = isset( $parts['path'] ) ? (string) $parts['path'] : '';

	if ( 'https' !== $scheme ) {
		return $args;
	}

	// Host harus persis api.github.com / github.com, bukan sekadar memuat string repo.
	$is_api    = 'api.github.com' === $host && 0 === strpos( $path, '/repos/' . MAKHADANE_UPDATER_REPO . '/' );
	$is_github = 'github.com' === $host && 0 === strpos( $path, '/' . MAKHADANE_UPDATER_REPO . '/' );

	if ( ! $is_api && ! $is_github ) {
		return $args;
	}

	$token = makhadane_updater_get_token();
	if ( '' === $token ) {
		return $args;
	}

	if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
		$args['headers'] = array();
	}

	$args['headers']['Authorization'] = 'token ' . $token;

	// Unduhan asset via API GitHub wajib memakai Accept: application/octet-stream.
	if ( $is_api && false !== strpos( $url, '/releases/assets/' ) ) {
		$args['headers']['Accept'] = 'application/octet-stream';
	}

	return $args;
}
add_filter( 'http_request_args', 'makhadane_updater_auth_header', 10, 2 );

/**
 * Pastikan nama folder hasil ekstraksi ZIP sesuai dengan slug theme.
 *
 * @param string               $source        Path folder sumber yang diekstrak.
 * @param string               $remote_source Path direktori sementara.
 * @param WP_Upgrader          $upgrader      Instance upgrader WordPress.
 * @param array<string, mixed> $hook_extra    Konteks ekstra proses upgrade.
 * @return string|WP_Error
 */
function makhadane_updater_rename_source( $source, $remote_source, $upgrader, $hook_extra ) {
	global $wp_filesystem;

	if ( ! isset( $hook_extra['theme'] ) || MAKHADANE_UPDATER_SLUG !== $hook_extra['theme'] ) {
		return $source;
	}

	$correct_dir = trailingslashit( $remote_source ) . MAKHADANE_UPDATER_SLUG;

	if ( trailingslashit( $source ) === trailingslashit( $correct_dir ) ) {
		return $source;
	}

	if ( is_object( $wp_filesystem ) && method_exists( $wp_filesystem, 'move' ) ) {
		$moved = $wp_filesystem->move( $source, $correct_dir, true );
		if ( $moved ) {
			return trailingslashit( $correct_dir );
		}
	}

	return new WP_Error(
		'makhadane_rename_failed',
		__( 'Gagal menyesuaikan nama folder theme saat update.', 'makhadane' )
	);
}
add_filter( 'upgrader_source_selection', 'makhadane_updater_rename_source', 10, 4 );

/**
 * Bersihkan transient rilis setelah proses update theme selesai.
 *
 * @param WP_Upgrader          $upgrader Instance upgrader.
 * @param array<string, mixed> $options  Opsi proses upgrade.
 * @return void
 */
function makhadane_updater_clear_cache( $upgrader, $options ) {
	if ( isset( $options['action'], $options['type'] ) && 'update' === $options['action'] && 'theme' === $options['type'] ) {
		delete_transient( 'makhadane_github_release' );
		delete_transient( 'makhadane_update_check' );
	}
}
add_action( 'upgrader_process_complete', 'makhadane_updater_clear_cache', 10, 2 );

/**
 * Daftarkan tab dan field Auto Update pada halaman ACF Options theme.
 *
 * @return void
 */
function makhadane_register_updater_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$version        = makhadane_updater_get_theme_version();
	$release        = get_transient( 'makhadane_github_release' );
	$changelog_html = '';

	if ( is_array( $release ) && ! empty( $release['version'] ) ) {
		$status = version_compare( $release['version'], $version, '>' )
			? '<span style="color:#e65100;font-weight:700;">' . sprintf(
				/* translators: %s: nomor versi terbaru */
				esc_html__( 'Update tersedia: v%s', 'makhadane' ),
				esc_html( $release['version'] )
			) . '</span>'
			: '<span style="color:#264113;">' . esc_html__( 'Sudah versi terbaru.', 'makhadane' ) . '</span>';

		$changelog_html = '<br><br><strong>' . esc_html__( 'Status:', 'makhadane' ) . '</strong> ' . $status;

		if ( ! empty( $release['body'] ) ) {
			$changelog_html .= '<br><br><strong>' . sprintf(
				/* translators: %s: nomor versi rilis */
				esc_html__( 'Changelog (v%s):', 'makhadane' ),
				esc_html( $release['version'] )
			) . '</strong><br>'
				. '<div style="background:#f7f7f7;border:1px solid #ddd;border-radius:8px;padding:12px 16px;margin-top:6px;font-size:13px;line-height:1.6;">'
				. nl2br( esc_html( $release['body'] ) )
				. '</div>';
		}
	}

	$force_url = wp_nonce_url(
		admin_url( 'themes.php?makhadane_force_check=1' ),
		'makhadane_updater_action'
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_makhadane_updater',
			'title'                 => __( 'Auto Update', 'makhadane' ),
			'fields'                => array(
				array(
					'key'       => 'field_makhadane_updater_tab',
					'label'     => __( 'Update', 'makhadane' ),
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'     => 'field_makhadane_updater_info',
					'label'   => __( 'Informasi', 'makhadane' ),
					'type'    => 'message',
					'message' => sprintf(
						'<strong>%s</strong> %s<br><strong>%s</strong> %s (private)<br><br>%s',
						esc_html__( 'Versi saat ini:', 'makhadane' ),
						esc_html( $version ),
						esc_html__( 'Repo:', 'makhadane' ),
						esc_html( MAKHADANE_UPDATER_REPO ),
						esc_html__( 'Theme otomatis mengecek update dari GitHub setiap 12 jam. Jika ada versi baru, notifikasi akan muncul di Appearance → Themes.', 'makhadane' )
					) . $changelog_html
					. '<br><br><a href="' . esc_url( $force_url ) . '" class="button button-secondary">' . esc_html__( 'Cek Update Sekarang', 'makhadane' ) . '</a>',
				),
				array(
					'key'          => 'field_makhadane_updater_token',
					'label'        => __( 'GitHub Personal Access Token', 'makhadane' ),
					'name'         => 'ane_updater_token',
					'type'         => 'password',
					'instructions' => sprintf(
						/* translators: %s: nama repository GitHub */
						__( 'Buat Fine-grained Token di GitHub Settings dengan permission Contents: Read-only pada repo %s, atau definisikan konstanta MAKHADANE_UPDATER_TOKEN di wp-config.php.', 'makhadane' ),
						'<code>' . esc_html( MAKHADANE_UPDATER_REPO ) . '</code>'
					),
					'placeholder'  => 'github_pat_xxxxx',
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
			'menu_order'            => 99,
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'makhadane_register_updater_fields' );

/**
 * Bersihkan cache update saat admin menyimpan halaman ACF Options.
 *
 * @param int|string $post_id ID post atau 'options'.
 * @return void
 */
function makhadane_updater_on_save_options( $post_id ) {
	if ( 'options' !== $post_id ) {
		return;
	}

	delete_transient( 'makhadane_github_release' );
	delete_transient( 'makhadane_update_check' );
	delete_site_transient( 'update_themes' );
}
add_action( 'acf/save_post', 'makhadane_updater_on_save_options', 20 );

/**
 * Tangani permintaan "Cek Update Sekarang" (dengan verifikasi capability & nonce).
 *
 * Dijalankan pada prioritas 1 di `admin_init` (sebelum `_maybe_update_themes`
 * pada prioritas 10) dan langsung memanggil `wp_update_themes()` agar notifikasi
 * update tampil seketika di halaman `themes.php` tanpa perlu refresh kedua.
 *
 * @return void
 */
function makhadane_updater_force_check_handler() {
	if ( ! isset( $_GET['makhadane_force_check'] ) ) {
		return;
	}

	if ( ! current_user_can( 'update_themes' ) ) {
		return;
	}

	check_admin_referer( 'makhadane_updater_action' );

	delete_transient( 'makhadane_github_release' );
	delete_transient( 'makhadane_update_check' );
	delete_site_transient( 'update_themes' );
	wp_update_themes();

	add_action( 'admin_notices', 'makhadane_updater_force_check_notice' );
}
add_action( 'admin_init', 'makhadane_updater_force_check_handler', 1 );

/**
 * Tampilkan admin notice setelah cache update dibersihkan dan dicek ulang.
 *
 * @return void
 */
function makhadane_updater_force_check_notice() {
	$release = makhadane_updater_get_release();
	$current = makhadane_updater_get_theme_version();

	if ( ! is_array( $release ) || empty( $release['version'] ) ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'Gagal mengambil informasi rilis dari GitHub. Pastikan GitHub Personal Access Token sudah diisi dengan benar di Makhad Ane → General Setting → Tab Update (izin Contents: Read-only).', 'makhadane' ); ?></p>
		</div>
		<?php
		return;
	}

	if ( version_compare( $release['version'], $current, '>' ) ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php
				printf(
					/* translators: 1: versi terbaru, 2: versi terpasang */
					esc_html__( 'Pembaruan ditemukan! Versi terbaru v%1$s sudah siap diinstal (versi Anda saat ini: v%2$s). Silakan klik tombol Perbarui Sekarang pada kartu tema Makhad Ane di bawah.', 'makhadane' ),
					esc_html( $release['version'] ),
					esc_html( $current )
				);
				?>
			</p>
		</div>
		<?php
		return;
	}
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<?php
			printf(
				/* translators: %s: versi terpasang */
				esc_html__( 'Pengecekan update selesai. Tema Makhad Ane Anda sudah menggunakan versi terbaru (v%s).', 'makhadane' ),
				esc_html( $current )
			);
			?>
		</p>
	</div>
	<?php
}
