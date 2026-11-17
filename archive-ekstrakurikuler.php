<?php
/**
 * Archive Template: Ekstrakurikuler (Masonry Gallery)
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Get background image from ACF options.
$makhadane_bgimg_data = get_field( 'ane_image_arsip_ekstra', 'option' );
$makhadane_bgimg_url  = '';

if ( is_array( $makhadane_bgimg_data ) && ! empty( $makhadane_bgimg_data['sizes']['large'] ) ) {
	$makhadane_bgimg_url = $makhadane_bgimg_data['sizes']['large'];
} elseif ( has_post_thumbnail() ) {
	$makhadane_bgimg_url = (string) get_the_post_thumbnail_url( null, 'large' );
}
?>

<main id="site-content" class="ane-arsip ane-arsip-ekstrakurikuler">
	<?php if ( '' !== $makhadane_bgimg_url ) : ?>
		<header class="archive-header" style="background-image: url('<?php echo esc_url( $makhadane_bgimg_url ); ?>')">
			<div class="ane-container archive-header-inner">
				<?php post_type_archive_title( '<h1 class="general-title">', '</h1>' ); ?>
				<p class="archive-header-sub"><?php esc_html_e( 'Beragam kegiatan minat, bakat, dan pengembangan karakter santri.', 'makhadane' ); ?></p>
			</div>
		</header>
	<?php else : ?>
		<header class="archive-header">
			<div class="ane-container archive-header-inner">
				<?php post_type_archive_title( '<h1 class="general-title">', '</h1>' ); ?>
				<p class="archive-header-sub"><?php esc_html_e( 'Beragam kegiatan minat, bakat, dan pengembangan karakter santri.', 'makhadane' ); ?></p>
			</div>
		</header>
	<?php endif; ?>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="ane-container ekskul-archive-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="ekskul-masonry-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$makhadane_post_id    = get_the_ID();
					$makhadane_post_title = get_the_title();
					$makhadane_raw_text   = has_excerpt() ? get_the_excerpt() : get_the_content();
					$makhadane_post_desc  = wp_trim_words( wp_strip_all_tags( $makhadane_raw_text ), 20, '...' );
					$makhadane_thumb_url  = get_the_post_thumbnail_url( $makhadane_post_id, 'large' );
					if ( ! $makhadane_thumb_url && function_exists( 'ane_dummy_thumbnail' ) ) {
						$makhadane_thumb_url = ane_dummy_thumbnail();
					}
					?>
					<article id="post-<?php echo esc_attr( (string) $makhadane_post_id ); ?>" <?php post_class( 'ekskul-masonry-item' ); ?>>
						<a class="ekskul-masonry-card" href="<?php the_permalink(); ?>">
							<div class="ekskul-masonry-media">
								<?php if ( ! empty( $makhadane_thumb_url ) ) : ?>
									<img src="<?php echo esc_url( (string) $makhadane_thumb_url ); ?>" alt="<?php echo esc_attr( $makhadane_post_title ); ?>" loading="lazy" decoding="async">
								<?php endif; ?>
								<span class="ekskul-masonry-arrow" aria-hidden="true">
									<?php
									if ( function_exists( 'makhadane_icon' ) ) {
										echo wp_kses_post( makhadane_icon( 'arrow-up-right' ) );
									}
									?>
								</span>
							</div>
							<div class="ekskul-masonry-body">
								<h2 class="ekskul-masonry-title"><?php echo esc_html( $makhadane_post_title ); ?></h2>
								<?php if ( '' !== $makhadane_post_desc ) : ?>
									<p class="ekskul-masonry-desc"><?php echo esc_html( $makhadane_post_desc ); ?></p>
								<?php endif; ?>
								<span class="ekskul-masonry-more">
									<span><?php esc_html_e( 'Lihat Kegiatan', 'makhadane' ); ?></span>
									<?php
									if ( function_exists( 'makhadane_icon' ) ) {
										echo wp_kses_post( makhadane_icon( 'chevron-right' ) );
									}
									?>
								</span>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			if ( function_exists( 'ane_post_pagination' ) ) {
				ane_post_pagination();
			}
			?>
		<?php else : ?>
			<?php get_template_part( 'tp/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
