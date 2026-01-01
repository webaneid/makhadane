<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
	if ( ! isset( $_GET['ane_manifest'] ) ) {
		return;
	}

	header( 'Content-Type: application/manifest+json' );

	$theme_color = get_field( 'ane-warna-utama', 'option' );
	$theme_color = $theme_color ? $theme_color : '#1e3a8a';

	$icon_url = has_site_icon() ? get_site_icon_url( 192 ) : ane_get_asset_url( 'img/admin-webane.svg' );

	$manifest = array(
		'name'              => get_bloginfo( 'name' ),
		'short_name'        => get_bloginfo( 'name' ),
		'start_url'         => home_url( '/' ),
		'display'           => 'standalone',
		'background_color'  => '#ffffff',
		'theme_color'       => $theme_color,
		'icons'             => array(
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
		'scope'             => home_url( '/' ),
		'categories'        => array( 'education', 'school', 'news', 'blog' ),
		'description'       => get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'Situs resmi ' . get_bloginfo( 'name' ),
		'lang'              => get_bloginfo( 'language' ),
		'dir'               => 'ltr',
	);

	echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'template_redirect', 'ane_generate_manifest' );

/**
 * Enqueue theme styles and scripts
 * Load semua CSS dan JavaScript yang dibutuhkan theme
 */
