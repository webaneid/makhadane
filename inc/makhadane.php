<?php
/**
 * Fungsi utama theme Makhad Ane (enqueue, helper tampilan, breadcrumbs, views, sosial media).
 *
 * @package makhadane
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.DB.SlowDBQuery, WordPress.DB.DirectDatabaseQuery, Squiz.Commenting, WordPress.WP.I18n.MissingTranslatorsComment, WordPress.WP.I18n.NonSingularStringLiteralSingle, WordPress.WP.I18n.NonSingularStringLiteralPlural, WordPress.DateTime.CurrentTimeTimestamp, Universal.NamingConventions.NoReservedKeywordParameterNames

/**
 * Get theme directory URI
 * Reusable function untuk mendapatkan base URL theme
 *
 * @return string Theme directory URI
 */
function ane_get_theme_uri() {
	return get_template_directory_uri();
}

/**
 * Get asset URL
 * Generate full URL untuk asset (css, js, images, etc)
 *
 * @param string $path Asset path relative to theme directory
 * @return string Full asset URL
 */
function ane_get_asset_url( $path ) {
	$path = ltrim( $path, '/' );
	return esc_url( trailingslashit( ane_get_theme_uri() ) . $path );
}

/**
 * Render inline SVG icon pengganti FontAne dan FontAwesome.
 *
 * @param string $name Nama icon (mendukung prefix 'ane-', 'fa-', 'fa ', dll).
 * @return string Markup HTML <i class="ane-icon"><svg class="ane-svg">...</svg></i>.
 */
function makhadane_icon( $name ) {
	$raw_name = strtolower( trim( (string) $name ) );
	$raw_name = (string) preg_replace( '/^(?:fa[srlbd]?|ane)\s+/', '', $raw_name );
	if ( 0 === strpos( $raw_name, 'fa-' ) ) {
		$raw_name = substr( $raw_name, 3 );
	} elseif ( 0 === strpos( $raw_name, 'ane-' ) ) {
		$raw_name = substr( $raw_name, 4 );
	}

	$aliases = array(
		'clock-o'        => 'clock',
		'jam'            => 'clock',
		'jam-alt'        => 'clock',
		'jam-alt-2'      => 'clock',
		'calendar-o'     => 'calendar',
		'agenda'         => 'calendar',
		'kalender'       => 'calendar',
		'kalender-alt'   => 'calendar',
		'home-x'         => 'home',
		'rumah'          => 'home',
		'building'       => 'profile',
		'building-o'     => 'profile',
		'university'     => 'profile',
		'bank'           => 'profile',
		'institution'    => 'profile',
		'school'         => 'profile',
		'user-circle'    => 'profile',
		'newspaper-o'    => 'news',
		'newspaper'      => 'news',
		'file-text-o'    => 'news',
		'file-text'      => 'news',
		'bullhorn'       => 'pengumuman',
		'rss'            => 'news',
		'graduation-cap' => 'ppdb',
		'mortar-board'   => 'ppdb',
		'toga'           => 'ppdb',
		'user-plus'      => 'ppdb',
		'id-card'        => 'ppdb',
		'id-card-o'      => 'ppdb',
		'pencil-square'  => 'ppdb',
		'edit'           => 'ppdb',
		'pensil'         => 'ppdb',
		'phone'          => 'telepon',
		'phone-square'   => 'telepon',
		'comments'       => 'contact',
		'comments-o'     => 'contact',
		'commenting'     => 'contact',
		'commenting-o'   => 'contact',
		'bubbles'        => 'contact',
		'bubbles3'       => 'contact',
		'envelope'       => 'email',
		'envelope-o'     => 'email',
		'email-alt'      => 'email',
		'address-book'   => 'contact',
		'address-book-o' => 'contact',
		'headphones'     => 'contact',
		'book'           => 'buku',
		'map-marker'     => 'lokasi',
		'lokasi-alt'     => 'lokasi',
		'peta'           => 'lokasi',
		'mobile'         => 'handphone',
		'desktop'        => 'laptop',
		'users'          => 'users',
		'users1'         => 'users',
		'group'          => 'users',
		'user'           => 'users',
		'user1'          => 'users',
		'user-tie'       => 'teacher',
		'male'           => 'users',
		'female'         => 'users',
		'child'          => 'users',
		'trophy'         => 'prestasi',
		'award'          => 'prestasi',
		'certificate'    => 'prestasi',
		'star-o'         => 'star',
		'bintang'        => 'star',
		'futbol-o'       => 'ekstra',
		'basketball'     => 'ekstra',
		'music'          => 'ekstra',
		'musik'          => 'ekstra',
		'paint-brush'    => 'ekstra',
		'picture-o'      => 'galeri',
		'image'          => 'galeri',
		'gambar'         => 'galeri',
		'gallery'        => 'galeri',
		'camera'         => 'galeri',
		'kamera'         => 'galeri',
		'video-camera'   => 'galeri',
		'mata'           => 'eye',
		'mata-alt'       => 'eye',
		'lihat'          => 'eye',
		'lihat-alt'      => 'eye',
		'tutup-alt'      => 'tutup',
		'close'          => 'tutup',
		'times'          => 'tutup',
		'twitter-x'      => 'twitter',
		'website'        => 'link',
		'bumi'           => 'link',
		'sphere'         => 'link',
		'arrow-left'     => 'chevron-left',
		'arrow-right'    => 'chevron-right',
	);

	if ( isset( $aliases[ $raw_name ] ) ) {
		$raw_name = $aliases[ $raw_name ];
	}

	$paths = array(
		'home'                => '<path d="M12 3.1 3.5 10a1 1 0 0 0-.37.78v9.22A1.5 1.5 0 0 0 4.63 21.5h4.62v-6.25a1.25 1.25 0 0 1 1.25-1.25h3a1.25 1.25 0 0 1 1.25 1.25v6.25h4.62a1.5 1.5 0 0 0 1.5-1.5v-9.22a1 1 0 0 0-.37-.78L12 3.1z"/>',
		'profile'             => '<path d="M12 2 2 7v2h20V7L12 2zm-7 9v7H3v2h18v-2h-2v-7h-2v7h-4v-7h-2v7H7v-7H5z"/>',
		'news'                => '<path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>',
		'pengumuman'          => '<path d="M18 11v2h4v-2h-4zm-2 6.61c.96.71 2.21 1.65 3.2 2.39.4-.53.8-1.07 1.2-1.6-.99-.74-2.24-1.68-3.2-2.4-.4.54-.8 1.08-1.2 1.61zM20.4 5.6c-.4-.53-.8-1.07-1.2-1.6-.99.74-2.24 1.68-3.2 2.4.4.53.8 1.07 1.2 1.6.96-.72 2.21-1.65 3.2-2.4zM4 9c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h1v4h2v-4h1l5 3V6L8 9H4zm11.5 3c0-1.33-.58-2.53-1.5-3.35v6.69c.92-.81 1.5-2.01 1.5-3.34z"/>',
		'ppdb'                => '<path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3zm-7 10.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>',
		'contact'             => '<path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-7 11H7v-2h6v2zm4-4H7V7h10v2z"/>',
		'users'               => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>',
		'teacher'             => '<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-4.42 0-8 2.24-8 5v2h16v-2c0-2.76-3.58-5-8-5zm-.5 1.5h1l.7 3.5-.7 1.2-.7-1.2.7-3.5z"/>',
		'prestasi'            => '<path d="M19 5h-2V3H7v2H5a2 2 0 0 0-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V19H7v2h10v-2h-4v-3.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7a2 2 0 0 0-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>',
		'ekstra'              => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 14.93V14h-2v2.93A8.006 8.006 0 0 1 4.07 11H7V9H4.07A8.006 8.006 0 0 1 11 4.07V7h2V4.07A8.006 8.006 0 0 1 19.93 9H17v2h2.93A8.006 8.006 0 0 1 13 16.93z"/>',
		'galeri'              => '<path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>',
		'star'                => '<path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>',
		'clock'               => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>',
		'eye'                 => '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>',
		'linkedin'            => '<path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14zm-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75zM6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68zm1.39 9.94v-8.37H5.5v8.37h2.77z"/>',
		'link'                => '<path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>',
		'check'               => '<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
		'buku'                => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15zm2.5-.5a.5.5 0 0 0 0 1H19v-1H6.5zM8 6h8v2H8V6zm0 4h8v2H8v-2z"/>',
		'search'              => '<path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9z"/>',
		'lokasi'              => '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>',
		'telepon'             => '<path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z"/>',
		'handphone'           => '<path d="M15.5 1h-8A2.5 2.5 0 0 0 5 3.5v17A2.5 2.5 0 0 0 7.5 23h8a2.5 2.5 0 0 0 2.5-2.5v-17A2.5 2.5 0 0 0 15.5 1zm-4 21c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4.5-4H7V4h9v14z"/>',
		'email'               => '<path d="M20 4H4a2 2 0 0 0-1.99 2L2 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/>',
		'laptop'              => '<path d="M20 18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>',
		'whatsapp'            => '<path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm5.82 14.01c-.24.68-1.41 1.3-1.95 1.38-.5.08-1.13.11-1.83-.11-.42-.13-.96-.31-1.65-.61-2.91-1.26-4.81-4.19-4.96-4.38-.14-.19-1.18-1.57-1.18-3s.75-2.13 1.01-2.42c.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.66.5.24.58.82 2.01.89 2.15.07.15.12.31.02.5-.1.19-.15.31-.29.48-.15.17-.31.38-.44.51-.15.15-.3.31-.13.6.17.29.76 1.25 1.63 2.02 1.12.99 2.06 1.3 2.35 1.45.29.15.46.12.63-.07.17-.19.73-.85.92-1.14.19-.29.39-.24.65-.15.27.1 1.7.8 1.99.95.29.15.48.22.56.34.07.12.07.7-.17 1.38z"/>',
		'facebook'            => '<path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/>',
		'twitter'             => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
		'youtube'             => '<path d="M21.58 7.19a2.51 2.51 0 0 0-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42a2.51 2.51 0 0 0-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81a2.51 2.51 0 0 0 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42a2.51 2.51 0 0 0 1.77-1.77C22 15.25 22 12 22 12s0-3.25-.42-4.81zM10 15V9l5.2 3-5.2 3z"/>',
		'instagram'           => '<path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2zm-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6zm9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>',
		'tiktok'              => '<path d="M16.6 5.82s.51.5 0 0A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3s-1.88.09-3.24-1.48z"/>',
		'telegram'            => '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>',
		'threads'             => '<path d="M16.705 11.108c-.162-2.987-1.794-4.697-4.534-4.714-1.652-.01-3.033.69-3.879 1.973L9.8 9.4c.634-.961 1.635-1.16 2.36-1.153.903.006 1.583.268 2.024.78.32.372.535.887.642 1.536-.801-.136-1.664-.178-2.59-.125-2.606.15-4.28 1.67-4.168 3.781.057 1.071.59 1.993 1.502 2.595.77.509 1.764.757 2.795.701 1.363-.075 2.432-.594 3.178-1.545.566-.722.924-1.658 1.082-2.836.65.392 1.13.907 1.397 1.527.452 1.054.478 2.786-.935 4.198-1.238 1.236-2.726 1.772-4.975 1.788-2.495-.018-4.382-.819-5.608-2.378-1.15-1.46-1.743-3.57-1.765-6.269.022-2.7.616-4.809 1.765-6.27 1.226-1.559 3.113-2.359 5.608-2.377 2.513.019 4.432.822 5.706 2.39.625.768 1.095 1.734 1.406 2.86l1.766-.47c-.377-1.387-.969-2.582-1.774-3.573-1.633-2.01-4.033-3.039-7.11-3.06-3.071.021-5.432 1.055-7.019 3.071-1.411 1.795-2.14 4.306-2.164 7.436.024 3.13.753 5.627 2.164 7.422 1.587 2.016 3.96 3.05 7.03 3.071 2.731-.019 4.655-.734 6.24-2.317 2.075-2.073 2.012-4.67 1.329-6.264-.525-1.225-1.57-2.206-2.98-2.81zm-4.438 4.557c-1.142.064-2.328-.448-2.387-1.546-.043-.814.58-1.722 2.457-1.83.854-.049 1.703.009 2.533.174-.216 2.702-1.485 3.14-2.603 3.202z"/>',
		'chevron-right-alt-2' => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1.29 14.29L9.3 14.88 12.17 12 9.3 9.12l1.41-1.41L15 12l-4.29 4.29z"/>',
		'chevron-left'        => '<path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>',
		'chevron-right'       => '<path d="M8.59 16.59 10 18l6-6-6-6-1.41 1.41L13.17 12z"/>',
		'chevron-up'          => '<path d="m7.41 15.41 4.59-4.58 4.59 4.58L18 14l-6-6-6 6z"/>',
		'chevron-down'        => '<path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>',
		'arrow-up-right'      => '<path d="M6 6v2h8.59L6 16.59 7.41 18 16 9.41V18h2V6H6z"/>',
		'tutup'               => '<path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>',
		'calendar'            => '<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>',
		'shield'              => '<path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>',
	);

	if ( ! isset( $paths[ $raw_name ] ) ) {
		if ( '' !== $raw_name ) {
			$raw_name = 'star';
		} else {
			return '';
		}
	}

	return '<i class="ane-icon" aria-hidden="true"><svg class="ane-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false">' . $paths[ $raw_name ] . '</svg></i>';
}

