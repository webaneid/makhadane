<?php
/**
 * Home News Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_judul     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_tagline   = trim( (string) get_sub_field( 'ane_subtitle' ) );
$makhadane_deskripsi = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_posts_url = get_permalink( (int) get_option( 'page_for_posts' ) );
if ( ! $makhadane_posts_url ) {
	$makhadane_posts_url = get_post_type_archive_link( 'post' );
}

$makhadane_news_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="ane-home-news gp">
	<div class="ane-container">
		<div class="news-section-header">
			<div class="news-header-copy ane-text">
				<h2 class="section-title">
					<?php echo '' !== $makhadane_judul ? esc_html( $makhadane_judul ) : esc_html__( 'Berita Terbaru', 'makhadane' ); ?>
				</h2>

				<?php if ( '' !== $makhadane_tagline ) : ?>
					<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_tagline ); ?></h3>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_deskripsi ) : ?>
					<p class="section-description"><?php echo esc_html( $makhadane_deskripsi ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $makhadane_posts_url ) ) : ?>
				<a class="news-all-link" href="<?php echo esc_url( (string) $makhadane_posts_url ); ?>">
					<span><?php esc_html_e( 'Lihat Semua Berita', 'makhadane' ); ?></span>
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $makhadane_news_query->have_posts() ) : ?>
			<div class="home-news-grid">
				<?php
				$makhadane_news_idx = 0;
				while ( $makhadane_news_query->have_posts() ) :
					$makhadane_news_query->the_post();
					$makhadane_pid       = get_the_ID();
					$makhadane_ptitle    = get_the_title();
					$makhadane_pdate     = get_the_date();
					$makhadane_cats      = get_the_category();
					$makhadane_cat_name  = ! empty( $makhadane_cats ) ? $makhadane_cats[0]->name : '';
					$makhadane_thumb_sz  = ( 0 === $makhadane_news_idx ) ? 'large' : 'medium_large';
					$makhadane_thumb_url = get_the_post_thumbnail_url( $makhadane_pid, $makhadane_thumb_sz );
					if ( ! $makhadane_thumb_url && function_exists( 'ane_dummy_thumbnail' ) ) {
						$makhadane_thumb_url = ane_dummy_thumbnail();
					}

					if ( 0 === $makhadane_news_idx ) :
						$makhadane_excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 20, '...' );
						?>
						<a class="home-news-featured" href="<?php the_permalink(); ?>">
							<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
								<img class="home-news-featured-bg" src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" width="800" height="600" alt="<?php echo esc_attr( $makhadane_ptitle ); ?>" loading="lazy" decoding="async">
							<?php endif; ?>
							<div class="home-news-featured-body">
								<div class="home-news-meta">
									<?php if ( '' !== $makhadane_cat_name ) : ?>
										<span class="home-news-cat"><?php echo esc_html( $makhadane_cat_name ); ?></span>
									<?php endif; ?>
									<span class="home-news-date"><?php echo esc_html( $makhadane_pdate ); ?></span>
								</div>
								<h4 class="home-news-featured-title"><?php echo esc_html( $makhadane_ptitle ); ?></h4>
								<?php if ( '' !== $makhadane_excerpt ) : ?>
									<p class="home-news-featured-excerpt"><?php echo esc_html( $makhadane_excerpt ); ?></p>
								<?php endif; ?>
							</div>
						</a>
						<div class="home-news-side-grid">
						<?php
					else :
						?>
						<a class="home-news-card" href="<?php the_permalink(); ?>">
							<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
								<div class="home-news-card-media">
									<img src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" width="480" height="300" alt="<?php echo esc_attr( $makhadane_ptitle ); ?>" loading="lazy" decoding="async">
								</div>
							<?php endif; ?>
							<div class="home-news-card-body">
								<div class="home-news-meta">
									<?php if ( '' !== $makhadane_cat_name ) : ?>
										<span class="home-news-cat"><?php echo esc_html( $makhadane_cat_name ); ?></span>
									<?php endif; ?>
									<span class="home-news-date"><?php echo esc_html( $makhadane_pdate ); ?></span>
								</div>
								<h4 class="home-news-card-title"><?php echo esc_html( $makhadane_ptitle ); ?></h4>
							</div>
						</a>
						<?php
					endif;
					++$makhadane_news_idx;
				endwhile;
				if ( $makhadane_news_idx > 0 ) :
					?>
					</div>
					<?php
				endif;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
