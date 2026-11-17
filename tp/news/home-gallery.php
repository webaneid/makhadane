<?php
/**
 * Home Gallery Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_kategori = get_sub_field( 'pilih_kategori' );
if ( ! $makhadane_kategori || ! isset( $makhadane_kategori->term_id, $makhadane_kategori->name ) ) {
	return;
}

$makhadane_cat_id    = (int) $makhadane_kategori->term_id;
$makhadane_cat_name  = (string) $makhadane_kategori->name;
$makhadane_cat_link  = get_category_link( $makhadane_cat_id );
$makhadane_judul     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_tagline   = trim( (string) get_sub_field( 'ane_subtitle' ) );
$makhadane_deskripsi = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );

$makhadane_gal_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'cat'                 => $makhadane_cat_id,
		'posts_per_page'      => 8,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="ane-home-gallery gp">
	<div class="ane-container">
		<div class="news-section-header">
			<div class="news-header-copy ane-text">
				<h2 class="section-title">
					<?php echo '' !== $makhadane_judul ? esc_html( $makhadane_judul ) : esc_html( $makhadane_cat_name ); ?>
				</h2>

				<?php if ( '' !== $makhadane_tagline ) : ?>
					<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_tagline ); ?></h3>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_deskripsi ) : ?>
					<p class="section-description"><?php echo esc_html( $makhadane_deskripsi ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! is_wp_error( $makhadane_cat_link ) && ! empty( $makhadane_cat_link ) ) : ?>
				<a class="news-all-link" href="<?php echo esc_url( (string) $makhadane_cat_link ); ?>" aria-label="<?php echo esc_attr( __( 'View All Posts in ', 'makhadane' ) . $makhadane_cat_name ); ?>">
					<span><?php esc_html_e( 'Lihat Semua', 'makhadane' ); ?></span>
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $makhadane_gal_query->have_posts() ) : ?>
			<div class="owl-carousel dot-style2" id="home-sliding">
				<?php
				while ( $makhadane_gal_query->have_posts() ) :
					$makhadane_gal_query->the_post();
					$makhadane_pid       = get_the_ID();
					$makhadane_ptitle    = get_the_title();
					$makhadane_pdate     = get_the_date();
					$makhadane_thumb_url = get_the_post_thumbnail_url( $makhadane_pid, 'medium_large' );
					if ( ! $makhadane_thumb_url && function_exists( 'ane_dummy_thumbnail' ) ) {
						$makhadane_thumb_url = ane_dummy_thumbnail();
					}
					?>
					<a class="home-news-card" href="<?php the_permalink(); ?>">
						<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
							<div class="home-news-card-media">
								<img src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" width="480" height="300" alt="<?php echo esc_attr( $makhadane_ptitle ); ?>" loading="lazy" decoding="async">
							</div>
						<?php endif; ?>
						<div class="home-news-card-body">
							<div class="home-news-meta">
								<span class="home-news-cat"><?php echo esc_html( $makhadane_cat_name ); ?></span>
								<span class="home-news-date"><?php echo esc_html( $makhadane_pdate ); ?></span>
							</div>
							<h4 class="home-news-card-title"><?php echo esc_html( $makhadane_ptitle ); ?></h4>
						</div>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
