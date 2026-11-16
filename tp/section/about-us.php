<?php
/**
 * About Us Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_title    = trim( (string) get_sub_field( 'ane_title' ) );
$makhadane_subtitle = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_subtitle' ) ) );
$makhadane_desc     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_link     = get_sub_field( 'ane_link' );
$makhadane_gallery  = get_sub_field( 'ane_about_gallery' );

$makhadane_slides = array();
if ( is_array( $makhadane_gallery ) ) {
	foreach ( $makhadane_gallery as $makhadane_row ) {
		if ( ! is_array( $makhadane_row ) ) {
			continue;
		}
		$img     = $makhadane_row['ane_gallery_image'] ?? null;
		$img_url = ( is_array( $img ) && ! empty( $img['sizes']['medium_large'] ) )
			? (string) $img['sizes']['medium_large']
			: ( ( is_array( $img ) && ! empty( $img['url'] ) ) ? (string) $img['url'] : '' );
		$img_alt = ( is_array( $img ) && ! empty( $img['alt'] ) ) ? (string) $img['alt'] : '';

		if ( '' === $img_url ) {
			continue;
		}

		$g_title = trim( wp_strip_all_tags( (string) ( $makhadane_row['ane_gallery_title'] ?? '' ) ) );
		$g_desc  = trim( wp_strip_all_tags( (string) ( $makhadane_row['ane_gallery_desc'] ?? '' ) ) );
		$g_link  = $makhadane_row['ane_gallery_link'] ?? null;
		$g_url   = ( is_array( $g_link ) && ! empty( $g_link['url'] ) ) ? trim( (string) $g_link['url'] ) : '';
		$g_tgt   = ( is_array( $g_link ) && ! empty( $g_link['target'] ) ) ? trim( (string) $g_link['target'] ) : '';

		$makhadane_slides[] = array(
			'url'    => $img_url,
			'alt'    => '' !== $img_alt ? $img_alt : ( '' !== $g_title ? $g_title : get_bloginfo( 'name' ) ),
			'title'  => $g_title,
			'desc'   => $g_desc,
			'link'   => $g_url,
			'target' => $g_tgt,
		);
	}
}

if ( empty( $makhadane_slides ) ) {
	$makhadane_fallback_img = get_sub_field( 'ane_image' );
	if ( is_array( $makhadane_fallback_img ) && ! empty( $makhadane_fallback_img['sizes']['large'] ) ) {
		$makhadane_slides[] = array(
			'url'    => (string) $makhadane_fallback_img['sizes']['large'],
			'alt'    => ! empty( $makhadane_fallback_img['alt'] ) ? (string) $makhadane_fallback_img['alt'] : get_bloginfo( 'name' ),
			'title'  => '',
			'desc'   => '',
			'link'   => '',
			'target' => '',
		);
	}
}
?>
<section class="about-us gp">
	<div class="ane-container">
		<div class="about-us-header">
			<?php if ( '' !== $makhadane_subtitle ) : ?>
				<div class="about-us-badge-group">
					<span class="about-us-badge"><?php echo esc_html( $makhadane_subtitle ); ?></span>
					<span class="about-us-badge-icon" aria-hidden="true">
						<?php echo wp_kses_post( makhadane_icon( 'star' ) ); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $makhadane_title ) : ?>
				<h2 class="about-us-title"><?php echo wp_kses_post( $makhadane_title ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== $makhadane_desc ) : ?>
				<p class="about-us-desc"><?php echo esc_html( $makhadane_desc ); ?></p>
			<?php endif; ?>

			<?php if ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) && ! empty( $makhadane_link['title'] ) ) : ?>
				<div class="about-us-cta">
					<a href="<?php echo esc_url( (string) $makhadane_link['url'] ); ?>" class="btn btn-primary"<?php echo ( ! empty( $makhadane_link['target'] ) && '_blank' === $makhadane_link['target'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( (string) $makhadane_link['title'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( ! empty( $makhadane_slides ) ) : ?>
		<div class="about-us-gallery-viewport">
			<div class="about-us-gallery-track">
				<?php foreach ( $makhadane_slides as $makhadane_slide ) : ?>
					<?php $makhadane_has_info = ( '' !== $makhadane_slide['title'] || '' !== $makhadane_slide['desc'] || '' !== $makhadane_slide['link'] ); ?>
					<article class="about-us-gallery-card<?php echo $makhadane_has_info ? ' has-info' : ''; ?>">
						<div class="about-us-gallery-media">
							<img src="<?php echo esc_url( $makhadane_slide['url'] ); ?>" width="320" height="400" alt="<?php echo esc_attr( $makhadane_slide['alt'] ); ?>" loading="lazy" decoding="async">
						</div>

						<?php if ( $makhadane_has_info ) : ?>
							<div class="about-us-gallery-info">
								<div class="about-us-gallery-copy">
									<?php if ( '' !== $makhadane_slide['title'] ) : ?>
										<h3 class="about-us-gallery-card-title"><?php echo esc_html( $makhadane_slide['title'] ); ?></h3>
									<?php endif; ?>
									<?php if ( '' !== $makhadane_slide['desc'] ) : ?>
										<p class="about-us-gallery-card-desc"><?php echo esc_html( $makhadane_slide['desc'] ); ?></p>
									<?php endif; ?>
								</div>
								<?php if ( '' !== $makhadane_slide['link'] ) : ?>
									<a class="about-us-gallery-arrow" href="<?php echo esc_url( $makhadane_slide['link'] ); ?>"<?php echo '_blank' === $makhadane_slide['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> aria-label="<?php echo esc_attr( '' !== $makhadane_slide['title'] ? $makhadane_slide['title'] : __( 'Lihat detail', 'makhadane' ) ); ?>">
										<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="ane-container">
			<div class="about-us-gallery-nav">
				<button type="button" class="about-us-nav-btn is-prev" aria-label="<?php esc_attr_e( 'Geser ke kiri', 'makhadane' ); ?>">
					<?php echo wp_kses_post( makhadane_icon( 'chevron-left' ) ); ?>
				</button>
				<div class="about-us-progress-track" aria-hidden="true">
					<div class="about-us-progress-bar"></div>
				</div>
				<button type="button" class="about-us-nav-btn is-next" aria-label="<?php esc_attr_e( 'Geser ke kanan', 'makhadane' ); ?>">
					<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>
</section>