/**
 * Alias kompatibilitas mundur untuk makhadane_icon().
 *
 * @param string $name Nama icon.
 * @return string
 */
function ane_icon( $name ) {
	return makhadane_icon( $name );
}

/**
 * Izinkan elemen <svg> dan <path> pada konteks 'post' di wp_kses_post()
 * agar output inline SVG dari makhadane_icon() tidak terhapus saat escaping.
 *
 * @param array<string, mixed> $tags    Daftar tag HTML yang diizinkan.
 * @param string               $context Konteks kses.
 * @return array<string, mixed>
 */
function makhadane_allow_svg_in_kses( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['svg']  = array(
			'class'       => true,
			'xmlns'       => true,
			'viewbox'     => true,
			'fill'        => true,
			'focusable'   => true,
			'aria-hidden' => true,
			'width'       => true,
			'height'      => true,
		);
		$tags['path'] = array(
			'd'    => true,
			'fill' => true,
		);
	}
	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'makhadane_allow_svg_in_kses', 10, 2 );

/**
 * Add Web App Manifest to head
 * Generate PWA manifest dynamically
 */
function ane_add_web_manifest() {
	$manifest_url = add_query_arg( 'ane_manifest', '1', home_url( '/' ) );
	echo '<link rel="manifest" href="' . esc_url( $manifest_url ) . '">' . "\n";
}
add_action( 'wp_head', 'ane_add_web_manifest', 1 );

/**
 * Generate Web App Manifest JSON
 * Handle dynamic manifest generation for PWA
 */
