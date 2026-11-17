<?php
/**
 * Default News Layout Template Part (Lead Story + Square List)
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$makhadane_homekategori  = get_sub_field( 'pilih_kategori' );
$makhadane_judul         = trim( (string) get_sub_field( 'custom_title' ) );
$makhadane_category_id   = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->term_id ) ) ? (int) $makhadane_homekategori->term_id : 0;
$makhadane_category_name = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->name ) ) ? (string) $makhadane_homekategori->name : '';
$makhadane_category_link = $makhadane_category_id ? (string) get_category_link( $makhadane_category_id ) : '#';
$makhadane_heading       = '' !== $makhadane_judul ? $makhadane_judul : $makhadane_category_name;

$makhadane_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'cat'                 => $makhadane_category_id,
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="ane-news-default ane-blog-section">
	<?php if ( '' !== $makhadane_heading ) : ?>
		<div class="blog-section-head">
			<div class="blog-section-title-wrap">
				<span class="blog-section-marker" aria-hidden="true"></span>
				<h2>
					<a href="<?php echo esc_url( $makhadane_category_link ); ?>">
						<?php echo esc_html( $makhadane_heading ); ?>
					</a>
				</h2>
			</div>
			<span class="blog-section-line" aria-hidden="true"></span>
			<a class="blog-section-more" href="<?php echo esc_url( $makhadane_category_link ); ?>">
				<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
				<span class="blog-section-more-icon" aria-hidden="true"><?php echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) ); ?></span>
			</a>
		</div>
	<?php endif; ?>

	<?php if ( $makhadane_query->have_posts() ) : ?>
		<div class="news-default-grid">
			<?php
			$makhadane_counter = 0;
			while ( $makhadane_query->have_posts() ) :
				$makhadane_query->the_post();
				++$makhadane_counter;

				if ( 1 === $makhadane_counter ) :
					$makhadane_lead_id    = get_the_ID();
					$makhadane_lead_title = get_the_title();
					$makhadane_raw_text   = has_excerpt() ? get_the_excerpt() : get_the_content();
					$makhadane_lead_desc  = wp_trim_words( wp_strip_all_tags( $makhadane_raw_text ), 22, '...' );
					$makhadane_lead_cats  = get_the_category( $makhadane_lead_id );
					$makhadane_lead_cat   = ! empty( $makhadane_lead_cats ) ? $makhadane_lead_cats[0]->name : '';
					$makhadane_lead_img   = get_the_post_thumbnail_url( $makhadane_lead_id, 'medium_large' );
					if ( ! $makhadane_lead_img && function_exists( 'ane_dummy_thumbnail' ) ) {
						$makhadane_lead_img = ane_dummy_thumbnail();
					}
					?>
					<article id="post-<?php echo esc_attr( (string) $makhadane_lead_id ); ?>" <?php post_class( 'news-default-lead' ); ?>>
						<a class="news-default-lead-card" href="<?php the_permalink(); ?>">
							<div class="news-default-lead-media">
								<?php if ( ! empty( $makhadane_lead_img ) ) : ?>
									<img src="<?php echo esc_url( (string) $makhadane_lead_img ); ?>" alt="<?php echo esc_attr( $makhadane_lead_title ); ?>" loading="lazy" decoding="async">
								<?php endif; ?>
							</div>
							<div class="news-default-lead-body">
								<div class="news-default-lead-meta">
									<?php if ( '' !== $makhadane_lead_cat ) : ?>
										<span class="news-default-lead-cat"><?php echo esc_html( $makhadane_lead_cat ); ?></span>
									<?php endif; ?>
									<time class="news-default-lead-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								</div>
								<h3 class="news-default-lead-title"><?php echo esc_html( $makhadane_lead_title ); ?></h3>
								<?php if ( '' !== $makhadane_lead_desc ) : ?>
									<p class="news-default-lead-desc"><?php echo esc_html( $makhadane_lead_desc ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</article>
					<div class="news-default-list">
				<?php else : ?>
					<?php get_template_part( 'tp/content', 'list' ); ?>
					<?php
				endif;
			endwhile;
			if ( $makhadane_counter >= 1 ) :
				?>
				</div>
				<?php
			endif;
			wp_reset_postdata();
			?>
		</div>
	<?php endif; ?>
</section>
