<?php
/**
 * Extracurricular Section Template Part (Landing Page Bento Checkerboard)
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_judul     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_subtitle  = trim( (string) get_sub_field( 'ane_subtitle' ) );
$makhadane_deskripsi = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );

$makhadane_ekstra_query = new WP_Query(
	array(
		'post_type'      => 'ekstrakurikuler',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	)
);
?>
<section class="ane-home-ekstrakurikuler gp">
	<div class="ane-container">
		<div class="ekstra-header">
			<h2 class="section-title">
				<?php echo '' !== $makhadane_judul ? esc_html( $makhadane_judul ) : esc_html__( 'Ekstrakurikuler', 'makhadane' ); ?>
			</h2>

			<?php if ( '' !== $makhadane_subtitle ) : ?>
				<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_subtitle ); ?></h3>
			<?php endif; ?>

			<?php if ( '' !== $makhadane_deskripsi ) : ?>
				<p class="section-description"><?php echo esc_html( $makhadane_deskripsi ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $makhadane_ekstra_query->have_posts() ) : ?>
			<div class="ekstra-bento-grid">
				<?php
				while ( $makhadane_ekstra_query->have_posts() ) :
					$makhadane_ekstra_query->the_post();
					$makhadane_post_id    = get_the_ID();
					$makhadane_post_title = get_the_title();
					$makhadane_raw_text   = has_excerpt() ? get_the_excerpt() : get_the_content();
					$makhadane_post_desc  = wp_trim_words( wp_strip_all_tags( $makhadane_raw_text ), 18, '...' );
					$makhadane_thumb_url  = get_the_post_thumbnail_url( $makhadane_post_id, 'kotak' );
					if ( ! $makhadane_thumb_url && function_exists( 'ane_dummy_thumbnail' ) ) {
						$makhadane_thumb_url = ane_dummy_thumbnail();
					}
					?>
					<a class="ekstra-bento-pair" href="<?php the_permalink(); ?>">
						<div class="ekstra-bento-media">
							<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
								<img src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" width="400" height="400" alt="<?php echo esc_attr( $makhadane_post_title ); ?>" loading="lazy" decoding="async">
							<?php endif; ?>
						</div>
						<div class="ekstra-bento-card">
							<h4 class="ekstra-bento-title"><?php echo esc_html( $makhadane_post_title ); ?></h4>
							<?php if ( '' !== $makhadane_post_desc ) : ?>
								<p class="ekstra-bento-desc"><?php echo esc_html( $makhadane_post_desc ); ?></p>
							<?php endif; ?>
						</div>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>

		<?php
		$makhadane_archive_url = get_post_type_archive_link( 'ekstrakurikuler' );
		if ( ! empty( $makhadane_archive_url ) ) :
			?>
			<div class="ekstra-footer">
				<a class="btn btn-primary ekstra-all-btn" href="<?php echo esc_url( $makhadane_archive_url ); ?>">
					<span><?php esc_html_e( 'Lihat Semua Ekstrakurikuler', 'makhadane' ); ?></span>
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