function ane_generate_manifest() {
	if ( ! isset( $_GET['ane_manifest'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	header( 'Content-Type: application/manifest+json' );

	$theme_color = get_field( 'ane-warna-utama', 'option' );
	$theme_color = $theme_color ? $theme_color : '#1e3a8a';

	$icon_url = has_site_icon() ? get_site_icon_url( 192 ) : ane_get_asset_url( 'img/admin-webane.svg' );

	$manifest = array(
		'name'             => get_bloginfo( 'name' ),
		'short_name'       => get_bloginfo( 'name' ),
		'start_url'        => home_url( '/' ),
		'display'          => 'standalone',
		'background_color' => '#ffffff',
		'theme_color'      => $theme_color,
		'icons'            => array(
			array(
				'src'   => esc_url( $icon_url ),
				'sizes' => '192x192',
				'type'  => 'image/png',
			),
			array(
				'src'   => esc_url( has_site_icon() ? get_site_icon_url( 512 ) : $icon_url ),
				'sizes' => '512x512',
				'type'  => 'image/png',
			),
		),
		'scope'            => home_url( '/' ),
		'categories'       => array( 'education', 'school', 'news', 'blog' ),
		'description'      => get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'Situs resmi ' . get_bloginfo( 'name' ),
		'lang'             => get_bloginfo( 'language' ),
		'dir'              => 'ltr',
	);

	echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'template_redirect', 'ane_generate_manifest' );

/**
 * Enqueue theme styles and scripts.
 *
 * Hanya memuat library eksternal (Owl Carousel & Magnific Popup) pada halaman
 * yang benar-benar membutuhkannya.
 *
 * @return void
 */
function ane_load_css_and_js() {
	global $post;

	$theme_dir    = get_template_directory();
	$main_css_ver = file_exists( $theme_dir . '/css/main.min.css' )
		? (string) filemtime( $theme_dir . '/css/main.min.css' )
		: MAKHADANE_VERSION;
	$main_js_ver  = file_exists( $theme_dir . '/js/makhadane.js' )
		? (string) filemtime( $theme_dir . '/js/makhadane.js' )
		: MAKHADANE_VERSION;

	$needs_owl = is_front_page()
		|| is_home()
		|| is_page_template( array( 'page-home.php', 'page-newslist.php' ) )
		|| is_singular( 'ekstrakurikuler' )
		|| ( is_singular() && isset( $post->post_content ) && has_shortcode( $post->post_content, 'post-block' ) );

	$needs_mfp = is_singular()
		&& ! is_front_page()
		&& ! is_page_template( array( 'page-home.php', 'page-newslist.php', 'page-kontak.php', 'page-linktree.php', 'page-mcategory.php' ) );

	// CSS Files.
	wp_enqueue_style(
		'ane-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,400;1,700&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts CSS2 URL carries its own query parameters.
	);

	if ( $needs_mfp ) {
		wp_enqueue_style(
			'magnific-popup',
			'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css',
			array(),
			'1.1.0'
		);
	}

	wp_enqueue_style(
		'ane-main-style',
		ane_get_asset_url( 'css/main.min.css' ),
		array(),
		$main_css_ver
	);

	// JavaScript Files — conditional library loading.
	$script_deps = array( 'jquery' );

	if ( $needs_owl ) {
		wp_enqueue_script(
			'owl-carousel',
			'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
			array( 'jquery' ),
			'2.3.4',
			true
		);
		$script_deps[] = 'owl-carousel';
	}

	if ( $needs_mfp ) {
		wp_enqueue_script(
			'magnific-popup',
			'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js',
			array( 'jquery' ),
			'1.1.0',
			true
		);
		$script_deps[] = 'magnific-popup';
	}

	wp_enqueue_script(
		'ane-main-script',
		ane_get_asset_url( 'js/makhadane.js' ),
		$script_deps,
		$main_js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ane_load_css_and_js' );

/**
 * Ubah stylesheet non-kritis (Google Fonts & Magnific Popup) menjadi async
 * agar tidak memblokir First Contentful Paint (FCP) dan Largest Contentful Paint (LCP).
 *
 * @param string $html   Tag HTML <link> stylesheet.
 * @param string $handle Nama handle stylesheet.
 * @param string $href   URL stylesheet.
 * @return string
 */
function makhadane_async_non_critical_styles( $html, $handle, $href ) {
	if ( is_admin() ) {
		return $html;
	}

	$async_handles = array( 'ane-google-fonts', 'magnific-popup' );
	if ( ! in_array( $handle, $async_handles, true ) ) {
		return $html;
	}

	$async_html = str_replace(
		array( "media='all'", 'media="all"' ),
		'media="print" onload="this.media=\'all\'"',
		$html
	);

	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- Fallback noscript untuk stylesheet yang sudah di-enqueue via wp_enqueue_style.
	return $async_html . '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>' . "\n";
}
add_filter( 'style_loader_tag', 'makhadane_async_non_critical_styles', 10, 3 );

/**
 * Tambahkan atribut defer pada script frontend non-blocking.
 *
 * @param string $tag    Tag HTML <script>.
 * @param string $handle Nama handle script.
 * @return string
 */
function makhadane_defer_frontend_scripts( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	$defer_handles = array( 'jquery-core', 'jquery-migrate', 'owl-carousel', 'magnific-popup', 'ane-main-script' );
	if ( ! in_array( $handle, $defer_handles, true ) ) {
		return $tag;
	}

	if ( false !== strpos( $tag, ' defer' ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}
add_filter( 'script_loader_tag', 'makhadane_defer_frontend_scripts', 10, 2 );

/**
 * Pusatkan resource hints (maksimal 2 preconnect + dns-prefetch CDN) sesuai standar webane-pagespeed-cwv.
 *
 * @param array<int, string|array<string, string>> $urls          Daftar URL resource hint.
 * @param string                                   $relation_type Jenis relasi (preconnect, dns-prefetch).
 * @return array<int, string|array<string, string>>
 */
function makhadane_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	} elseif ( 'dns-prefetch' === $relation_type ) {
		$urls[] = 'https://cdnjs.cloudflare.com';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'makhadane_resource_hints', 10, 2 );

/**
 * Resolusi ID attachment WordPress dari URL gambar (dengan object cache).
 *
 * @param string $url URL gambar.
 * @return int ID attachment atau 0 jika tidak ditemukan.
 */
function makhadane_get_attachment_id_from_url( $url ) {
	$url = trim( $url );
	if ( '' === $url ) {
		return 0;
	}

	$cache_key = 'att_id_' . md5( $url );
	$cached    = wp_cache_get( $cache_key, 'makhadane_media' );
	if ( false !== $cached ) {
		return (int) $cached;
	}

	$att_id = (int) attachment_url_to_postid( $url );
	if ( 0 === $att_id ) {
		$stripped = (string) preg_replace( '/-\d+x\d+(?=\.[a-zA-Z0-9]+$)/', '', $url );
		if ( '' !== $stripped && $stripped !== $url ) {
			$att_id = (int) attachment_url_to_postid( $stripped );
		}
	}
	wp_cache_set( $cache_key, $att_id, 'makhadane_media', HOUR_IN_SECONDS );

	return $att_id;
}

/**
 * Preload gambar LCP (Hero Slider pertama di beranda atau Featured Image di single post)
 * pada wp_head prioritas 1 sesuai standar webane-pagespeed-cwv.
 *
 * @return void
 */
function makhadane_preload_lcp_image() {
	if ( is_admin() ) {
		return;
	}

	$is_mobile = wp_is_mobile();

	// 1. Beranda / Template Landing Page: preload slide pertama dari home_box_slider.
	if ( is_front_page() || is_page_template( array( 'page-home.php', 'page-kelembagaan.php', 'page-aneprofil.php' ) ) ) {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		$sections = get_field( 'home_content' );
		if ( ! is_array( $sections ) ) {
			return;
		}

		foreach ( $sections as $section ) {
			if ( ! is_array( $section ) || empty( $section['acf_fc_layout'] ) || 'home_box_slider' !== $section['acf_fc_layout'] ) {
				continue;
			}

			if ( empty( $section['home_slider'] ) || ! is_array( $section['home_slider'] ) ) {
				break;
			}

			$first_slide = reset( $section['home_slider'] );
			$raw_bg      = ( is_array( $first_slide ) && ! empty( $first_slide['ane_background'] ) ) ? trim( (string) $first_slide['ane_background'] ) : '';
			if ( '' === $raw_bg ) {
				break;
			}

			$att_id = makhadane_get_attachment_id_from_url( $raw_bg );
			if ( $att_id > 0 ) {
				$size   = $is_mobile ? 'medium' : 'large';
				$src    = wp_get_attachment_image_src( $att_id, $size );
				$href   = ( is_array( $src ) && ! empty( $src[0] ) ) ? (string) $src[0] : $raw_bg;
				$srcset = (string) wp_get_attachment_image_srcset( $att_id, $size );
				if ( '' !== $srcset ) {
					echo '<link rel="preload" as="image" href="' . esc_url( $href ) . '" imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="(max-width: 767px) 240px, 100vw" fetchpriority="high">' . "\n";
				} else {
					echo '<link rel="preload" as="image" href="' . esc_url( $href ) . '" fetchpriority="high">' . "\n";
				}
			} else {
				echo '<link rel="preload" as="image" href="' . esc_url( $raw_bg ) . '" fetchpriority="high">' . "\n";
			}
			break;
		}
		return;
	}

	// 2. Halaman Single Post / Page / CPT: preload featured image utama.
	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		if ( $post_id <= 0 || ! has_post_thumbnail( $post_id ) ) {
			return;
		}

		$post_type = (string) get_post_type( $post_id );
		if ( 'ustadz' === $post_type ) {
			$size = 'kotak';
		} else {
			$size = $is_mobile ? 'thumbnail' : 'large';
		}

		$thumb_id = (int) get_post_thumbnail_id( $post_id );
		$src      = wp_get_attachment_image_src( $thumb_id, $size );
		if ( ! is_array( $src ) || empty( $src[0] ) ) {
			return;
		}

		$srcset = (string) wp_get_attachment_image_srcset( $thumb_id, $size );
		$sizes  = ( 'ustadz' === $post_type ) ? '400px' : '(max-width: 767px) 400px, 1000px';

		if ( '' !== $srcset ) {
			echo '<link rel="preload" as="image" href="' . esc_url( (string) $src[0] ) . '" imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $sizes ) . '" fetchpriority="high">' . "\n";
		} else {
			echo '<link rel="preload" as="image" href="' . esc_url( (string) $src[0] ) . '" fetchpriority="high">' . "\n";
		}
	}
}
add_action( 'wp_head', 'makhadane_preload_lcp_image', 1 );

/**
 * Set excerpt length
 *
 * @param int $length Default excerpt length
 * @return int Excerpt length
 */
function ane_excerpt_length( $length ) {
	unset( $length );
	return 15;
}
add_filter( 'excerpt_length', 'ane_excerpt_length' );

/**
 * Remove shortcodes from excerpt
 *
 * @param string $text Excerpt text
 * @return string Cleaned excerpt
 */
function ane_clean_excerpt( $text ) {
	if ( empty( $text ) ) {
		return $text;
	}

	$pos = strrpos( $text, '[' );
	if ( false === $pos ) {
		return $text;
	}

	return rtrim( substr( $text, 0, $pos ) );
}
add_filter( 'get_the_excerpt', 'ane_clean_excerpt' );

/**
 * Add lightbox functionality to image links
 *
 * @param string $content Post content
 * @return string Modified content
 */
function ane_popup_gallery( $content ) {
	if ( empty( $content ) || ! is_string( $content ) ) {
		return $content;
	}

	global $post;
	if ( ! $post instanceof WP_Post ) {
		return $content;
	}

	$pattern     = '/<a(.*?)href=([\'"])(.*?\.(bmp|gif|jpeg|jpg|png|webp|svg))([\'"])(.*?)>/i';
	$replacement = sprintf(
		'<a$1href=$2$3$5 class="gallery-popup" title="%s"$6>',
		esc_attr( $post->post_title )
	);

	return preg_replace( $pattern, $replacement, $content );
}
add_filter( 'the_content', 'ane_popup_gallery' );

/**
 * Custom image caption shortcode
 *
 * @param array  $attr    Shortcode attributes
 * @param string $content Shortcode content
 * @return string Caption HTML
 */
function ane_theme_caption( $attr, $content = null ) {
	$output = apply_filters( 'img_caption_shortcode', '', $attr, $content );
	if ( ! empty( $output ) ) {
		return $output;
	}

	$attr = shortcode_atts(
		array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => '',
		),
		$attr
	);

	if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
		return $content;
	}

	$id_attr = ! empty( $attr['id'] ) ? 'id="' . esc_attr( $attr['id'] ) . '" ' : '';

	return sprintf(
		'<figure %sclass="wp-caption %s" style="width: %spx">%s<figcaption class="wp-caption-text">%s</figcaption></figure>',
		$id_attr,
		esc_attr( $attr['align'] ),
		esc_attr( $attr['width'] ),
		do_shortcode( $content ),
		$attr['caption']
	);
}
add_shortcode( 'wp_caption', 'ane_theme_caption' );
add_shortcode( 'caption', 'ane_theme_caption' );

/**
 * Get embedded media from content
 *
 * @param array $type Media types to get
 * @return string Embedded media HTML
 */
function ane_get_embedded_media( $type = array() ) {
	$content = do_shortcode( apply_filters( 'the_content', get_the_content() ) );
	$embed   = get_media_embedded_in_content( $content, $type );

	if ( empty( $embed ) ) {
		return '';
	}

	if ( in_array( 'audio', $type, true ) ) {
		return str_replace( '?visual=true', '?visual=false', $embed[0] );
	}

	return $embed[0];
}

/**
 * Get YouTube video thumbnail
 *
 * @param string $url  YouTube URL
 * @param string $size Thumbnail size (empty, 'hq', 'mq', 'sd', 'maxres')
 * @return string Thumbnail URL
 */
function ane_get_youtube_thumbnail( $url, $size = '' ) {
	if ( empty( $url ) ) {
		return '';
	}

	$id = '';

	if ( preg_match( '/iframe/', $url ) ) {
		$id = preg_replace( '/[\s\S]*\/embed\/|"[\s\S]*|\?[\s\S]*/', '', $url );
	} elseif ( preg_match( '/youtu\.be/', $url ) || preg_match( '/embed/', $url ) ) {
		$id = preg_replace( '/^((?![^\/]+$).)*/', '', $url );
	} else {
		$id = preg_replace( '/^((?![^\?v]+$).)*=|&[^\&]*/', '', $url );
	}

	if ( empty( $id ) ) {
		return '';
	}

	$size_suffix = ! empty( $size ) ? $size . 'default.jpg' : 'default.jpg';

	return 'https://img.youtube.com/vi/' . $id . '/' . $size_suffix;
}

/**
 * SEO-friendly pagination for posts
 *
 * @param WP_Query|null $wp_query WordPress Query object
 * @param bool          $echo     Whether to echo or return
 * @return string|null Pagination HTML
 */
function ane_post_pagination( $wp_query = null, $echo = true ) {
	if ( null === $wp_query ) {
		global $wp_query;
	}

	if ( ! $wp_query instanceof WP_Query ) {
		return null;
	}

	if ( $wp_query->max_num_pages <= 1 ) {
		return null;
	}

	$paged = max( 1, absint( get_query_var( 'paged' ) ) );

	$args = array(
		'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
		'format'    => '?paged=%#%',
		'current'   => $paged,
		'total'     => absint( $wp_query->max_num_pages ),
		'type'      => 'array',
		'end_size'  => 2,
		'mid_size'  => 2,
		'prev_text' => makhadane_icon( 'chevron-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous Page', 'makhadane' ) . '</span>',
		'next_text' => makhadane_icon( 'chevron-right' ) . '<span class="screen-reader-text">' . esc_html__( 'Next Page', 'makhadane' ) . '</span>',
	);

	$pages = paginate_links( $args );

	if ( ! is_array( $pages ) || empty( $pages ) ) {
		return null;
	}

	$allowed_html = array(
		'a'    => array(
			'href'         => array(),
			'class'        => array(),
			'aria-current' => array(),
		),
		'span' => array(
			'class'       => array(),
			'aria-hidden' => array(),
		),
		'i'    => array(
			'class'       => array(),
			'aria-hidden' => array(),
		),
		'svg'  => array(
			'class'     => array(),
			'xmlns'     => array(),
			'viewbox'   => array(),
			'fill'      => array(),
			'focusable' => array(),
		),
		'path' => array(
			'd'    => array(),
			'fill' => array(),
		),
	);

	$output = '<nav class="pagination-area" aria-label="' . esc_attr__( 'Posts Navigation', 'makhadane' ) . '"><ul>';

	foreach ( $pages as $page ) {
		$output .= '<li>' . wp_kses( $page, $allowed_html ) . '</li>';
	}

	$output .= '</ul></nav>';

	if ( $echo ) {
		echo wp_kses_post( $output );
		return null;
	}

	return $output;
}

/**
 * Display related posts by tags or categories
 */
function ane_related_post() {
	global $post;

	if ( ! $post ) {
		return;
	}

	$query = null;

	// Try by tags first
	$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );

	if ( ! empty( $tags ) ) {
		$query = new WP_Query(
			array(
				'tag__in'             => $tags,
				'post__not_in'        => array( $post->ID ),
				'posts_per_page'      => 5,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
	}

	// If no posts from tags, try by category
	if ( ! $query || ! $query->have_posts() ) {
		$categories = wp_get_post_categories( $post->ID, array( 'fields' => 'ids' ) );

		if ( ! empty( $categories ) ) {
			$query = new WP_Query(
				array(
					'category__in'        => $categories,
					'post__not_in'        => array( $post->ID ),
					'posts_per_page'      => 5,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
		}
	}

	// If still no posts, show message
	if ( ! $query || ! $query->have_posts() ) {
		echo '<p>' . esc_html__( 'No related posts', 'makhadane' ) . '</p>';
		if ( $query ) {
			wp_reset_postdata();
		}
		return;
	}

	echo '<h2>' . esc_html__( 'Related Posts', 'makhadane' ) . '</h2>';

	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'tp/content', 'list' );
	}

	wp_reset_postdata();
}

/**
 * Display newest posts
 */
function ane_newest_posts() {
	$current_post_id = is_single() ? get_queried_object_id() : 0;

	$query = new WP_Query(
		array(
			'posts_per_page'      => 5,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post__not_in'        => $current_post_id ? array( $current_post_id ) : array(),
		)
	);

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	echo '<h2>' . esc_html__( 'Newest Posts', 'makhadane' ) . '</h2>';
	echo '<div class="isi-konten">';

	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'tp/content', 'list' );
	}

	echo '</div>';

	wp_reset_postdata();
}

/**
 * Display previous and next post navigation
 *
 * @return string Navigation HTML
 */
function ane_prev_next_post() {
	$prev_post = get_previous_post( true );
	$next_post = get_next_post( true );

	if ( ! $prev_post && ! $next_post ) {
		return '';
	}

	$prev_img = '';
	$next_img = '';

	if ( $prev_post ) {
		$prev_img = has_post_thumbnail( $prev_post )
			? get_the_post_thumbnail_url( $prev_post, 'kotak' )
			: ane_dummy_kotak();
	}

	if ( $next_post ) {
		$next_img = has_post_thumbnail( $next_post )
			? get_the_post_thumbnail_url( $next_post, 'kotak' )
			: ane_dummy_kotak();
	}

	ob_start();
	?>
	<div class="ane-col-55 mplr">
		<?php if ( $prev_post ) : ?>
			<div class="ane-kiri">
				<div class="nav-header-prev">
					<span><?php echo wp_kses_post( makhadane_icon( 'chevron-left' ) ); ?> <?php esc_html_e( 'Prev Post', 'makhadane' ); ?></span>
				</div>
				<article class="ane-konten-nav">
					<div class="entry-header">
						<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
							<div class="ane-image">
								<img src="<?php echo esc_url( $prev_img ); ?>" alt="<?php echo esc_attr( get_the_title( $prev_post ) ); ?>" loading="lazy">
							</div>
						</a>
					</div>
					<div class="entry-content">
						<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
							<?php echo esc_html( get_the_title( $prev_post ) ); ?>
						</a>
					</div>
				</article>
			</div>
		<?php endif; ?>

		<?php if ( $next_post ) : ?>
			<div class="ane-kanan">
				<div class="nav-header-prev text-right">
					<span><?php esc_html_e( 'Next Post', 'makhadane' ); ?> <?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?></span>
				</div>
				<article class="ane-konten-nav nav-next">
					<div class="entry-header">
						<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
							<div class="ane-image">
								<img src="<?php echo esc_url( $next_img ); ?>" alt="<?php echo esc_attr( get_the_title( $next_post ) ); ?>" loading="lazy">
							</div>
						</a>
					</div>
					<div class="entry-content">
						<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
							<?php echo esc_html( get_the_title( $next_post ) ); ?>
						</a>
					</div>
				</article>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Convert timestamp to human-readable time ago
 *
 * @param int $seconds Timestamp
 * @return string Time ago string
 */
function ane_time_ago( $seconds ) {
	$seconds  = absint( $seconds );
	$time_now = current_time( 'timestamp' );

	if ( $seconds <= 0 || $seconds > $time_now ) {
		return esc_html__( 'Just now', 'makhadane' );
	}

	$elapsed = $time_now - $seconds;

	$intervals = array(
		31536000 => array( __( '%s year ago', 'makhadane' ), __( '%s years ago', 'makhadane' ) ),
		2628000  => array( __( '%s month ago', 'makhadane' ), __( '%s months ago', 'makhadane' ) ),
		604800   => array( __( '%s week ago', 'makhadane' ), __( '%s weeks ago', 'makhadane' ) ),
		86400    => array( __( '%s day ago', 'makhadane' ), __( '%s days ago', 'makhadane' ) ),
		3600     => array( __( '%s hour ago', 'makhadane' ), __( '%s hours ago', 'makhadane' ) ),
		60       => array( __( '%s minute ago', 'makhadane' ), __( '%s minutes ago', 'makhadane' ) ),
	);

	foreach ( $intervals as $unit => $strings ) {
		if ( $elapsed >= $unit ) {
			$count = floor( $elapsed / $unit );
			return sprintf(
				_n( $strings[0], $strings[1], $count, 'makhadane' ),
				number_format_i18n( $count )
			);
		}
	}

	return esc_html__( 'Just now', 'makhadane' );
}

/**
 * Display social media share buttons
 *
 * @return string Share buttons HTML
 */
function ane_social_share() {
	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$title     = get_the_title( $post_id );
	$permalink = get_permalink( $post_id );
	$excerpt   = get_the_excerpt( $post_id );

	$share_links = array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $permalink ),
		'twitter-x' => 'https://twitter.com/intent/tweet?text=' . rawurlencode( $title ) . '&url=' . rawurlencode( $permalink ),
		'whatsapp'  => 'https://api.whatsapp.com/send?text=' . rawurlencode( $title . "\n\n" . $excerpt . "\n\n" . $permalink ),
		'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( $permalink ) . '&title=' . rawurlencode( $title ),
	);

	ob_start();
	?>
	<div class="social-share-buttons mt-20">
		<ul>
			<?php foreach ( $share_links as $network => $url ) : ?>
				<li>
					<a class="<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer nofollow" aria-label="<?php echo esc_attr( sprintf( __( 'Share on %s', 'makhadane' ), ucfirst( str_replace( '-', ' ', $network ) ) ) ); ?>">
						<?php echo wp_kses_post( makhadane_icon( $network ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	return ob_get_clean();
}
/**
 * Display social media links from ACF options
 *
 * Outputs a list of social media links configured in theme options.
 *
 * @since 4.1.1
 * @return void
 */
function ane_sosial_media() {
	$sosmed = get_field( 'sosmed_ane', 'option' );

	if ( ! $sosmed ) {
		return;
	}

	$sosmed_list = array(
		'sosmed_wa'      => array(
			'class'  => 'whatsapp',
			'prefix' => 'https://wa.me/',
			'icon'   => 'whatsapp',
			'label'  => __( 'WhatsApp', 'makhadane' ),
		),
		'sosmed_fb'      => array(
			'class'  => 'facebook',
			'prefix' => '',
			'icon'   => 'facebook',
			'label'  => __( 'Facebook', 'makhadane' ),
		),
		'sosmed_tw'      => array(
			'class'  => 'twitter',
			'prefix' => '',
			'icon'   => 'twitter',
			'label'  => __( 'Twitter/X', 'makhadane' ),
		),
		'sosmed_youtube' => array(
			'class'  => 'youtube',
			'prefix' => '',
			'icon'   => 'youtube',
			'label'  => __( 'YouTube', 'makhadane' ),
		),
		'sosmed_ig'      => array(
			'class'  => 'instagram',
			'prefix' => '',
			'icon'   => 'instagram',
			'label'  => __( 'Instagram', 'makhadane' ),
		),
		'sosmed_tiktok'  => array(
			'class'  => 'tiktok',
			'prefix' => '',
			'icon'   => 'tiktok',
			'label'  => __( 'TikTok', 'makhadane' ),
		),
	);

	echo '<div class="ane-sosmed"><ul>';

	foreach ( $sosmed_list as $key => $data ) {
		if ( ! empty( $sosmed[ $key ] ) ) {
			$url = $data['prefix'] . $sosmed[ $key ];
			printf(
				'<li><a class="%s" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a></li>',
				esc_attr( $data['class'] ),
				esc_url( $url ),
				esc_attr( $data['label'] ),
				wp_kses_post( makhadane_icon( $data['icon'] ) )
			);
		}
	}

	echo '</ul></div>';
}


/**
 * Display company information from ACF options
 *
 * Outputs company name and slogan from theme options.
 *
 * @since 4.1.1
 * @return void
 */
function ane_about_company() {
	$about = get_field( 'about_ane', 'option' );

	if ( ! $about ) {
		return;
	}

	$judul   = ! empty( $about['company_name'] ) ? $about['company_name'] : '';
	$sologan = ! empty( $about['company_sologan'] ) ? $about['company_sologan'] : '';

	if ( $judul ) {
		echo '<h3>' . esc_html( $judul ) . '</h3>';
	}

	if ( $sologan ) {
		echo '<h4>' . esc_html( $sologan ) . '</h4>';
	}
}

/**
 * Display contact information from ACF options
 *
 * Outputs contact details including address, phone, mobile, email, and website.
 *
 * @since 4.1.1
 * @return void
 */
function ane_contact_info() {
	$contact = get_field( 'contact_ane', 'option' );

	if ( ! $contact ) {
		return;
	}

	$alamat_parts = array_filter(
		array(
			! empty( $contact['kontak_alamat'] ) ? $contact['kontak_alamat'] : '',
			! empty( $contact['kontak_kabupaten'] ) ? $contact['kontak_kabupaten'] : '',
			! empty( $contact['kontak_provinsi'] ) ? $contact['kontak_provinsi'] : '',
			! empty( $contact['kontak_kodepos'] ) ? $contact['kontak_kodepos'] : '',
		)
	);

	$alamat  = ! empty( $alamat_parts ) ? implode( ', ', $alamat_parts ) : '';
	$phone   = ! empty( $contact['kontak_telepon'] ) ? $contact['kontak_telepon'] : '';
	$mobile  = ! empty( $contact['kontak_handphone'] ) ? $contact['kontak_handphone'] : '';
	$email   = ! empty( $contact['kontak_email'] ) ? $contact['kontak_email'] : '';
	$website = ! empty( $contact['kontak_website'] ) ? $contact['kontak_website'] : '';

	echo '<div class="footer-kontak"><ul>';

	if ( $alamat ) {
		echo '<li>' . wp_kses_post( makhadane_icon( 'lokasi' ) ) . ' ' . esc_html( $alamat ) . '</li>';
	}
	if ( $phone ) {
		echo '<li>' . wp_kses_post( makhadane_icon( 'telepon' ) ) . ' ' . esc_html( $phone ) . '</li>';
	}
	if ( $mobile ) {
		echo '<li>' . wp_kses_post( makhadane_icon( 'handphone' ) ) . ' ' . esc_html( $mobile ) . '</li>';
	}
	if ( $email ) {
		echo '<li>' . wp_kses_post( makhadane_icon( 'email' ) ) . ' ' . esc_html( $email ) . '</li>';
	}
	if ( $website ) {
		printf(
			'<li><a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s %s</a></li>',
			esc_url( $website ),
			esc_attr__( 'Visit website', 'makhadane' ),
			wp_kses_post( makhadane_icon( 'laptop' ) ),
			esc_html( $website )
		);
	}

	echo '</ul></div>';
}

/**
 * Get formatted address from ACF contact options
 *
 * Returns the complete address as a string.
 *
 * @since 4.1.1
 * @return string Formatted address or empty string
 */
function ane_get_alamat() {
	$contact = get_field( 'contact_ane', 'option' );

	if ( ! $contact ) {
		return '';
	}

	$alamat_parts = array_filter(
		array(
			! empty( $contact['kontak_alamat'] ) ? $contact['kontak_alamat'] : '',
			! empty( $contact['kontak_kabupaten'] ) ? $contact['kontak_kabupaten'] : '',
			! empty( $contact['kontak_provinsi'] ) ? $contact['kontak_provinsi'] : '',
			! empty( $contact['kontak_kodepos'] ) ? $contact['kontak_kodepos'] : '',
		)
	);

	return ! empty( $alamat_parts ) ? esc_html( implode( ', ', $alamat_parts ) ) : '';
}


/**
 * Get marquee news with transient caching
 *
 * Returns an array of recent posts for marquee display.
 *
 * @since 4.1.1
 * @return array Array of news items with title and url
 */
function ane_get_marquee_news() {
	$cache_key = 'ane_marquee_news';
	$news      = get_transient( $cache_key );

	if ( false === $news ) {
		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);

		$query = new WP_Query( $args );
		$news  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$news[] = array(
					'title' => get_the_title(),
					'url'   => get_permalink(),
				);
			}
			wp_reset_postdata();
		}

		set_transient( $cache_key, $news, MINUTE_IN_SECONDS );
	}

	return $news;
}

