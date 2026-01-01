<?php
/**
 * Post-related functions
 *
 * @package Makhadane
 * @since 4.1.1
 */

/**
 * Display post title with appropriate heading level
 *
 * Outputs title with proper heading hierarchy based on context.
 * - H1: Single post main content only (the actual post being viewed)
 * - H2: Archive pages
 * - H3: Homepage, related posts, newest posts, widgets (secondary loops)
 *
 * IMPORTANT: Detects secondary loops by comparing current post ID with main queried object.
 * Even when called inside single.php's main loop, related/newest posts will use H3.
 *
 * @since 4.1.1
 * @return void
 */
function ane_get_title() {
	global $wp_query;

	// Check if current post is THE main post being viewed (not a related/newest post)
	$queried_object_id = get_queried_object_id();
	$current_post_id   = get_the_ID();
	$is_main_post      = is_single() && in_the_loop() && ( $queried_object_id === $current_post_id );

	if ( is_home() ) {
		// Homepage: use H3
		printf(
			'<h3 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h3>',
			esc_url( get_permalink() ),
			esc_html( get_the_title() )
		);
	} elseif ( is_archive() ) {
		// Archive pages: use H2
		printf(
			'<h2 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h2>',
			esc_url( get_permalink() ),
			esc_html( get_the_title() )
		);
	} elseif ( $is_main_post ) {
		// Single post MAIN content ONLY: use H1 (without link)
		// This will ONLY match the actual post being viewed, not related/newest posts
		printf(
			'<h1 class="post-title" itemprop="headline">%s</h1>',
			esc_html( get_the_title() )
		);
	} else {
		// Everything else (related posts, newest posts, widgets, etc.): use H3 with link
		printf(
			'<h3 class="post-title" itemprop="headline"><a href="%s" rel="bookmark">%s</a></h3>',
			esc_url( get_permalink() ),
			esc_html( get_the_title() )
		);
	}
}

/**
 * Get time ago string for post
 *
 * Returns formatted time ago string using timestamp.
 *
 * @since 4.1.1
 * @return string HTML output with time ago
 */
function ane_load_times_ago() {
	$timestamp = get_the_time( 'U' );
	$time_ago  = ane_time_ago( $timestamp );

	return sprintf(
		'<div class="meta-time"><i class="fa fa-clock-o" aria-hidden="true"></i> %s</div>',
		esc_html( $time_ago )
	);
}

/**
 * Display post categories
 *
 * Returns HTML list of post categories with links.
 *
 * @since 4.1.1
 * @return string Categories HTML output
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
			esc_html( $category->cat_name )
		);
	}

	return $categories_output;
}

/**
 * Display post meta information
 *
 * Outputs post metadata including categories and time.
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
 * Calculate reading time for post
 *
 * Estimates reading time based on word count (200 words per minute).
 *
 * @since 4.1.1
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @return string Reading time string
 */
function ane_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );

	if ( empty( $content ) ) {
		return sprintf(
			_n( '%d min', '%d mins', 1, 'makhadane' ),
			1
		);
	}

	$word_count   = str_word_count( wp_strip_all_tags( $content ) );
	$reading_time = max( 1, ceil( $word_count / 200 ) );

	return sprintf(
		_n( '%d min', '%d mins', $reading_time, 'makhadane' ),
		$reading_time
	);
}

/**
 * Display single post meta with schema markup
 *
 * Outputs comprehensive metadata for single posts including:
 * - Published date with schema
 * - Reading time with schema
 * - View count with schema
 * - Modified date (hidden in meta tag)
 *
 * @since 4.1.1
 * @return string Meta HTML output
 */
function ane_single_meta() {
	global $post;

	if ( ! $post ) {
		return '';
	}

	$published_date = get_the_date( 'c' );
	$modified_date  = get_the_modified_date( 'c' );
	$formatted_date = esc_html( get_the_date( 'l, j F Y' ) );
	$formatted_time = esc_html( get_the_time( 'G:i' ) );
	$reading_time   = ane_reading_time( $post->ID );
	$views          = function_exists( 'ane_get_views' ) ? ane_get_views() : __( '0 views', 'makhadane' );

	ob_start();
	?>
	<div class="ane-meta">
		<ul>
			<li itemprop="datePublished" content="<?php echo esc_attr( $published_date ); ?>">
				<?php
				printf(
					/* translators: 1: date, 2: time, 3: timezone */
					esc_html__( '%1$s - %2$s %3$s', 'makhadane' ),
					$formatted_date,
					$formatted_time,
					esc_html__( 'WIB', 'makhadane' )
				);
				?>
			</li>
			<li itemprop="timeRequired" content="PT<?php echo absint( str_replace( array( ' min', ' mins' ), '', $reading_time ) ); ?>M">
				<?php
				printf(
					/* translators: %s: reading time */
					esc_html__( '- Reading Time: %s', 'makhadane' ),
					esc_html( $reading_time )
				);
				?>
			</li>
			<li itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter">
				<meta itemprop="interactionType" content="https://schema.org/ViewAction"/>
				<?php
				echo esc_html__( '- ', 'makhadane' );
				?><span itemprop="userInteractionCount"><?php echo esc_html( $views ); ?></span>
			</li>
		</ul>
		<meta itemprop="dateModified" content="<?php echo esc_attr( $modified_date ); ?>" />
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Hide sticky checkbox on post edit screen
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

				if ( label.html() == 'Stick to the top of the blog') {
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

/**
 * Remove all sticky posts
 *
 * @since 4.1.1
 * @return void
 */
function ane_remove_all_sticky_posts() {
	update_option( 'sticky_posts', array() );
}
add_action( 'init', 'ane_remove_all_sticky_posts' );
