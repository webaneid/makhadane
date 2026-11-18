<?php
/**
 * Integrasi Google, Search Console, Yandex, Bing, Meta & TikTok.
 *
 * Admin cukup mengisi ID pelacakan atau kode verifikasi di halaman
 * "Makhad Ane Settings → Script" (ACF Options `ane-general-setting`). Theme
 * mengekstrak & memvalidasi ID dengan regex ketat, lalu mencetak snippet resmi
 * di `wp_head` dan `wp_body_open` HANYA bila ID terisi. Admin tidak lagi
 * menempel script mentah (lihat ADR-0005).
 *
 * @package makhadane
 * @since 4.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Peta nama field ACF tracking => key field ACF (sesuai inc/acf-json/field-groups.json).
 *
 * @return array<string, string>
 */
function makhadane_tracking_field_keys() {
	return array(
		'makhadane_gsc_verification'    => 'field_makhadane_trk_gsc',
		'makhadane_gtm_id'              => 'field_makhadane_trk_gtm',
		'makhadane_ga4_id'              => 'field_makhadane_trk_ga4',
		'makhadane_yandex_verification' => 'field_makhadane_trk_yandex_verify',
		'makhadane_yandex_metrica_id'   => 'field_makhadane_trk_yandex_metrica',
		'makhadane_bing_verification'   => 'field_makhadane_trk_bing',
		'makhadane_fb_pixel_id'         => 'field_makhadane_trk_fb_pixel',
		'makhadane_fb_domain_verify'    => 'field_makhadane_trk_fb_domain',
		'makhadane_tiktok_pixel_id'     => 'field_makhadane_trk_tiktok',
	);
}

/**
 * Ekstrak kode verifikasi murni dari input yang mungkin berupa tag `<meta ... content="KODE" />`
 * atau hanya `KODE`.
 *
 * @param string $raw Input mentah dari admin.
 * @return string Kode verifikasi bersih.
 */
function makhadane_extract_verification_token( $raw ) {
	$text = trim( (string) $raw );
	if ( '' === $text ) {
		return '';
	}

	if ( preg_match( '/content\s*=\s*["\']([^"\']+)["\']/i', $text, $found ) && ! empty( $found[1] ) ) {
		$text = trim( $found[1] );
	}

	return (string) preg_replace( '/[^a-zA-Z0-9_\-\.]/', '', $text );
}

/**
 * Bersihkan & validasi satu nilai field tracking/verifikasi berdasarkan nama field-nya.
 *
 * Dipakai saat penyimpanan (`acf/update_value`), saat pembacaan di front-end,
 * dan saat migrasi dari script lama.
 *
 * @param string $name Nama field ACF (mis. `makhadane_gtm_id`).
 * @param string $raw  Nilai mentah.
 * @return string Nilai bersih yang tervalidasi (string kosong bila tidak valid).
 */
function makhadane_clean_tracking_field( $name, $raw ) {
	$text = trim( (string) $raw );
	if ( '' === $text ) {
		return '';
	}

	switch ( $name ) {
		case 'makhadane_gsc_verification':
		case 'makhadane_yandex_verification':
		case 'makhadane_bing_verification':
		case 'makhadane_fb_domain_verify':
			return makhadane_extract_verification_token( $text );

		case 'makhadane_gtm_id':
			return preg_match( '/(GTM-[A-Z0-9]+)/i', $text, $found ) ? strtoupper( $found[1] ) : '';

		case 'makhadane_ga4_id':
			return preg_match( '/((?:G|AW|DC)-[A-Z0-9\-]+)/i', $text, $found ) ? strtoupper( $found[1] ) : '';

		case 'makhadane_yandex_metrica_id':
			return preg_match( '/([0-9]{5,12})/', $text, $found ) ? $found[1] : '';

		case 'makhadane_fb_pixel_id':
			return preg_match( '/([0-9]{8,25})/', $text, $found ) ? $found[1] : '';

		case 'makhadane_tiktok_pixel_id':
			return preg_match( '/^([A-Z0-9]{10,30})$/', strtoupper( $text ), $found ) ? $found[1] : '';

		default:
			return '';
	}
}

/**
 * Sanitasi nilai field tracking saat disimpan lewat ACF, sehingga yang tersimpan
 * di database hanya ID/kode yang valid (snippet mentah otomatis dibuang).
 *
 * @param mixed                $value   Nilai yang akan disimpan.
 * @param int|string           $post_id ID post / 'options'.
 * @param array<string, mixed> $field   Definisi field ACF.
 * @return mixed
 */
function makhadane_sanitize_tracking_value( $value, $post_id, $field ) {
	unset( $post_id );

	$name = isset( $field['name'] ) ? (string) $field['name'] : '';
	if ( ! is_string( $value ) || ! isset( makhadane_tracking_field_keys()[ $name ] ) ) {
		return $value;
	}

	return makhadane_clean_tracking_field( $name, $value );
}
add_filter( 'acf/update_value', 'makhadane_sanitize_tracking_value', 10, 3 );

/**
 * Ambil dan validasi seluruh pengaturan tracking dari ACF Options.
 *
 * @return array<string, string> Nama field => nilai bersih ('' bila kosong/tidak valid).
 */
