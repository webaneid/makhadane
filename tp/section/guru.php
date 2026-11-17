<?php
/**
 * Teachers Section Template Part
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

$makhadane_guru_query = new WP_Query(
	array(
		'post_type'      => 'ustadz',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	)
);
?>
<section class="ane-home-guru gp">
	<div class="ane-container">
		<div class="guru-header ane-text">
			<h2 class="section-title">
				<?php echo '' !== $makhadane_judul ? esc_html( $makhadane_judul ) : esc_html__( 'Dewan Guru', 'makhadane' ); ?>
			</h2>

			<?php if ( '' !== $makhadane_tagline ) : ?>
				<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_tagline ); ?></h3>
			<?php endif; ?>

			<?php if ( '' !== $makhadane_deskripsi ) : ?>
				<p class="section-description"><?php echo esc_html( $makhadane_deskripsi ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $makhadane_guru_query->have_posts() ) : ?>
			<div class="guru-hero-grid">
				<?php
				while ( $makhadane_guru_query->have_posts() ) :
					$makhadane_guru_query->the_post();
					$makhadane_gid    = get_the_ID();
					$makhadane_gname  = get_the_title();
					$makhadane_grole  = trim( wp_strip_all_tags( (string) get_field( 'ane_jabatan', $makhadane_gid ) ) );
					$makhadane_gthumb = has_post_thumbnail( $makhadane_gid ) ? wp_get_attachment_image_src( (int) get_post_thumbnail_id( $makhadane_gid ), 'persegi' ) : false;
					$makhadane_gphoto = ( is_array( $makhadane_gthumb ) && ! empty( $makhadane_gthumb[0] ) ) ? (string) $makhadane_gthumb[0] : ( function_exists( 'ane_dummy_kotak' ) ? ane_dummy_kotak() : '' );
					?>
					<a class="guru-hero-card" href="<?php the_permalink(); ?>">
						<?php if ( '' !== $makhadane_gphoto ) : ?>
							<img class="guru-hero-photo" src="<?php echo esc_url( $makhadane_gphoto ); ?>" width="400" height="500" alt="<?php echo esc_attr( $makhadane_gname ); ?>" loading="lazy" decoding="async">
						<?php endif; ?>
						<div class="guru-hero-info">
							<h4 class="guru-hero-name"><?php echo esc_html( $makhadane_gname ); ?></h4>
							<?php if ( '' !== $makhadane_grole ) : ?>
								<p class="guru-hero-role"><?php echo esc_html( $makhadane_grole ); ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<?php
		$makhadane_archive_url = get_post_type_archive_link( 'ustadz' );
		if ( ! empty( $makhadane_archive_url ) ) :
			?>
			<div class="guru-footer">
				<a href="<?php echo esc_url( $makhadane_archive_url ); ?>" class="btn btn-primary guru-all-btn">
					<span><?php esc_html_e( 'Lihat Semua Guru', 'makhadane' ); ?></span>
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
