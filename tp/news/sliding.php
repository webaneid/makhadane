<?php
/**
 * Sliding / Showcase News Layout Template Part
 *
 * Mendukung 2 pilihan desain dari sub-field ACF `desain_sliding`:
 * - `simple` (default): Carousel (`#home-sliding.owl-carousel`) dengan `tp/content-overlay`.
 * - `modern`: Editorial Cinema & Frosted Glass Showcase (1 Panoramic Hero + 4 Glass Cards).
 *
 * Mendukung 2 pilihan background dari sub-field ACF `warna_background`:
 * - `utama` (default): Gradasi warna utama (`--ane-warna-utama` ke `--ane-warna-gelap`).
 * - `gelap`: Warna gelap editorial (`--ane-warna-gelap`).
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited

$makhadane_homekategori  = get_sub_field( 'pilih_kategori' );
$makhadane_judul         = trim( (string) get_sub_field( 'custom_title' ) );
$makhadane_desain_raw    = (string) get_sub_field( 'desain_sliding' );
$makhadane_warna_bg_raw  = (string) get_sub_field( 'warna_background' );
$makhadane_category_id   = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->term_id ) ) ? (int) $makhadane_homekategori->term_id : 0;
$makhadane_category_name = ( is_object( $makhadane_homekategori ) && isset( $makhadane_homekategori->name ) ) ? (string) $makhadane_homekategori->name : '';
$makhadane_category_link = $makhadane_category_id ? (string) get_category_link( $makhadane_category_id ) : '#';
$makhadane_heading       = '' !== $makhadane_judul ? $makhadane_judul : $makhadane_category_name;

$makhadane_is_modern    = ( 'modern' === $makhadane_desain_raw );
$makhadane_section_cls  = 'ane-news-sliding ane-blog-section';
$makhadane_section_cls .= $makhadane_is_modern ? ' news-sliding-modern' : ' news-sliding-simple';
if ( $makhadane_is_modern ) {
	$makhadane_section_cls .= ( 'gelap' === $makhadane_warna_bg_raw ) ? ' news-sliding-gelap' : ' news-sliding-utama';
}
?>
<section class="<?php echo esc_attr( $makhadane_section_cls ); ?>">
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

	<?php if ( ! $makhadane_is_modern ) : ?>
		<?php
		if ( $makhadane_category_id ) :
			$makhadane_query = new WP_Query(
				array(
					'post_type'           => 'post',
					'cat'                 => $makhadane_category_id,
					'posts_per_page'      => 8,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( $makhadane_query->have_posts() ) :
				?>
				<div class="owl-carousel dot-style2" id="home-sliding">
					<?php
					while ( $makhadane_query->have_posts() ) :
						$makhadane_query->the_post();
						get_template_part( 'tp/content', 'overlay' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<?php
			endif;
		endif;
		?>
	<?php else : ?>
		<?php
		if ( $makhadane_category_id ) :
			$makhadane_modern_query = new WP_Query(
				array(
					'post_type'           => 'post',
					'cat'                 => $makhadane_category_id,
					'posts_per_page'      => 5,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);

			if ( $makhadane_modern_query->have_posts() ) :
				$makhadane_modern_posts = $makhadane_modern_query->posts;
				$makhadane_total_posts  = count( $makhadane_modern_posts );
				?>
				<div class="sliding-modern-showcase">
					<?php
					if ( isset( $makhadane_modern_posts[0] ) && $makhadane_modern_posts[0] instanceof WP_Post ) :
						$post = $makhadane_modern_posts[0];
						setup_postdata( $post );
						?>
						<div class="sliding-modern-hero">
							<?php get_template_part( 'tp/content', 'klasik' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $makhadane_total_posts > 1 ) : ?>
						<div class="sliding-modern-deck">
							<?php
							for ( $makhadane_i = 1; $makhadane_i < $makhadane_total_posts; $makhadane_i++ ) :
								if ( ! ( $makhadane_modern_posts[ $makhadane_i ] instanceof WP_Post ) ) {
									continue;
								}
								$post = $makhadane_modern_posts[ $makhadane_i ];
								setup_postdata( $post );
								?>
								<div class="sliding-glass-card">
									<?php get_template_part( 'tp/content', 'klasik' ); ?>
								</div>
							<?php endfor; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
				wp_reset_postdata();
			endif;
		endif;
		?>
	<?php endif; ?>
</section>
