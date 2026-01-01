<?php
/**
 * Image handling functions
 *
 * @package Makhadane
 * @since 4.1.1
 */

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

    if ( has_post_thumbnail() ) {
        if ( is_single() ) {
            $img_size = wp_is_mobile() ? 'thumbnail' : 'large';
        } else {
            $img_size = 'medium';
        }

        $thumb_img = wp_get_attachment_image_src( get_post_thumbnail_id(), $img_size );
        $img_url   = esc_url( $thumb_img[0] );
        $img_alt   = ane_get_image_title();

        if ( is_single() ) {
            printf(
                '<figure class="ane-image" itemscope itemprop="image" itemtype="https://schema.org/ImageObject"><img src="%s" alt="%s" itemprop="url contentUrl" /></figure>',
                $img_url,
                esc_attr( $img_alt )
            );
        } else {
            printf(
                '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
                esc_url( get_permalink() ),
                $img_url,
                esc_attr( $img_alt )
            );
        }
    } elseif ( $first_image ) {
        printf(
            '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
            esc_url( get_permalink() ),
            esc_url( $first_image ),
            esc_attr( get_the_title() )
        );
    } else {
        printf(
            '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
            esc_url( get_permalink() ),
            esc_url( ane_dummy_thumbnail() ),
            esc_attr( get_the_title() )
        );
    }
}
/**
 * Display featured image as overlay header background
 *
 * Outputs header with background image for overlay layouts.
 * Falls back to first content image, then dummy image.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_featured_image_overlay() {
    $first_image = ane_get_first_image();

    if ( has_post_thumbnail() ) {
        $img_size  = wp_is_mobile() ? 'thumbnail' : 'large';
        $thumb_img = wp_get_attachment_image_src( get_post_thumbnail_id(), $img_size );
        $img_url   = esc_url( $thumb_img[0] );
    } elseif ( $first_image ) {
        $img_url = esc_url( $first_image );
    } else {
        $img_url = esc_url( ane_dummy_thumbnail() );
    }

    printf(
        '<header class="entry-header" itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/NewsArticle" style="background-image:url(%s)">',
        $img_url
    );
}

/**
 * Display featured image in square format with schema markup
 *
 * Outputs square featured image optimized for Google Images.
 * Falls back to first content image, then dummy square image.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_featured_image_kotak() {
    $kotak_image_url = ane_get_first_image_kotak();

    if ( has_post_thumbnail() ) {
        $img_size  = wp_is_mobile() ? 'backend-kotak' : 'kotak';
        $thumb_img = wp_get_attachment_image_src( get_post_thumbnail_id(), $img_size );
        $img_url   = esc_url( $thumb_img[0] );
        $img_alt   = ane_get_image_title();

        printf(
            '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
            esc_url( get_permalink() ),
            $img_url,
            esc_attr( $img_alt )
        );
    } elseif ( $kotak_image_url ) {
        printf(
            '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
            esc_url( get_permalink() ),
            esc_url( $kotak_image_url ),
            esc_attr( get_the_title() )
        );
    } else {
        printf(
            '<a href="%s" itemprop="url"><figure class="ane-image"><img src="%s" alt="%s" itemprop="image" loading="lazy" /></figure></a>',
            esc_url( get_permalink() ),
            esc_url( ane_dummy_kotak() ),
            esc_attr( get_the_title() )
        );
    }
}