function makhadane_get_tracking_settings() {
	static $settings = null;
	if ( is_array( $settings ) ) {
		return $settings;
	}

	$settings = array();
	foreach ( array_keys( makhadane_tracking_field_keys() ) as $name ) {
		$raw               = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : '';
		$settings[ $name ] = makhadane_clean_tracking_field( $name, is_string( $raw ) ? $raw : '' );
	}

	return $settings;
}

/**
 * Apakah request ini boleh memuat tag pelacakan (bukan admin, feed, robots, atau pratinjau Customizer).
 *
 * @return bool
 */
function makhadane_should_render_tracking() {
	return ! ( is_admin() || is_feed() || is_robots() || is_customize_preview() );
}

/**
 * Cetak meta verifikasi (Google, Yandex, Bing, Meta) serta skrip resmi GTM, GA4,
 * Yandex Metrica, Meta Pixel & TikTok Pixel di `<head>`.
 *
 * Hanya mencetak bagian yang ID-nya terisi.
 *
 * @return void
 */
function makhadane_render_tracking_head() {
	if ( ! makhadane_should_render_tracking() ) {
		return;
	}

	$t = makhadane_get_tracking_settings();

	$verifications = array(
		'makhadane_gsc_verification'    => 'google-site-verification',
		'makhadane_yandex_verification' => 'yandex-verification',
		'makhadane_bing_verification'   => 'msvalidate.01',
		'makhadane_fb_domain_verify'    => 'facebook-domain-verification',
	);
	foreach ( $verifications as $name => $meta_name ) {
		if ( '' !== $t[ $name ] ) {
			echo '<meta name="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $t[ $name ] ) . '" />' . "\n";
		}
	}

	if ( '' !== $t['makhadane_gtm_id'] ) {
		$gtm_js = esc_js( $t['makhadane_gtm_id'] );
		echo "<!-- Google Tag Manager -->\n";
		echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . $gtm_js . "');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID divalidasi regex & di-esc_js.
	}

	if ( '' !== $t['makhadane_ga4_id'] ) {
		$ga_url = 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $t['makhadane_ga4_id'] );
		$ga_js  = esc_js( $t['makhadane_ga4_id'] );
		echo "<!-- Google Analytics (gtag.js) -->\n";
		echo '<script async src="' . esc_url( $ga_url ) . '"></script>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- Snippet resmi pelacakan, URL di-esc_url.
		echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . $ga_js . "');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID divalidasi regex & di-esc_js.
	}

	if ( '' !== $t['makhadane_yandex_metrica_id'] ) {
		$ym_id = (int) $t['makhadane_yandex_metrica_id'];
		echo "<!-- Yandex.Metrika counter -->\n";
		echo "<script>(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');ym(" . $ym_id . ",'init',{clickmap:true,trackLinks:true,accurateTrackBounce:true});</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID dicasting ke integer.
	}

	if ( '' !== $t['makhadane_fb_pixel_id'] ) {
		$fb_js = esc_js( $t['makhadane_fb_pixel_id'] );
		$fb_tr = 'https://www.facebook.com/tr?id=' . rawurlencode( $t['makhadane_fb_pixel_id'] ) . '&ev=PageView&noscript=1';
		echo "<!-- Meta Pixel Code -->\n";
		echo "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','" . $fb_js . "');fbq('track','PageView');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID divalidasi regex & di-esc_js.
		echo '<noscript><img height="1" width="1" style="display:none" src="' . esc_url( $fb_tr ) . '" alt="" /></noscript>' . "\n";
	}

	if ( '' !== $t['makhadane_tiktok_pixel_id'] ) {
		$tt_js = esc_js( $t['makhadane_tiktok_pixel_id'] );
		echo "<!-- TikTok Pixel Code -->\n";
		echo "<script>!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};ttq.load('" . $tt_js . "');ttq.page();}(window,document,'ttq');</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID divalidasi regex & di-esc_js.
	}
}
add_action( 'wp_head', 'makhadane_render_tracking_head', 2 );

/**
 * Cetak `<noscript>` GTM & Yandex Metrica tepat setelah tag pembuka `<body>` (`wp_body_open`).
 *
 * @return void
 */