/**
 * Add author bio box to single post content
 *
 * Displays author information at the end of single posts only.
 *
 * @since 4.1.1
 * @param string $content Post content
 * @return string Modified content with author bio
 */
function ane_author_info_box( $content ) {
	global $post;

	if ( ! is_singular( 'post' ) || ! isset( $post->post_author ) ) {
		return $content;
	}

	$display_name = get_the_author_meta( 'display_name', $post->post_author );

	if ( empty( $display_name ) ) {
		$display_name = get_the_author_meta( 'nickname', $post->post_author );
	}

	$user_description = get_the_author_meta( 'user_description', $post->post_author );
	$user_website     = get_the_author_meta( 'url', $post->post_author );
	$user_posts       = get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) );

	$author_details = '';

	if ( ! empty( $display_name ) ) {
		$author_details .= sprintf(
			'<h2 class="author_name">%s %s</h2>',
			esc_html__( 'Written By:', 'makhadane' ),
			esc_html( $display_name )
		);
	}

	if ( ! empty( $user_description ) ) {
		$author_details .= sprintf(
			'<div class="author_details">%s%s</div>',
			get_avatar( get_the_author_meta( 'user_email' ), 90 ),
			nl2br( esc_html( $user_description ) )
		);
	}

	$author_details .= sprintf(
		'<div class="author_links"><a href="%s">%s %s</a>',
		esc_url( $user_posts ),
		esc_html( $display_name ),
		esc_html__( 'Posts', 'makhadane' )
	);

	if ( ! empty( $user_website ) ) {
		$author_details .= sprintf(
			' | <a href="%s" target="_blank" rel="nofollow noopener noreferrer">%s</a></div>',
			esc_url( $user_website ),
			esc_html__( 'Website', 'makhadane' )
		);
	} else {
		$author_details .= '</div>';
	}

	$content .= '<footer class="author_bio_section">' . $author_details . '</footer>';

	return $content;
}

