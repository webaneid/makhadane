<?php
/**
 * Testimonial Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_judul   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_tagline = trim( (string) get_sub_field( 'ane_deskripsi' ) );
$makhadane_sub     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_testimoni_sub' ) ) );
$makhadane_bg_raw  = (string) get_sub_field( 'ane_testimoni_bg' );
$makhadane_bg_cls  = ( 'white' === $makhadane_bg_raw ) ? 'is-white' : 'is-terang';

$makhadane_testimoni_query = new WP_Query(
	array(
		'post_type'      => 'testimoni',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'no_found_rows'  => true,
	)
);
?>
<section class="ane-home-testimoni gp <?php echo esc_attr( $makhadane_bg_cls ); ?>">
	<div class="ane-container">
		<div class="testimoni-header">
			<h2 class="section-title">
				<?php echo '' !== $makhadane_judul ? esc_html( $makhadane_judul ) : esc_html__( 'Testimoni', 'makhadane' ); ?>
			</h2>
		</div>

		<div class="testimoni-layout">
			<div class="testimoni-intro">
				<?php if ( '' !== $makhadane_tagline ) : ?>
					<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_tagline ); ?></h3>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_sub ) : ?>
					<p class="section-description"><?php echo esc_html( $makhadane_sub ); ?></p>
				<?php endif; ?>

				<?php
				$makhadane_archive_url = get_post_type_archive_link( 'testimoni' );
				if ( ! empty( $makhadane_archive_url ) ) :
					?>
					<a href="<?php echo esc_url( $makhadane_archive_url ); ?>" class="testimoni-all-link">
						<span><?php esc_html_e( 'Lihat Semua Testimoni', 'makhadane' ); ?></span>
						<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $makhadane_testimoni_query->have_posts() ) : ?>
				<div class="testimoni-stage">
					<div class="testimoni-slider owl-carousel">
						<?php
						while ( $makhadane_testimoni_query->have_posts() ) :
							$makhadane_testimoni_query->the_post();
							$makhadane_tid    = get_the_ID();
							$makhadane_quote  = trim( wp_strip_all_tags( (string) get_field( 'ane_testimoni', $makhadane_tid ) ) );
							$makhadane_role   = trim( wp_strip_all_tags( (string) get_field( 'ane_jabatan', $makhadane_tid ) ) );
							$makhadane_author = get_the_title();
							$makhadane_thumb  = has_post_thumbnail( $makhadane_tid ) ? wp_get_attachment_image_src( (int) get_post_thumbnail_id( $makhadane_tid ), 'kotak' ) : false;
							$makhadane_avatar = ( is_array( $makhadane_thumb ) && ! empty( $makhadane_thumb[0] ) ) ? (string) $makhadane_thumb[0] : ( function_exists( 'ane_dummy_kotak' ) ? ane_dummy_kotak() : '' );
							?>
							<article class="testimoni-hero-card">
								<div class="testimoni-quote-wrap">
									<span class="testimoni-quote-mark is-open" aria-hidden="true">&ldquo;</span>
									<?php if ( '' !== $makhadane_quote ) : ?>
										<p class="testimoni-quote"><?php echo esc_html( $makhadane_quote ); ?></p>
									<?php endif; ?>
									<span class="testimoni-quote-mark is-close" aria-hidden="true">&rdquo;</span>
								</div>

								<div class="testimoni-author">
									<?php if ( '' !== $makhadane_avatar ) : ?>
										<div class="testimoni-author-avatar">
											<img src="<?php echo esc_url( $makhadane_avatar ); ?>" width="54" height="54" alt="<?php echo esc_attr( $makhadane_author ); ?>" loading="lazy" decoding="async">
										</div>
									<?php endif; ?>
									<div class="testimoni-author-meta">
										<h4 class="testimoni-author-name"><?php echo esc_html( $makhadane_author ); ?></h4>
										<?php if ( '' !== $makhadane_role ) : ?>
											<p class="testimoni-author-role"><?php echo esc_html( $makhadane_role ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</div>
				<?php
			endif;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
