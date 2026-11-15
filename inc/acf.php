<?php
/**
 * Integrasi ACF, Google Maps API Key, dan CSS Variable Warna Dinamis.
 *
 * @package makhadane
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Deregister stylesheet bawaan ACF di front-end.
 *
 * @return void
 */
function my_deregister_styles() {
	wp_deregister_style( 'acf' );
	wp_deregister_style( 'acf-field-group' );
	wp_deregister_style( 'acf-global' );
	wp_deregister_style( 'acf-input' );
	wp_deregister_style( 'acf-datepicker' );
}
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );

/**
 * Dekripsi kunci default Google Maps bawaan Webane yang disimpan dalam
 * format XOR-Hex agar tidak terpapar sebagai plain-text di repositori.
 *
 * @return string
 */
function makhadane_get_default_gmaps_key() {
	$cipher_hex = '2c281109321d220f504416325717182875550955251f1900725559325e230c382835182e26162c';
	$mask       = 'makhadane-webane-gmaps-v1';
	$mask_len   = strlen( $mask );
	$hex_len    = strlen( $cipher_hex );
	$decoded    = '';

	for ( $i = 0; $i < $hex_len; $i += 2 ) {
		$byte     = (int) hexdec( substr( $cipher_hex, $i, 2 ) );
		$mask_chr = ord( $mask[ (int) ( $i / 2 ) % $mask_len ] );
		$decoded .= chr( $byte ^ $mask_chr );
	}

	return $decoded;
}

/**
 * Ambil Google Maps API Key dari konstanta wp-config.php, ACF option,
 * atau fallback default bawaan Webane (XOR-Hex).
 *
 * @return string API key Google Maps.
 */
function makhadane_get_google_maps_api_key() {
	if ( defined( 'MAKHADANE_GMAPS_API_KEY' ) && is_string( MAKHADANE_GMAPS_API_KEY ) ) {
		$key = trim( MAKHADANE_GMAPS_API_KEY );
		if ( '' !== $key ) {
			return $key;
		}
	}

	if ( function_exists( 'get_field' ) ) {
		$acf_key = get_field( 'ane_google_maps_api_key', 'option' );
		if ( is_string( $acf_key ) && '' !== trim( $acf_key ) ) {
			return trim( $acf_key );
		}
	}

	return makhadane_get_default_gmaps_key();
}

/**
 * Daftarkan Google Maps API Key pada pengaturan ACF.
 *
 * @return void
 */
function my_acf_init() {
	$gmaps_key = makhadane_get_google_maps_api_key();
	if ( '' !== $gmaps_key && function_exists( 'acf_update_setting' ) ) {
		acf_update_setting( 'google_api_key', $gmaps_key );
	}
}
add_action( 'acf/init', 'my_acf_init' );

/**
 * Daftarkan tab dan field API Keys pada halaman ACF Options General Setting.
 *
 * @return void
 */
function makhadane_register_api_keys_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_makhadane_api_keys',
			'title'                 => __( 'API Keys', 'makhadane' ),
			'fields'                => array(
				array(
					'key'       => 'field_makhadane_api_keys_tab',
					'label'     => __( 'API Keys', 'makhadane' ),
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'          => 'field_makhadane_gmaps_api_key',
					'label'        => __( 'Google Maps API Key', 'makhadane' ),
					'name'         => 'ane_google_maps_api_key',
					'type'         => 'password',
					'instructions' => __( 'Opsional. Isi jika ingin menggunakan Google Maps API Key milik sendiri, atau definisikan konstanta MAKHADANE_GMAPS_API_KEY di wp-config.php.', 'makhadane' ),
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
			'menu_order'            => 98,
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'makhadane_register_api_keys_fields' );

/**
 * Enqueue script Google Maps hanya pada halaman Kontak.
 *
 * @return void
 */
function ane_load_gmap_script() {
	if ( ! is_page_template( 'page-kontak.php' ) ) {
		return;
	}

	$gmaps_key = makhadane_get_google_maps_api_key();
	if ( '' === $gmaps_key ) {
		return;
	}

	wp_enqueue_script(
		'googlemap',
		'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( $gmaps_key ) . '&callback=Function.prototype',
		array(),
		null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Maps JS API URL.
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ane_load_gmap_script' );

/**
 * Konversi kode warna hex (#rrggbb atau #rgb) ke format string RGB "r, g, b".
 *
 * @param string $colour Warna hex.
 * @return string|false String RGB atau false jika format tidak valid.
 */
function hex2rgb( $colour ) {
	if ( ! is_string( $colour ) || '' === $colour ) {
		return false;
	}

	if ( '#' === $colour[0] ) {
		$colour = substr( $colour, 1 );
	}

	if ( 6 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( 3 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	return "$r, $g, $b";
}

/**
 * Cetak custom CSS variables warna tema di <head> dengan sanitasi warna hex.
 *
 * @return void
 */
function makhadane_output_custom_colors() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$colors = array( 'text', 'gelap', 'utama', 'terang', 'alternatif', 'utama-2', 'putih' );
	$rules  = '';

	foreach ( $colors as $name ) {
		$raw_color = get_field( 'ane-warna-' . $name, 'option' );
		$hex_color = is_string( $raw_color ) ? sanitize_hex_color( $raw_color ) : '';

		if ( is_string( $hex_color ) && '' !== $hex_color ) {
			$rgb    = hex2rgb( $hex_color );
			$rules .= '--ane-warna-' . sanitize_key( $name ) . ': ' . $hex_color . ';';
			if ( false !== $rgb ) {
				$rules .= '--ane-warna-' . sanitize_key( $name ) . '-rgb: ' . $rgb . ';';
			}
		}
	}

	if ( '' === $rules ) {
		return;
	}
	?>
	<style id="ane-colors">
		:root {
			<?php echo esc_html( $rules ); ?>
		}
	</style>
	<?php
}
add_action( 'wp_head', 'makhadane_output_custom_colors', 999 );