add_filter( 'the_content', 'ane_author_info_box' );

/**
 * Generate SEO-friendly breadcrumbs with schema markup
 *
 * Outputs structured breadcrumb navigation with proper BreadcrumbList schema.
 *
 * @since 4.1.1
 * @return void
 */
function ane_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	global $post;
	$home_url    = home_url( '/' );
	$breadcrumbs = array();
	$position    = 1;

	// Home link dengan schema
	$breadcrumbs[] = sprintf(
		'<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
		esc_url( $home_url ),
		esc_html__( 'Home', 'makhadane' ),
		$position
	);

	++$position;

	if ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$post_type     = get_query_var( 'post_type' );
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj ) {
				$breadcrumbs[] = sprintf(
					'<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
					esc_html( $post_type_obj->labels->name ),
					$position
				);
			}
		} elseif ( is_category() ) {
			$category = get_queried_object();

			if ( 0 !== (int) $category->parent ) {
				$parent_cats = array();
				$parent_id   = $category->parent;

				while ( $parent_id ) {
					$parent        = get_category( $parent_id );
					$parent_cats[] = sprintf(
						'<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
						esc_url( get_category_link( $parent->term_id ) ),
						esc_html( $parent->name ),
						$position
					);
					$parent_id     = $parent->parent;
					++$position;
				}

				$breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parent_cats ) );
			}

			$breadcrumbs[] = sprintf(
				'<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
				esc_html( single_cat_title( '', false ) ),
				$position
			);
		} elseif ( is_tag() ) {
			$breadcrumbs[] = sprintf(
				'<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
				esc_html( single_tag_title( '', false ) ),
				$position
			);
		} else {
			$breadcrumbs[] = sprintf(
				'<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
				esc_html( get_the_archive_title() ),
				$position
			);
		}
	} elseif ( is_singular() ) {
		$post_type = get_post_type();

		if ( 'post' !== $post_type && 'page' !== $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj && ! empty( $post_type_obj->has_archive ) ) {
				$breadcrumbs[] = sprintf(
					'<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
					esc_url( get_post_type_archive_link( $post_type ) ),
					esc_html( $post_type_obj->labels->singular_name ),
					$position
				);
				++$position;
			}
		}

		if ( 'post' === $post_type ) {
			$categories = get_the_category();

			if ( ! empty( $categories ) ) {
				$category = $categories[0];

				$breadcrumbs[] = sprintf(
					'<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
					esc_url( get_category_link( $category->term_id ) ),
					esc_html( $category->name ),
					$position
				);
				++$position;
			}
		}

		if ( isset( $post->post_parent ) && $post->post_parent ) {
			$parents   = array();
			$parent_id = $post->post_parent;

			while ( $parent_id ) {
				$parent    = get_post( $parent_id );
				$parents[] = sprintf(
					'<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
					esc_url( get_permalink( $parent->ID ) ),
					esc_html( get_the_title( $parent->ID ) ),
					$position
				);
				$parent_id = $parent->post_parent;
				++$position;
			}

			$breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parents ) );
		}

		$breadcrumbs[] = sprintf(
			'<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
			esc_html( get_the_title() ),
			$position
		);
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elemen di $breadcrumbs sudah di-escape dengan esc_url/esc_html.
	echo '<ul class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">' . implode( '', $breadcrumbs ) . '</ul>';
}


