<?php
/**
 * Saklar "Matikan Komentar WordPress" (total).
 *
 * Bila aktif, komentar tidak bisa dikirim lewat jalur apa pun: form
 * (`wp-comments-post.php`), REST API, XML-RPC, maupun trackback/pingback.
 * Komentar lama tidak ditampilkan dan menu Komentar/Diskusi disembunyikan.
 * Pengaturan ada di ACF Options `ane-general-setting` (lihat ADR-0006).
 *
 * Default: DIMATIKAN (bila admin belum pernah menyimpan pengaturan ini).
 *
 * @package makhadane
 * @since 4.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Apakah komentar WordPress sedang dimatikan total?
 *
 * Nilai tersimpan `0` = admin sengaja mengaktifkan komentar. Selain itu
 * (termasuk belum pernah disimpan) dianggap dimatikan.
 *
 * @return bool
 */
function makhadane_comments_disabled() {
	static $disabled = null;

	if ( null === $disabled ) {
		$stored   = get_option( 'options_makhadane_disable_comments', '1' );
		$disabled = '0' !== (string) $stored;
	}

	return $disabled;
}

/**
 * Daftarkan field saklar di halaman ACF Options `ane-general-setting`.
 *
 * @return void
 */
function makhadane_register_comments_control_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'        => 'group_makhadane_comments_control',
			'title'      => __( 'Komentar WordPress', 'makhadane' ),
			'menu_order' => -1,
			'fields'     => array(
				array(
					'key'           => 'field_makhadane_disable_comments',
					'label'         => __( 'Matikan Komentar WordPress', 'makhadane' ),
					'name'          => 'makhadane_disable_comments',
					'type'          => 'true_false',
					'instructions'  => __( 'Aktif = komentar dimatikan total: tidak bisa dikirim lewat form, REST API, XML-RPC, maupun trackback/pingback; komentar lama tidak ditampilkan; menu Komentar & Diskusi disembunyikan. Cocok untuk mencegah spam (mis. judi online). Matikan saklar ini hanya jika situs benar-benar membutuhkan komentar WordPress.', 'makhadane' ),
					'ui'            => 1,
					'ui_on_text'    => __( 'Dimatikan', 'makhadane' ),
					'ui_off_text'   => __( 'Aktif', 'makhadane' ),
					'default_value' => 1,
				),
			),
			'location'   => array(
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
add_action( 'acf/init', 'makhadane_register_comments_control_field' );

/**
 * Pasang seluruh penutup komentar bila saklar aktif.
 *
 * Dijalankan pada `init` prioritas 100 agar semua post type sudah terdaftar.
 *
 * @return void
 */
function makhadane_comments_control_bootstrap() {
	if ( ! makhadane_comments_disabled() ) {
		return;
	}

	// Tutup komentar & ping untuk semua konten, sembunyikan komentar lama.
	add_filter( 'comments_open', '__return_false', 20 );
	add_filter( 'pings_open', '__return_false', 20 );
	add_filter( 'comments_array', '__return_empty_array', 20 );
	add_filter( 'get_comments_number', '__return_zero', 20 );
	add_filter( 'feed_links_show_comments_feed', '__return_false' );
	add_filter( 'pre_option_default_comment_status', 'makhadane_comments_closed_status' );
	add_filter( 'pre_option_default_ping_status', 'makhadane_comments_closed_status' );

	// Blokir pengiriman komentar di semua jalur.
	add_action( 'pre_comment_on_post', 'makhadane_comments_block_request', 1 );
	add_filter( 'preprocess_comment', 'makhadane_comments_block_insert', 1 );
	add_filter( 'rest_endpoints', 'makhadane_comments_remove_rest_endpoints' );
	add_filter( 'xmlrpc_methods', 'makhadane_comments_remove_xmlrpc_methods' );
	add_action( 'template_redirect', 'makhadane_comments_block_feed', 1 );
	add_action( 'widgets_init', 'makhadane_comments_unregister_widget', 20 );

	// Hapus dukungan komentar/trackback dari semua post type.
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}

	// Sembunyikan UI komentar di wp-admin.
	add_action( 'admin_menu', 'makhadane_comments_remove_admin_menu', 999 );
	add_action( 'admin_init', 'makhadane_comments_block_admin_pages', 1 );
	add_action( 'wp_dashboard_setup', 'makhadane_comments_remove_dashboard_widget', 999 );
	add_action( 'admin_bar_menu', 'makhadane_comments_remove_admin_bar_node', 999 );
}
add_action( 'init', 'makhadane_comments_control_bootstrap', 100 );