function makhadane_render_tracking_body_open() {
	if ( ! makhadane_should_render_tracking() ) {
		return;
	}

	$t = makhadane_get_tracking_settings();

	if ( '' !== $t['makhadane_gtm_id'] ) {
		$gtm_ns_url = 'https://www.googletagmanager.com/ns.html?id=' . rawurlencode( $t['makhadane_gtm_id'] );
		echo '<!-- Google Tag Manager (noscript) -->' . "\n";
		echo '<noscript><iframe src="' . esc_url( $gtm_ns_url ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
	}

	if ( '' !== $t['makhadane_yandex_metrica_id'] ) {
		$ym_img = 'https://mc.yandex.ru/watch/' . rawurlencode( $t['makhadane_yandex_metrica_id'] );
		echo '<noscript><div><img src="' . esc_url( $ym_img ) . '" style="position:absolute;left:-9999px;" alt="" /></div></noscript>' . "\n";
	}
}
add_action( 'wp_body_open', 'makhadane_render_tracking_body_open', 2 );

/**
 * Ekstrak ID dari script/meta lama (textarea) menjadi nilai field baru.
 *
 * @param array<string, string> $legacy Nilai lama: ane_ga_header, ane_ga_footer,
 *                                       ane_sc_header, ane_metapixel_header.
 * @return array<string, string> Nama field baru => nilai bersih (hanya yang berhasil diekstrak).
 */
function makhadane_extract_legacy_tracking( $legacy ) {
	$found = array();

	// Google Tag Manager (header atau noscript footer) & GA4 yang ikut ditempel.
	$google_text = ( $legacy['ane_ga_header'] ?? '' ) . "\n" . ( $legacy['ane_ga_footer'] ?? '' );
	if ( preg_match( '/GTM-[A-Z0-9]+/i', $google_text, $m ) ) {
		$found['makhadane_gtm_id'] = makhadane_clean_tracking_field( 'makhadane_gtm_id', $m[0] );
	}
	if ( preg_match( '/(?:gtag\/js\?id=|[\'"]config[\'"]\s*,\s*[\'"])((?:G|AW|DC)-[A-Z0-9\-]+)/i', $google_text, $m ) ) {
		$found['makhadane_ga4_id'] = makhadane_clean_tracking_field( 'makhadane_ga4_id', $m[1] );
	}

	// Meta verifikasi (Google/Bing/Yandex/Meta) dari tag <meta name=".." content="..">.
	$meta_text = (string) ( $legacy['ane_sc_header'] ?? '' );
	$meta_map  = array(
		'google-site-verification'     => 'makhadane_gsc_verification',
		'msvalidate.01'                => 'makhadane_bing_verification',
		'yandex-verification'          => 'makhadane_yandex_verification',
		'facebook-domain-verification' => 'makhadane_fb_domain_verify',
	);
	if ( preg_match_all( '/<meta\b[^>]*>/i', $meta_text, $tags ) ) {
		foreach ( $tags[0] as $tag ) {
			if ( preg_match( '/name\s*=\s*["\']([^"\']+)["\']/i', $tag, $n ) && preg_match( '/content\s*=\s*["\']([^"\']+)["\']/i', $tag, $c ) ) {
				$name = strtolower( $n[1] );
				if ( isset( $meta_map[ $name ] ) ) {
					$found[ $meta_map[ $name ] ] = makhadane_clean_tracking_field( $meta_map[ $name ], $c[1] );
				}
			}
		}
	} elseif ( '' !== trim( $meta_text ) ) {
		// Admin hanya menempel kode verifikasi Google saja.
		$found['makhadane_gsc_verification'] = makhadane_clean_tracking_field( 'makhadane_gsc_verification', $meta_text );
	}

	// Meta Pixel.
	$pixel_text = (string) ( $legacy['ane_metapixel_header'] ?? '' );
	if ( preg_match( '/fbq\(\s*[\'"]init[\'"]\s*,\s*[\'"]?([0-9]{8,25})/i', $pixel_text, $m ) || preg_match( '/facebook\.com\/tr\?id=([0-9]{8,25})/i', $pixel_text, $m ) ) {
		$found['makhadane_fb_pixel_id'] = makhadane_clean_tracking_field( 'makhadane_fb_pixel_id', $m[1] );
	}

	return array_filter( $found, 'strlen' );
}

/**
 * Migrasi sekali jalan: ambil ID dari isian script lama, simpan ke field baru,
 * lalu hapus isian lama dari database (ADR-0005).
 *
 * Hanya menulis ke database 1 kali; setelah selesai, flag `makhadane_tracking_migrated`
 * mencegah eksekusi ulang di request berikutnya.
 *
 * @return void
 */
function makhadane_migrate_legacy_tracking() {
	if ( '1' === get_option( 'makhadane_tracking_migrated' ) ) {
		return;
	}

	$legacy_names = array( 'ane_ga_header', 'ane_ga_footer', 'ane_sc_header', 'ane_metapixel_header', 'ane_metasdk_body' );
	$legacy       = array();
	foreach ( $legacy_names as $name ) {
		$value = get_option( 'options_' . $name, '' );
		if ( is_string( $value ) ) {
			$legacy[ $name ] = $value;
		}
	}

	$keys = makhadane_tracking_field_keys();
	foreach ( makhadane_extract_legacy_tracking( $legacy ) as $name => $value ) {
		// Jangan menimpa nilai baru yang sudah diisi admin.
		if ( '' !== (string) get_option( 'options_' . $name, '' ) ) {
			continue;
		}
		update_option( 'options_' . $name, $value );
		update_option( '_options_' . $name, $keys[ $name ] );
	}

	// Hapus isian script lama (hanya ID hasil ekstraksi yang dipertahankan).
	foreach ( $legacy_names as $name ) {
		delete_option( 'options_' . $name );
		delete_option( '_options_' . $name );
	}

	update_option( 'makhadane_tracking_migrated', '1' );
}
add_action( 'init', 'makhadane_migrate_legacy_tracking', 20 );