/**
 * Display breadcrumb navigation wrapper
 *
 * Outputs breadcrumb in a container wrapper.
 *
 * @since 4.1.1
 * @return void
 */
function ane_display_breadcrumbs() {
	if ( function_exists( 'ane_breadcrumbs' ) ) {
		?>
		<div class="ane-breadcrumb">
			<div class="ane-container">
				<?php ane_breadcrumbs(); ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Get designed by attribution links
 *
 * Returns footer attribution for Yakaafi Foundation and Webane Indonesia.
 *
 * @since 4.1.1
 * @return string HTML output with attribution links
 */
function ane_get_designed_by() {
	$yakaafi_url = 'https://yakaafi.com/';
	$webane_url  = 'https://webane.com/';

	return sprintf(
		'| %s',
		sprintf(
			__( 'Wakaf digital %1$s, kolaborasi dengan %2$s', 'makhadane' ),
			sprintf(
				'<a id="klik-yakaafi" href="%s" target="_blank" rel="noopener noreferrer" title="%s">%s</a>',
				esc_url( $yakaafi_url ),
				esc_attr__( 'Yakaafi - Yayasan Amal Wakaf Indonesia', 'makhadane' ),
				esc_html__( 'Yakaafi Foundation', 'makhadane' )
			),
			sprintf(
				'<a id="klik-webane" href="%s" target="_blank" rel="noopener noreferrer" title="%s">%s</a>',
				esc_url( $webane_url ),
				esc_attr__( 'Web Design Webane Indonesia', 'makhadane' ),
				esc_html__( 'Webane Indonesia', 'makhadane' )
			)
		)
	);
}

/**
 * Get dynamic copyright year range
 *
 * Returns copyright year range based on first and last published posts.
 *
 * @since 4.1.1
 * @return string Copyright year output
 */
function ane_get_copyright_year() {
	$cached = get_transient( 'makhadane_copyright_year' );
	if ( is_string( $cached ) && '' !== $cached ) {
		return $cached;
	}

	global $wpdb;

	$copyright_dates = $wpdb->get_results(
		"SELECT
            YEAR(MIN(post_date_gmt)) AS firstdate,
            YEAR(MAX(post_date_gmt)) AS lastdate
        FROM {$wpdb->posts}
        WHERE post_status = 'publish'"
	);

	$copyright = '&copy; ' . gmdate( 'Y' );

	if ( ! empty( $copyright_dates ) && isset( $copyright_dates[0]->firstdate ) && ! empty( $copyright_dates[0]->firstdate ) ) {
		$first_year = $copyright_dates[0]->firstdate;
		$last_year  = $copyright_dates[0]->lastdate;

		$copyright = '&copy; ' . $first_year;

		if ( $first_year !== $last_year ) {
			$copyright .= '-' . $last_year;
		}
	}

	set_transient( 'makhadane_copyright_year', $copyright, DAY_IN_SECONDS );

	return $copyright;
}

/**
 * Bersihkan cache tahun copyright saat post disimpan.
 *
 * @return void
 */
function makhadane_flush_copyright_year_cache() {
	delete_transient( 'makhadane_copyright_year' );
}
add_action( 'save_post', 'makhadane_flush_copyright_year_cache' );

/**
 * Filter avatar to use custom ACF image if available
 *
 * Replaces default Gravatar with custom uploaded avatar from ACF field.
 *
 * @since 4.1.1
 * @param string $avatar      HTML for the user's avatar
 * @param mixed  $id_or_email User ID, email address, or WP_User object
 * @param int    $size        Avatar square size in pixels
 * @param string $default     URL for the default avatar image
 * @param string $alt         Alternative text for the avatar image
 * @return string Modified avatar HTML
 */
function ane_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	$size = absint( $size );
	$alt  = esc_attr( $alt );

	$user = ane_get_user_from_id_or_email( $id_or_email );

	if ( ! $user instanceof WP_User ) {
		return $avatar;
	}

	$image_id = get_user_meta( $user->ID, 'gravatar_ane', true );

	if ( empty( $image_id ) ) {
		return $avatar;
	}

	$image_id = absint( $image_id );

	if ( ! wp_attachment_is_image( $image_id ) ) {
		return $avatar;
	}

	$image = wp_get_attachment_image_src( $image_id, 'kotak' );

	if ( ! $image ) {
		return $avatar;
	}

	$attrs = array(
		'alt'     => $alt,
		'src'     => esc_url( $image[0] ),
		'class'   => sprintf( 'avatar avatar-%d', $size ),
		'height'  => $size,
		'width'   => $size,
		'loading' => 'lazy',
	);

	return sprintf( '<img%s />', ane_build_html_attrs( $attrs ) );
}

/**
 * Get WP_User object from various input types
 *
 * Converts ID, email, or object to WP_User.
 *
 * @since 4.1.1
 * @param mixed $id_or_email User ID, email address, or WP_User/WP_Post/WP_Comment object
 * @return WP_User|false User object or false on failure
 */
function ane_get_user_from_id_or_email( $id_or_email ) {
	if ( $id_or_email instanceof WP_User ) {
		return $id_or_email;
	}

	if ( $id_or_email instanceof WP_Post ) {
		return get_user_by( 'id', (int) $id_or_email->post_author );
	}

	if ( $id_or_email instanceof WP_Comment ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			return get_user_by( 'id', (int) $id_or_email->user_id );
		}
		return get_user_by( 'email', $id_or_email->comment_author_email );
	}

	if ( is_numeric( $id_or_email ) ) {
		return get_user_by( 'id', (int) $id_or_email );
	}

	if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		return get_user_by( 'email', $id_or_email );
	}

	return false;
}

