<?php
/**
 * Functions and definitions
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

if ( ! function_exists( 'ane_theme_setup' ) ) :

	function ane_theme_setup() {

		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'kotak', 400, 400, true );
		add_image_size( 'persegi', 800, 1000, true );
		add_image_size( 'backend-persegi', 100, 125, true );
		add_image_size( 'backend-kotak', 100, 100, true );
		add_image_size( 'backend-default', 100, 56, true );
		add_image_size( 'backend-banner', 200, 44, true );
		update_option( 'medium_size_w', 700 );
		update_option( 'medium_size_h', 394 );
		update_option( 'medium_crop', 1 );
		update_option( 'large_size_w', 1000 );
		update_option( 'large_size_h', 563 );
		update_option( 'large_crop', 1 );
		update_option( 'thumbnail_size_w', 400 );
		update_option( 'thumbnail_size_h', 225 );
		update_option( 'thumbnail_crop', 1 );

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
		add_theme_support( 'html5', array( 'gallery', 'caption' ) );
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
add_action( 'after_setup_theme', 'ane_theme_setup' );

if ( ! function_exists( 'makhadane_load_theme_textdomain' ) ) :
	function makhadane_load_theme_textdomain() {
		load_theme_textdomain( 'makhadane', get_template_directory() . '/languages' );
	}
endif;
add_action( 'after_setup_theme', 'makhadane_load_theme_textdomain' );

// add_filter('acf/settings/show_admin', '__return_false');

if ( ! function_exists( 'ane_widgets' ) ) :
	/**
	 * Register widget areas
	 */
	function ane_widgets() {

		register_sidebar(
			array(
				'name'          => 'Default Sidebar',
				'id'            => 'default-sidebar',
				'before_widget' => '<div class="ane-sidebar">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title ane-title">',
				'after_title'   => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'          => 'Blog Type Sidebar',
				'id'            => 'blog-sidebar',
				'before_widget' => '<div class="ane-sidebar">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="widget-title ane-title">',
				'after_title'   => '</h2>',
			)
		);
	}
endif;
add_action( 'widgets_init', 'ane_widgets' );
if ( ! function_exists( 'ane_remove_wp_version_strings' ) ) :
	/**
	 * Remove WordPress version strings from scripts and styles
	 *
	 * @param string $src Script/style source URL.
	 * @return string Modified source URL.
	 */
	function ane_remove_wp_version_strings( $src ) {
		global $wp_version;
		parse_str( parse_url( $src, PHP_URL_QUERY ), $query );
		if ( ! empty( $query['ver'] ) && $query['ver'] === $wp_version ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}
endif;
add_filter( 'script_loader_src', 'ane_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'ane_remove_wp_version_strings' );

if ( ! function_exists( 'ane_remove_meta_version' ) ) :
	/**
	 * Remove WordPress version meta tag
	 *
	 * @return string Empty string.
	 */
	function ane_remove_meta_version() {
		return '';
	}
endif;
add_filter( 'the_generator', 'ane_remove_meta_version' );

/**
 * Load theme includes
 *
 * Auto-load all PHP files from /inc directory and subdirectories
 */
$root_files        = glob( get_template_directory() . '/inc/*.php' );
$subdirectory_files = glob( get_template_directory() . '/inc/**/*.php' );
$all_files         = array_merge( $root_files ?: array(), $subdirectory_files ?: array() );

foreach ( $all_files as $file ) {
	if ( is_file( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) === 'php' ) {
		require_once $file;
	}
}
