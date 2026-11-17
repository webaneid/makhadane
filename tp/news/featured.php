<?php
/**
 * Featured News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$makhadane_featured_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_key'            => 'ane_utama', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'          => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	)
);

if ( ! $makhadane_featured_posts->have_posts() ) {
	$makhadane_featured_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 5,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

$makhadane_recent_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="ane-news-featured ane-blog-section">
	<div class="news-featured-grid">
		<div class="news-featured-slider">
			<?php if ( $makhadane_featured_posts->have_posts() ) : ?>
				<div class="home-big-slider owl-carousel">
					<?php
					while ( $makhadane_featured_posts->have_posts() ) :
						$makhadane_featured_posts->the_post();
						get_template_part( 'tp/content', 'overlay' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="news-featured-latest">
			<div class="news-featured-latest-head">
				<span class="news-featured-live-dot" aria-hidden="true"></span>
				<h2><?php esc_html_e( 'Berita Terkini', 'makhadane' ); ?></h2>
			</div>

			<?php if ( $makhadane_recent_posts->have_posts() ) : ?>
				<ol class="news-featured-list">
					<?php
					$makhadane_idx = 0;
					while ( $makhadane_recent_posts->have_posts() ) :
						$makhadane_recent_posts->the_post();
						++$makhadane_idx;
						$makhadane_post_id    = get_the_ID();
						$makhadane_post_title = get_the_title();
						$makhadane_cats       = get_the_category( $makhadane_post_id );
						$makhadane_cat_name   = ! empty( $makhadane_cats ) ? $makhadane_cats[0]->name : '';
						$makhadane_thumb_url  = get_the_post_thumbnail_url( $makhadane_post_id, 'kotak' );
						if ( ! $makhadane_thumb_url && function_exists( 'ane_dummy_thumbnail' ) ) {
							$makhadane_thumb_url = ane_dummy_thumbnail();
						}
						?>
						<li>
							<a class="news-featured-item" href="<?php the_permalink(); ?>">
								<span class="news-featured-num"><?php echo esc_html( sprintf( '%02d', $makhadane_idx ) ); ?></span>
								<div class="news-featured-item-body">
									<?php if ( '' !== $makhadane_cat_name ) : ?>
										<span class="news-featured-cat"><?php echo esc_html( $makhadane_cat_name ); ?></span>
									<?php endif; ?>
									<h3 class="news-featured-item-title"><?php echo esc_html( $makhadane_post_title ); ?></h3>
									<time class="news-featured-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								</div>
								<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
									<div class="news-featured-thumb">
										<img src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" width="72" height="72" alt="<?php echo esc_attr( $makhadane_post_title ); ?>" loading="lazy" decoding="async">
									</div>
								<?php endif; ?>
							</a>
						</li>
					<?php endwhile; ?>
				</ol>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