/**
 * Build HTML attributes string from array
 *
 * Converts array of attributes to formatted HTML string.
 *
 * @since 4.1.1
 * @param array $attrs Array of attribute key-value pairs
 * @return string Formatted HTML attributes
 */
function ane_build_html_attrs( $attrs ) {
	$html = '';

	foreach ( $attrs as $key => $value ) {
		if ( ! empty( $value ) || '0' === $value ) {
			$html .= sprintf(
				' %s="%s"',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
	}

	return $html;
}

add_filter( 'get_avatar', 'ane_custom_avatar', 10, 5 );

/**
 * Get post view count with formatted text
 *
 * Returns human-readable view count for a post.
 *
 * @since 4.1.1
 * @param int $post_id Optional. Post ID. Defaults to current post.
 * @return string Formatted view count
 */
function ane_get_views( $post_id = null ) {
	if ( ! $post_id ) {
		global $post;
		if ( ! $post ) {
			return __( '0 views', 'makhadane' );
		}
		$post_id = $post->ID;
	}

	$count_key = 'musi_views';
	$count     = (int) get_post_meta( $post_id, $count_key, true );

	return sprintf(
		_n( '%s view', '%s views', $count, 'makhadane' ),
		number_format_i18n( $count )
	);
}

/**
 * Increment post view count with IP-based throttling
 *
 * Prevents duplicate views from same IP within 1 hour.
 *
 * @since 4.1.1
 * @param int $post_id Optional. Post ID. Defaults to current post.
 * @return void
 */
function ane_set_views( $post_id = 0 ) {
	if ( ! $post_id ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$post_id = $post->ID;
	}

	$post_id = absint( $post_id );
	if ( $post_id <= 0 || 'publish' !== get_post_status( $post_id ) ) {
		return;
	}

	$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$count_key  = 'musi_views';
	$view_key   = 'viewed_' . $post_id . '_' . md5( $ip_address );

	if ( false === get_transient( $view_key ) ) {
		$count = (int) get_post_meta( $post_id, $count_key, true );
		++$count;
		update_post_meta( $post_id, $count_key, $count );

		set_transient( $view_key, time(), HOUR_IN_SECONDS );
	}
}

/**
 * AJAX handler for post view increment
 *
 * Processes AJAX request to increment post views.
 *
 * @since 4.1.1
 * @return void
 */
function ane_increment_views_ajax() {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only view counter throttled per IP via transient.
	if ( ! empty( $_GET['postviews_id'] ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( wp_unslash( $_GET['postviews_id'] ) );
		if ( $post_id > 0 && 'publish' === get_post_status( $post_id ) ) {
			ane_set_views( $post_id );
		}
	}
	wp_die();
}
add_action( 'wp_ajax_postviews', 'ane_increment_views_ajax' );
add_action( 'wp_ajax_nopriv_postviews', 'ane_increment_views_ajax' );

/**
 * Localize post views tracking data onto the consolidated main script.
 *
 * @since 4.1.1
 * @return void
 */
function ane_enqueue_postviews_script() {
	global $post;

	if ( is_single() && isset( $post->ID ) ) {
		wp_localize_script(
			'ane-main-script',
			'postViewsCache',
			array(
				'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
				'post_id'        => (int) $post->ID,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_postviews_script', 20 );

// Tambahkan kolom di admin post dan custom post type
add_filter( 'manage_posts_columns', 'musi_add_views_column' );
add_filter( 'manage_edit-post_sortable_columns', 'musi_make_views_column_sortable' );
add_action( 'manage_posts_custom_column', 'musi_show_views_column', 10, 2 );

function musi_add_views_column( $columns ) {
	$columns['musi_post_views'] = __( 'Views', 'makhadane' );
	return $columns;
}

function musi_show_views_column( $column_name, $post_id ) {
	if ( $column_name === 'musi_post_views' ) {
		$views_count = (int) get_post_meta( $post_id, 'musi_views', true );
		echo esc_html( (string) $views_count );
	}
}

// Jadikan kolom views sortable
function musi_make_views_column_sortable( $columns ) {
	$columns['musi_post_views'] = 'musi_views';
	return $columns;
}

// Custom sorting untuk kolom views
add_action( 'pre_get_posts', 'musi_sort_views_column' );
function musi_sort_views_column( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );
	if ( 'musi_views' === $orderby ) {
		$query->set( 'meta_key', 'musi_views' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}
