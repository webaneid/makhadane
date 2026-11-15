<?php
/**
 * Image handling functions
 *
 * @package Makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Get image caption for featured image
 *
 * Returns formatted caption with schema markup.
 *
 * @since 4.1.1
 * @return string Caption HTML or empty string
 */
function ane_get_image_caption() {
	$caption = get_the_post_thumbnail_caption();

	if ( ! empty( $caption ) ) {
		return sprintf(
			'<figcaption class="wp-element-caption" itemprop="caption">%s</figcaption>',
			wp_kses_post( $caption )
		);
	}

	return '';
}

/**
 * Get image title/alt text for featured image
 *
 * Returns alt text, falls back to caption or post title.
 *
 * @since 4.1.1
 * @return string Image alt text
 */
function ane_get_image_title() {
	$image_id = get_post_thumbnail_id();
	$alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

	if ( ! empty( $alt_text ) ) {
		return wp_kses_post( $alt_text );
	}

	$caption = get_the_post_thumbnail_caption();

	return ! empty( $caption ) ? wp_kses_post( $caption ) : wp_kses_post( get_the_title() );
}

/**
 * Get dummy/placeholder thumbnail URL
 *
 * Returns default thumbnail image with customizer override option.
 *
 * @since 4.1.1
 * @return string Dummy image URL
 */
function ane_dummy_thumbnail() {
	$default_image = ane_get_asset_url( 'img/no-image.jpg' );
	$dummythumb    = get_theme_mod( 'dummythumb', $default_image );

	return esc_url( $dummythumb );
}

/**
 * Get dummy/placeholder square thumbnail URL
 *
 * Returns default square thumbnail image with customizer override option.
 *
 * @since 4.1.1
 * @return string Dummy square image URL
 */
function ane_dummy_kotak() {
	$default_image = ane_get_asset_url( 'img/no-image-kotak.jpg' );
	$dummykotak    = get_theme_mod( 'dummythumb', $default_image );

	return esc_url( $dummykotak );
}

/**
 * Get first image from post content
 *
 * Extracts first image URL from post content, returns appropriate size.
 * Falls back to original URL if not from Media Library.
 *
 * @since 4.1.1
 * @return string|false Image URL or false if no image found
 */