function ane_load_css_and_js() {
	// CSS Files
	wp_enqueue_style(
		'ane-font-icon',
		ane_get_asset_url( 'css/FontAne.css' ),
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'ane-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,400;1,700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'magnific-popup','https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css' );
	wp_enqueue_style( 'OwlCarouse','https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css' );

	wp_enqueue_style(
		'ane-main-style',
		ane_get_asset_url( 'css/main.min.css' ),
		array(),
		'4.1.1'
	);

	// JavaScript Files - Load libraries first
	wp_enqueue_script(
		'owl-carousel',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
		array( 'jquery' ),
		'2.3.4',
		true
	);

	wp_enqueue_script(
		'magnific-popup',
		'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js',
		array( 'jquery' ),
		'1.1.0',
		true
	);

	wp_enqueue_script(
		'ane-main-script',
		ane_get_asset_url( 'js/makhadane.js' ),
		array( 'jquery', 'owl-carousel', 'magnific-popup' ),
		'4.1.1',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ane_load_css_and_js' );

/**
 * Set excerpt length
 *
 * @param int $length Default excerpt length
 * @return int Excerpt length
 */
function ane_excerpt_length( $length ) {
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

	$pattern = '/<a(.*?)href=([\'"])(.*?\.(bmp|gif|jpeg|jpg|png|webp|svg))([\'"])(.*?)>/i';
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

	// Extract video ID from iframe
	if ( preg_match( '/iframe/', $url ) ) {
		$id = preg_replace( '/[\s\S]*\/embed\/|"[\s\S]*|\?[\s\S]*/', '', $url );
	}
	// Extract from youtu.be or embed URL
	elseif ( preg_match( '/youtu\.be/', $url ) || preg_match( '/embed/', $url ) ) {
		$id = preg_replace( "/^((?![^\/]+$).)*/", '', $url );
	}
	// Extract from watch URL
	else {
		$id = preg_replace( "/^((?![^\?v]+$).)*=|&[^\&]*/", '', $url );
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
		'prev_text' => '<i class="ane ane-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Previous Page', 'makhadane' ) . '</span>',
		'next_text' => '<i class="ane ane-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">' . esc_html__( 'Next Page', 'makhadane' ) . '</span>',
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
	$query = new WP_Query(
		array(
			'posts_per_page'      => 5,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
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
					<span><i class="fa fa-chevron-left"></i> <?php esc_html_e( 'Prev Post', 'makhadane' ); ?></span>
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
					<span><?php esc_html_e( 'Next Post', 'makhadane' ); ?> <i class="fa fa-chevron-right"></i></span>
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
						<i class="ane-<?php echo esc_attr( $network ); ?>"></i>
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
            'icon'   => 'ane-whatsapp',
            'label'  => __( 'WhatsApp', 'makhadane' ),
        ),
        'sosmed_fb'      => array(
            'class'  => 'facebook',
            'prefix' => '',
            'icon'   => 'ane-facebook',
            'label'  => __( 'Facebook', 'makhadane' ),
        ),
        'sosmed_tw'      => array(
            'class'  => 'twitter',
            'prefix' => '',
            'icon'   => 'ane-twitter-x',
            'label'  => __( 'Twitter/X', 'makhadane' ),
        ),
        'sosmed_youtube' => array(
            'class'  => 'youtube',
            'prefix' => '',
            'icon'   => 'ane-youtube',
            'label'  => __( 'YouTube', 'makhadane' ),
        ),
        'sosmed_ig'      => array(
            'class'  => 'instagram',
            'prefix' => '',
            'icon'   => 'ane-instagram',
            'label'  => __( 'Instagram', 'makhadane' ),
        ),
        'sosmed_tiktok'  => array(
            'class'  => 'tiktok',
            'prefix' => '',
            'icon'   => 'ane-tiktok',
            'label'  => __( 'TikTok', 'makhadane' ),
        ),
    );

    echo '<div class="ane-sosmed"><ul>';

    foreach ( $sosmed_list as $key => $data ) {
        if ( ! empty( $sosmed[ $key ] ) ) {
            $url = esc_url( $data['prefix'] . $sosmed[ $key ] );
            printf(
                '<li><a class="%s" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s"></i></a></li>',
                esc_attr( $data['class'] ),
                $url,
                esc_attr( $data['label'] ),
                esc_attr( $data['icon'] )
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

    $judul   = ! empty( $about['company_name'] ) ? esc_html( $about['company_name'] ) : '';
    $sologan = ! empty( $about['company_sologan'] ) ? esc_html( $about['company_sologan'] ) : '';

    if ( $judul ) {
        echo '<h3>' . $judul . '</h3>';
    }

    if ( $sologan ) {
        echo '<h4>' . $sologan . '</h4>';
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

    $alamat  = ! empty( $alamat_parts ) ? esc_html( implode( ', ', $alamat_parts ) ) : '';
    $phone   = ! empty( $contact['kontak_telepon'] ) ? esc_html( $contact['kontak_telepon'] ) : '';
    $mobile  = ! empty( $contact['kontak_handphone'] ) ? esc_html( $contact['kontak_handphone'] ) : '';
    $email   = ! empty( $contact['kontak_email'] ) ? esc_html( $contact['kontak_email'] ) : '';
    $website = ! empty( $contact['kontak_website'] ) ? esc_url( $contact['kontak_website'] ) : '';

    echo '<div class="footer-kontak"><ul>';

    if ( $alamat ) {
        echo '<li><i class="ane-lokasi"></i> ' . $alamat . '</li>';
    }
    if ( $phone ) {
        echo '<li><i class="ane-telepon"></i> ' . $phone . '</li>';
    }
    if ( $mobile ) {
        echo '<li><i class="ane-handphone"></i> ' . $mobile . '</li>';
    }
    if ( $email ) {
        echo '<li><i class="ane-email"></i> ' . $email . '</li>';
    }
    if ( $website ) {
        printf(
            '<li><a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="ane-laptop"></i> %s</a></li>',
            $website,
            esc_attr__( 'Visit website', 'makhadane' ),
            $website
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

add_action( 'the_content', 'ane_author_info_box' );
remove_filter( 'pre_user_description', 'wp_filter_kses' );

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

    $position++;

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

            if ( $category->parent != 0 ) {
                $parent_cats = array();
                $parent_id   = $category->parent;

                while ( $parent_id ) {
                    $parent      = get_category( $parent_id );
                    $parent_cats[] = sprintf(
                        '<li itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><a itemprop="item" href="%s"><span itemprop="name">%s</span></a><meta itemprop="position" content="%d" /></li>',
                        esc_url( get_category_link( $parent->term_id ) ),
                        esc_html( $parent->name ),
                        $position
                    );
                    $parent_id = $parent->parent;
                    $position++;
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
                $position++;
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
                $position++;
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
                $position++;
            }

            $breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parents ) );
        }

        $breadcrumbs[] = sprintf(
            '<li class="current" itemscope itemtype="https://schema.org/ListItem" itemprop="itemListElement"><span itemprop="name">%s</span><meta itemprop="position" content="%d" /></li>',
            esc_html( get_the_title() ),
            $position
        );
    }

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
            __( 'Wakaf digital %s, kolaborasi dengan %s', 'makhadane' ),
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
    global $wpdb;

    $copyright_dates = $wpdb->get_results(
        "SELECT
            YEAR(MIN(post_date_gmt)) AS firstdate,
            YEAR(MAX(post_date_gmt)) AS lastdate
        FROM {$wpdb->posts}
        WHERE post_status = 'publish'"
    );

    if ( ! empty( $copyright_dates ) && isset( $copyright_dates[0]->firstdate ) ) {
        $first_year = $copyright_dates[0]->firstdate;
        $last_year  = $copyright_dates[0]->lastdate;

        $copyright = '&copy; ' . $first_year;

        if ( $first_year != $last_year ) {
            $copyright .= '-' . $last_year;
        }

        return $copyright;
    }

    return '&copy; ' . date( 'Y' );
}

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
 * Filter allowed mime types for uploads
 *
 * Adds support for modern image formats.
 *
 * @since 4.1.1
 * @param array $mimes Existing mime types
 * @return array Modified mime types
 */
function ane_allowed_mime_types( $mimes ) {
    return array(
        'jpg|jpeg' => 'image/jpeg',
        'png'      => 'image/png',
        'gif'      => 'image/gif',
        'webp'     => 'image/webp',
        'svg'      => 'image/svg+xml',
        'bmp'      => 'image/bmp',
    );
}
add_filter( 'upload_mimes', 'ane_allowed_mime_types' );

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

    $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
    $count_key  = 'musi_views';
    $view_key   = 'viewed_' . $post_id . '_' . md5( $ip_address );

    if ( false === get_transient( $view_key ) ) {
        $count = (int) get_post_meta( $post_id, $count_key, true );
        $count++;
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
    if ( ! empty( $_GET['postviews_id'] ) ) {
        $post_id = absint( $_GET['postviews_id'] );
        if ( $post_id > 0 ) {
            ane_set_views( $post_id );
        }
    }
    wp_die();
}
add_action( 'wp_ajax_postviews', 'ane_increment_views_ajax' );
add_action( 'wp_ajax_nopriv_postviews', 'ane_increment_views_ajax' );

/**
 * Enqueue post views tracking script
 *
 * Loads AJAX script for view counting on single posts.
 *
 * @since 4.1.1
 * @return void
 */
function ane_enqueue_postviews_script() {
    global $post;

    if ( is_single() && isset( $post->ID ) ) {
        wp_enqueue_script(
            'ane-postviews',
            ane_get_asset_url( 'js/postviews-cache.js' ),
            array( 'jquery' ),
            '4.1.1',
            true
        );

        wp_localize_script(
            'ane-postviews',
            'postViewsCache',
            array(
                'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
                'post_id'        => $post->ID,
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_postviews_script' );

// Tambahkan kolom di admin post dan custom post type
add_filter('manage_posts_columns', 'musi_add_views_column');
add_filter('manage_edit-post_sortable_columns', 'musi_make_views_column_sortable');
add_action('manage_posts_custom_column', 'musi_show_views_column', 10, 2);

function musi_add_views_column($columns) {
    $columns['musi_post_views'] = __('Views', 'makhadane');
    return $columns;
}

function musi_show_views_column($column_name, $post_id) {
    if ($column_name === 'musi_post_views') {
        echo get_post_meta($post_id, 'musi_views', true) ?: '0';
    }
}

// Jadikan kolom views sortable
function musi_make_views_column_sortable($columns) {
    $columns['musi_post_views'] = 'musi_views';
    return $columns;
}

// Custom sorting untuk kolom views
add_action('pre_get_posts', 'musi_sort_views_column');
function musi_sort_views_column($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($orderby = $query->get('orderby')) {
        if ($orderby === 'musi_views') {
            $query->set('meta_key', 'musi_views');
            $query->set('orderby', 'meta_value_num');
        }
    }
}
