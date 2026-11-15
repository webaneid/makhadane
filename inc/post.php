<?php
/**
 * Post-related functions.
 *
 * @package makhadane
 * @since 4.1.1
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Bersihkan string judul dari tag HTML, artefak copy-paste Microsoft Word
 * (mso-*, <o:p>, <xml>, conditional comments), entitas &nbsp;, dan karakter zero-width.
 *
 * @param string $title Judul mentah.
 * @return string Judul bersih tanpa tag HTML/style Word.
 */
function makhadane_clean_title_string( $title ) {
	if ( ! is_string( $title ) || '' === $title ) {
		return '';
	}

	$clean = $title;

	// Hapus komentar kondisional MS Word & blok <style>/<script>/<xml> yang ikut ter-paste.
	$clean = (string) preg_replace( '/<!--\[if[^\]]*\]>.*?<!\[endif\]-->/is', '', $clean );
	$clean = (string) preg_replace( '/<!--.*?-->/s', '', $clean );
	$clean = (string) preg_replace( '#<\s*(style|script|xml|meta|link)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $clean );
	$clean = (string) preg_replace( '#<\s*(style|script|xml|meta|link|o:p)\b[^>]*/?\s*>#is', '', $clean );
	$clean = (string) preg_replace( '#<\s*/\s*o:p\s*>#is', '', $clean );

	// Hapus seluruh tag HTML (<span>, <font>, <b>, <i>, <p>, <br>, dll).
	$clean = wp_strip_all_tags( $clean, true );

	// Normalisasi non-breaking space (&nbsp; / UTF-8 \xC2\xA0) dan zero-width space dari Word.
	$clean = str_ireplace( array( '&nbsp;', '&#160;', '&#xa0;' ), ' ', $clean );
	$clean = str_replace( array( "\xC2\xA0", "\xE2\x80\x8B", "\xE2\x80\x8C", "\xE2\x80\x8D", "\xEF\xBB\xBF" ), ' ', $clean );

	// Rapikan spasi berlebih.
	$clean = (string) preg_replace( '/\s+/u', ' ', $clean );

	return trim( $clean );
}

/**
 * Sanitasi judul saat post/page/CPT disimpan ke database (mencegah HTML & sampah MS Word).
 *
 * @param string $title Judul yang akan disimpan.
 * @return string
 */
function makhadane_sanitize_title_on_save( $title ) {
	return makhadane_clean_title_string( (string) $title );
}
add_filter( 'title_save_pre', 'makhadane_sanitize_title_on_save', 10 );

/**
 * Sanitasi judul saat ditampilkan (membersihkan post lama di DB yang sudah terlanjur berisi HTML/Word).
 *
 * Dijalankan pada prioritas 5 sebelum `wptexturize` (prioritas 10).
 *
 * @param string $title Judul post.
 * @return string
 */
function makhadane_sanitize_title_on_display( $title ) {
	return makhadane_clean_title_string( (string) $title );
}
add_filter( 'the_title', 'makhadane_sanitize_title_on_display', 5 );

/**
 * Display post title with appropriate heading level.
 *
 * Outputs title with proper heading hierarchy based on context.
 * - H1: Single post main content only (the actual post being viewed)
 * - H2: Archive pages
 * - H3: Homepage, related posts, newest posts, widgets (secondary loops)
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_title() {
	$queried_object_id = get_queried_object_id();
	$current_post_id   = get_the_ID();
	$is_main_post      = is_single() && in_the_loop() && is_main_query() && ( $queried_object_id === $current_post_id );
	$permalink         = (string) get_permalink();

	if ( is_home() ) {
		printf(
			'<h3 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h3>',
			esc_url( $permalink ),
			esc_html( get_the_title() )
		);
	} elseif ( is_archive() ) {
		printf(
			'<h2 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h2>',
			esc_url( $permalink ),
			esc_html( get_the_title() )
		);
	} elseif ( $is_main_post ) {
		printf(
			'<h1 class="post-title" itemprop="headline">%s</h1>',
			esc_html( get_the_title() )
		);
	} else {
		printf(
			'<h3 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h3>',
			esc_url( $permalink ),
			esc_html( get_the_title() )
		);
	}
}

/**
 * Get time ago string for post.
 *
 * @since 4.1.1
 * @return string HTML output with time ago.
 */
function ane_load_times_ago() {
	$timestamp = (int) get_the_time( 'U' );
	$time_ago  = ane_time_ago( $timestamp );

	return sprintf(
		'<div class="meta-time"><i class="fa fa-clock-o" aria-hidden="true"></i> %s</div>',
		esc_html( $time_ago )
	);
}

/**
 * Display post categories.
 *
 * @since 4.1.1
 * @return string Categories HTML output.
 */
function ane_display_post_categories() {
	$categories = get_the_category();

	if ( empty( $categories ) ) {
		return '';
	}

	$categories_output = '';

	foreach ( $categories as $category ) {
		$categories_output .= sprintf(
			'<a class="post-cat" href="%s">%s</a>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_html( $category->name )
		);
	}

	return $categories_output;
}

/**
 * Display post meta information.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_meta_content() {
	$categories_output = ane_display_post_categories();
	$time_output       = ane_load_times_ago();

	$output = sprintf(
		'<div class="ane-meta"><ul><li>%s</li><li>%s</li></ul></div>',
		$categories_output,
		$time_output
	);

	echo wp_kses_post( $output );
}

/**
 * Calculate reading time for post.
 *
 * @since 4.1.1
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @return string Reading time string.
 */
function ane_reading_time( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$content = get_post_field( 'post_content', $post_id );

	if ( empty( $content ) ) {
		return sprintf(
			/* translators: %d: number of minutes */
			_n( '%d min', '%d mins', 1, 'makhadane' ),
			1
		);
	}

	$word_count   = str_word_count( wp_strip_all_tags( $content ) );
	$reading_time = (int) max( 1, ceil( $word_count / 200 ) );

	return sprintf(
		/* translators: %d: number of minutes */
		_n( '%d min', '%d mins', $reading_time, 'makhadane' ),
		$reading_time
	);
}

/**
 * Display single post meta with schema markup.
 *
 * @since 4.1.1
 * @return string Meta HTML output.
 */
function ane_single_meta() {
	global $post;

	if ( ! $post ) {
		return '';
	}

	$published_date = (string) get_the_date( 'c' );
	$modified_date  = (string) get_the_modified_date( 'c' );
	$formatted_date = esc_html( (string) get_the_date( 'l, j F Y' ) );
	$formatted_time = esc_html( (string) get_the_time( 'G:i' ) );
	$reading_time   = ane_reading_time( $post->ID );
	$views          = function_exists( 'ane_get_views' ) ? ane_get_views() : __( '0 views', 'makhadane' );
	$show_separator = ! wp_is_mobile();

	ob_start();
	?>
	<div class="ane-meta">
		<ul>
			<li itemprop="datePublished" content="<?php echo esc_attr( $published_date ); ?>">
				<?php
				printf(
					/* translators: 1: date, 2: time, 3: timezone */
					esc_html__( '%1$s - %2$s %3$s', 'makhadane' ),
					$formatted_date, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped with esc_html().
					$formatted_time, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped with esc_html().
					esc_html__( 'WIB', 'makhadane' )
				);
				?>
			</li>
			<li itemprop="timeRequired" content="PT<?php echo absint( str_replace( array( ' min', ' mins' ), '', $reading_time ) ); ?>M">
				<?php
				printf(
					/* translators: 1: separator, 2: reading time */
					esc_html__( '%1$sReading Time: %2$s', 'makhadane' ),
					$show_separator ? '- ' : '',
					esc_html( $reading_time )
				);
				?>
			</li>
			<?php if ( ! wp_is_mobile() ) : ?>
				<li itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter">
					<meta itemprop="interactionType" content="https://schema.org/ViewAction"/>
					<?php
					if ( $show_separator ) {
						echo esc_html__( '- ', 'makhadane' );
					}
					?>
					<span itemprop="userInteractionCount"><?php echo esc_html( $views ); ?></span>
				</li>
			<?php endif; ?>
		</ul>
		<meta itemprop="dateModified" content="<?php echo esc_attr( $modified_date ); ?>" />
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Hide sticky checkbox on post edit screen.
 *
 * @since 4.1.1
 * @return void
 */
function ane_hide_admin_sticky_checkbox() {
	?>
	<script>
		function hideAdminStickyCheckbox() {
			let labels = jQuery('.components-panel label.components-checkbox-control__label');

			labels.each(function(){
				let label = jQuery(this);

				if ( label.html() === 'Stick to the top of the blog') {
					label.closest('.components-panel__row').hide();
					return;
				}
			});
		}

		jQuery('body').on('DOMNodeInserted', '.edit-post-sidebar .edit-post-post-status', hideAdminStickyCheckbox);
	</script>
	<?php
}
add_action( 'admin_footer-post.php', 'ane_hide_admin_sticky_checkbox', 999, 0 );
add_action( 'admin_footer-post-new.php', 'ane_hide_admin_sticky_checkbox', 999, 0 );