function ane_get_first_image() {
	$content = get_the_content();

	if ( empty( $content ) ) {
		return false;
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( $content );
	libxml_clear_errors();

	$images = $doc->getElementsByTagName( 'img' );

	if ( $images->length > 0 ) {
		$first_image_src = $images->item( 0 )->getAttribute( 'src' );
		$image_id        = attachment_url_to_postid( $first_image_src );
		$img_size        = is_single() ? 'large' : 'thumbnail';

		if ( $image_id ) {
			$img_data = wp_get_attachment_image_src( $image_id, $img_size );
			if ( $img_data ) {
				return $img_data[0];
			}
		} else {
			return esc_url( $first_image_src );
		}
	}

	return false;
}
/**
 * Get first image from post content in square format
 *
 * Extracts first image URL from post content, returns square size.
 * Falls back to original URL if not from Media Library.
 *
 * @since 4.1.1
 * @return string|false Image URL or false if no image found
 */
function ane_get_first_image_kotak() {
	$content = get_the_content();

	if ( empty( $content ) ) {
		return false;
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( $content );
	libxml_clear_errors();

	$images = $doc->getElementsByTagName( 'img' );

	if ( $images->length > 0 ) {
		$first_image_src = $images->item( 0 )->getAttribute( 'src' );
		$image_id        = attachment_url_to_postid( $first_image_src );
		$img_size        = 'kotak';

		if ( $image_id ) {
			$img_data = wp_get_attachment_image_src( $image_id, $img_size );
			if ( $img_data ) {
				return $img_data[0];
			}
		} else {
			return esc_url( $first_image_src );
		}
	}

	return false;
}

/**
 * Display featured image with schema markup
 *
 * Outputs featured image with Google Images optimized markup.
 * Falls back to first content image, then dummy image.
 * Responsive image sizes based on context.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_featured_image() {
	$first_image = ane_get_first_image();
	$img_alt     = ane_get_image_title();

	if ( has_post_thumbnail() ) {
		$thumb_id = (int) get_post_thumbnail_id();
		if ( is_single() ) {
			$img_size = wp_is_mobile() ? 'thumbnail' : 'large';
			$sizes    = '(max-width: 767px) 400px, 1000px';
		} else {
			$img_size = wp_is_mobile() ? 'thumbnail' : 'medium';
			$sizes    = '(max-width: 767px) 400px, 700px';
		}

		$thumb_img  = wp_get_attachment_image_src( $thumb_id, $img_size );
		$img_url    = ( is_array( $thumb_img ) && ! empty( $thumb_img[0] ) ) ? esc_url( (string) $thumb_img[0] ) : esc_url( ane_dummy_thumbnail() );
		$img_w      = ( is_array( $thumb_img ) && ! empty( $thumb_img[1] ) ) ? (int) $thumb_img[1] : 700;
		$img_h      = ( is_array( $thumb_img ) && ! empty( $thumb_img[2] ) ) ? (int) $thumb_img[2] : 394;
		$img_srcset = (string) wp_get_attachment_image_srcset( $thumb_id, $img_size );
		$srcset_str = '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="' . esc_attr( $sizes ) . '"' : '';

		if ( is_single() ) {
			printf(
				'<figure class="ane-image" itemscope itemprop="image" itemtype="https://schema.org/ImageObject"><img src="%1$s"%2$s width="%3$d" height="%4$d" alt="%5$s" itemprop="url contentUrl" fetchpriority="high" decoding="async" /></figure>',
				$img_url, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$srcset_str, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				absint( $img_w ),
				absint( $img_h ),
				esc_attr( $img_alt )
			);
		} else {
			printf(
				'<a href="%1$s" itemprop="url" aria-label="%6$s"><figure class="ane-image"><img src="%2$s"%3$s width="%4$d" height="%5$d" alt="%6$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
				esc_url( (string) get_permalink() ),
				$img_url, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$srcset_str, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				absint( $img_w ),
				absint( $img_h ),
				esc_attr( $img_alt )
			);
		}
	} elseif ( $first_image ) {
		printf(
			'<a href="%1$s" itemprop="url" aria-label="%3$s"><figure class="ane-image"><img src="%2$s" width="700" height="394" alt="%3$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
			esc_url( (string) get_permalink() ),
			esc_url( $first_image ),
			esc_attr( get_the_title() )
		);
	} else {
		printf(
			'<a href="%1$s" itemprop="url" aria-label="%3$s"><figure class="ane-image"><img src="%2$s" width="700" height="394" alt="%3$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
			esc_url( (string) get_permalink() ),
			esc_url( ane_dummy_thumbnail() ),
			esc_attr( get_the_title() )
		);
	}
}

/**
 * Display featured image as overlay header background (`<img class="ane-overlay-bg">`).
 *
 * Outputs header with responsive `<img>` instead of inline CSS `background-image`
 * per standard `webane-pagespeed-cwv`.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_featured_image_overlay() {
	$first_image = ane_get_first_image();
	$img_url     = '';
	$img_srcset  = '';
	$img_w       = 700;
	$img_h       = 394;

	if ( has_post_thumbnail() ) {
		$thumb_id  = (int) get_post_thumbnail_id();
		$img_size  = wp_is_mobile() ? 'thumbnail' : 'medium';
		$thumb_img = wp_get_attachment_image_src( $thumb_id, $img_size );
		if ( is_array( $thumb_img ) && ! empty( $thumb_img[0] ) ) {
			$img_url = (string) $thumb_img[0];
			$img_w   = ! empty( $thumb_img[1] ) ? (int) $thumb_img[1] : 700;
			$img_h   = ! empty( $thumb_img[2] ) ? (int) $thumb_img[2] : 394;
		}
		$img_srcset = (string) wp_get_attachment_image_srcset( $thumb_id, $img_size );
	} elseif ( $first_image ) {
		$img_url = (string) $first_image;
	} else {
		$img_url = ane_dummy_thumbnail();
	}

	$srcset_attr = '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 400px, 700px"' : '';
	$title_attr  = esc_attr( get_the_title() );

	printf(
		'<header class="entry-header" itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/NewsArticle"><img class="ane-overlay-bg" src="%1$s"%2$s width="%3$d" height="%4$d" alt="%5$s" loading="lazy" decoding="async" /><a href="%6$s" class="ane-overlay-permalink" aria-label="%5$s"></a>',
		esc_url( $img_url ),
		$srcset_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		absint( $img_w ),
		absint( $img_h ),
		$title_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_url( (string) get_permalink() )
	);
}

/**
 * Display featured image in list/thumbnail format with schema markup.
 *
 * Uses `'thumbnail'` (`400x225`) with DPR-aware `sizes="(max-width: 767px) 86px, 98px"`
 * per `webane-pagespeed-cwv`.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_featured_image_kotak() {
	$kotak_image_url = ane_get_first_image_kotak();

	if ( has_post_thumbnail() ) {
		$thumb_id   = (int) get_post_thumbnail_id();
		$thumb_img  = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
		$img_url    = ( is_array( $thumb_img ) && ! empty( $thumb_img[0] ) ) ? esc_url( (string) $thumb_img[0] ) : esc_url( ane_dummy_kotak() );
		$img_w      = ( is_array( $thumb_img ) && ! empty( $thumb_img[1] ) ) ? (int) $thumb_img[1] : 400;
		$img_h      = ( is_array( $thumb_img ) && ! empty( $thumb_img[2] ) ) ? (int) $thumb_img[2] : 225;
		$img_srcset = (string) wp_get_attachment_image_srcset( $thumb_id, 'thumbnail' );
		$srcset_str = '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 86px, 98px"' : '';
		$img_alt    = ane_get_image_title();

		printf(
			'<a href="%1$s" itemprop="url" aria-label="%6$s"><figure class="ane-image"><img src="%2$s"%3$s width="%4$d" height="%5$d" alt="%6$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
			esc_url( (string) get_permalink() ),
			$img_url, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$srcset_str, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			absint( $img_w ),
			absint( $img_h ),
			esc_attr( $img_alt )
		);
	} elseif ( $kotak_image_url ) {
		printf(
			'<a href="%1$s" itemprop="url" aria-label="%3$s"><figure class="ane-image"><img src="%2$s" width="400" height="225" alt="%3$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
			esc_url( (string) get_permalink() ),
			esc_url( $kotak_image_url ),
			esc_attr( get_the_title() )
		);
	} else {
		printf(
			'<a href="%1$s" itemprop="url" aria-label="%3$s"><figure class="ane-image"><img src="%2$s" width="400" height="225" alt="%3$s" itemprop="image" loading="lazy" decoding="async" /></figure></a>',
			esc_url( (string) get_permalink() ),
			esc_url( ane_dummy_kotak() ),
			esc_attr( get_the_title() )
		);
	}
}