/**
 * Nilai status bawaan untuk konten baru: selalu `closed`.
 *
 * @return string
 */
function makhadane_comments_closed_status() {
	return 'closed';
}

/**
 * Hentikan pengiriman komentar lewat `wp-comments-post.php`.
 *
 * @return void
 */
function makhadane_comments_block_request() {
	wp_die(
		esc_html__( 'Komentar dinonaktifkan di situs ini.', 'makhadane' ),
		esc_html__( 'Komentar Dinonaktifkan', 'makhadane' ),
		array(
			'response'  => 403,
			'back_link' => true,
		)
	);
}

/**
 * Pengaman terakhir: hentikan pembuatan komentar lewat `wp_new_comment()` dari kode mana pun.
 *
 * @param array<string, mixed> $commentdata Data komentar (tidak dipakai).
 * @return array<string, mixed>
 */
function makhadane_comments_block_insert( $commentdata ) {
	unset( $commentdata );
	makhadane_comments_block_request();

	return array();
}

/**
 * Hapus endpoint REST API komentar (baca & tulis).
 *
 * @param array<string, mixed> $endpoints Daftar endpoint REST.
 * @return array<string, mixed>
 */
function makhadane_comments_remove_rest_endpoints( $endpoints ) {
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( (string) $route, '/wp/v2/comments' ) ) {
			unset( $endpoints[ $route ] );
		}
	}

	return $endpoints;
}

/**
 * Hapus metode XML-RPC yang berkaitan dengan komentar & pingback.
 *
 * @param array<string, mixed> $methods Daftar metode XML-RPC.
 * @return array<string, mixed>
 */
function makhadane_comments_remove_xmlrpc_methods( $methods ) {
	$blocked = array(
		'wp.newComment',
		'wp.getComment',
		'wp.getComments',
		'wp.editComment',
		'wp.deleteComment',
		'wp.getCommentCount',
		'wp.getCommentStatusList',
		'pingback.ping',
		'pingback.extensions.getPingbacks',
	);

	foreach ( $blocked as $method ) {
		unset( $methods[ $method ] );
	}

	return $methods;
}

/**
 * Tutup feed komentar (404).
 *
 * @return void
 */
function makhadane_comments_block_feed() {
	if ( is_comment_feed() ) {
		wp_die(
			esc_html__( 'Komentar dinonaktifkan di situs ini.', 'makhadane' ),
			'',
			array( 'response' => 404 )
		);
	}
}

/**
 * Lepas widget Komentar Terbaru.
 *
 * @return void
 */
function makhadane_comments_unregister_widget() {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}

/**
 * Sembunyikan menu Komentar dan submenu Diskusi.
 *
 * @return void
 */
function makhadane_comments_remove_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}

/**
 * Alihkan akses langsung ke halaman admin komentar/diskusi ke dashboard.
 *
 * @return void
 */
function makhadane_comments_block_admin_pages() {
	global $pagenow;

	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php', 'options-discussion.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}

/**
 * Lepas widget dashboard Komentar Terbaru.
 *
 * @return void
 */
function makhadane_comments_remove_dashboard_widget() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}

/**
 * Lepas ikon komentar di admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Instance admin bar.
 * @return void
 */
function makhadane_comments_remove_admin_bar_node( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
