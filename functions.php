<?php
/**
 * Setup utama theme Makhad Ane.
 *
 * @package makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAKHADANE_VERSION' ) ) {
	$makhadane_version = wp_get_theme( get_template() )->get( 'Version' );
	define( 'MAKHADANE_VERSION', is_string( $makhadane_version ) && '' !== $makhadane_version ? $makhadane_version : '4.1.3' );
}

if ( ! function_exists( 'makhadane_theme_setup' ) ) :
	/**
	 * Daftarkan fitur standar theme, ukuran gambar, dan lokasi menu.
	 *
	 * @return void
	 */
	function makhadane_theme_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'kotak', 400, 400, true );
		add_image_size( 'persegi', 800, 1000, true );
		add_image_size( 'backend-persegi', 100, 125, true );
		add_image_size( 'backend-kotak', 100, 100, true );
		add_image_size( 'backend-default', 100, 56, true );
		add_image_size( 'backend-banner', 200, 44, true );

		register_nav_menus(
			array(
				'menuutama'  => __( 'Menu Utama', 'makhadane' ),
				'menufooter' => __( 'Menu Footer', 'makhadane' ),
			)
		);

		add_theme_support(
			'post-formats',
			array(
				'video',
				'gallery',
			)
		);

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 97,
				'width'       => 400,
				'flex-width'  => false,
				'flex-height' => false,
			)
		);

		add_theme_support( 'yoast-seo-breadcrumbs' );
	}
endif;
add_action( 'after_setup_theme', 'makhadane_theme_setup' );

if ( ! function_exists( 'makhadane_configure_image_sizes' ) ) :
	/**
	 * Setel ukuran gambar bawaan WordPress hanya saat aktivasi theme atau upgrade versi.
	 *
	 * @return void
	 */
	function makhadane_configure_image_sizes() {
		update_option( 'medium_size_w', 700 );
		update_option( 'medium_size_h', 394 );
		update_option( 'medium_crop', 1 );
		update_option( 'large_size_w', 1000 );
		update_option( 'large_size_h', 563 );
		update_option( 'large_crop', 1 );
		update_option( 'thumbnail_size_w', 400 );
		update_option( 'thumbnail_size_h', 225 );
		update_option( 'thumbnail_crop', 1 );
		update_option( 'makhadane_image_sizes_ver', MAKHADANE_VERSION );
	}
endif;
add_action( 'after_switch_theme', 'makhadane_configure_image_sizes' );

if ( ! function_exists( 'makhadane_maybe_upgrade_image_sizes' ) ) :
	/**
	 * Pastikan ukuran & crop gambar tersetel satu kali setelah auto-update versi baru.
	 *
	 * @return void
	 */
	function makhadane_maybe_upgrade_image_sizes() {
		if ( get_option( 'makhadane_image_sizes_ver' ) !== MAKHADANE_VERSION ) {
			makhadane_configure_image_sizes();
		}
	}
endif;
add_action( 'admin_init', 'makhadane_maybe_upgrade_image_sizes' );

if ( ! function_exists( 'makhadane_load_theme_textdomain' ) ) :
	/**
	 * Muat file terjemahan theme.
	 *
	 * @return void
	 */
	function makhadane_load_theme_textdomain() {
		load_theme_textdomain( 'makhadane', get_template_directory() . '/languages' );
	}
endif;
add_action( 'after_setup_theme', 'makhadane_load_theme_textdomain' );

if ( ! function_exists( 'makhadane_widgets' ) ) :
	/**
	 * Register widget areas.
	 *
	 * @return void
	 */
	function makhadane_widgets() {
		register_sidebar(
			array(
				'name'          => __( 'Default Sidebar', 'makhadane' ),
				'id'            => 'default-sidebar',
				'before_widget' => '<div class="ane-sidebar">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title ane-title">',
				'after_title'   => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Blog Type Sidebar', 'makhadane' ),
				'id'            => 'blog-sidebar',
				'before_widget' => '<div class="ane-sidebar">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title ane-title">',
				'after_title'   => '</h2>',
			)
		);
	}
endif;
add_action( 'widgets_init', 'makhadane_widgets' );

if ( ! function_exists( 'makhadane_remove_wp_version_strings' ) ) :
	/**
	 * Remove WordPress version strings from scripts and styles.
	 *
	 * @param string $src Script/style source URL.
	 * @return string Modified source URL.
	 */
	function makhadane_remove_wp_version_strings( $src ) {
		global $wp_version;

		if ( ! is_string( $src ) || '' === $src ) {
			return $src;
		}

		$query_str = wp_parse_url( $src, PHP_URL_QUERY );
		if ( ! is_string( $query_str ) || '' === $query_str ) {
			return $src;
		}

		wp_parse_str( $query_str, $query );
		if ( ! empty( $query['ver'] ) && $query['ver'] === $wp_version ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}
endif;
add_filter( 'script_loader_src', 'makhadane_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'makhadane_remove_wp_version_strings' );

if ( ! function_exists( 'makhadane_remove_meta_version' ) ) :
	/**
	 * Remove WordPress version meta tag.
	 *
	 * @return string Empty string.
	 */
	function makhadane_remove_meta_version() {
		return '';
	}
endif;
add_filter( 'the_generator', 'makhadane_remove_meta_version' );

/**
 * Load theme includes.
 *
 * Auto-load all PHP files from /inc directory and subdirectories.
 */
$makhadane_root_files   = glob( get_template_directory() . '/inc/*.php' );
$makhadane_subdir_files = glob( get_template_directory() . '/inc/**/*.php' );
$makhadane_all_files    = array_merge(
	is_array( $makhadane_root_files ) ? $makhadane_root_files : array(),
	is_array( $makhadane_subdir_files ) ? $makhadane_subdir_files : array()
);

foreach ( $makhadane_all_files as $makhadane_file ) {
	if ( is_file( $makhadane_file ) && 'php' === pathinfo( $makhadane_file, PATHINFO_EXTENSION ) ) {
		require_once $makhadane_file;
	}
}
