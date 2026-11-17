<?php
/**
 * Image Below Text Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_bg_class = sanitize_html_class( (string) ( get_sub_field( 'ane_background' ) ?? 'terang' ) );
if ( '' === $makhadane_bg_class ) {
	$makhadane_bg_class = 'terang';
}
$makhadane_title      = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_subtitle   = trim( (string) get_sub_field( 'ane_subtitle' ) );
$makhadane_desc       = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_image      = get_sub_field( 'ane_image' );
$makhadane_link       = get_sub_field( 'ane_link' );
$makhadane_link_url   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) ) ? trim( (string) $makhadane_link['url'] ) : '';
$makhadane_link_title = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link['title'] ) ) : '';
$makhadane_link_tgt   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['target'] ) ) ? trim( (string) $makhadane_link['target'] ) : '';

$makhadane_features = array();
if ( have_rows( 'ane_repeater' ) ) {
	while ( have_rows( 'ane_repeater' ) ) {
		the_row();
		$ft_title = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
		$ft_desc  = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
		$ft_icon  = trim( (string) get_sub_field( 'ane_icon' ) );
		if ( '' !== $ft_title || '' !== $ft_desc || '' !== $ft_icon ) {
			$makhadane_features[] = array(
				'title' => $ft_title,
				'desc'  => $ft_desc,
				'icon'  => $ft_icon,
			);
		}
	}
}
?>
<section class="image-below-text gp <?php echo esc_attr( $makhadane_bg_class ); ?>">
	<div class="ane-container">
		<?php if ( '' !== $makhadane_title || '' !== $makhadane_subtitle || '' !== $makhadane_desc ) : ?>
			<div class="ibt-header ane-text">
				<?php if ( '' !== $makhadane_title ) : ?>
					<h2 class="section-title"><?php echo esc_html( $makhadane_title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_subtitle ) : ?>
					<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_subtitle ); ?></h3>
				<?php endif; ?>

				<?php if ( '' !== $makhadane_desc ) : ?>
					<p class="section-description"><?php echo esc_html( $makhadane_desc ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( is_array( $makhadane_image ) && ( ! empty( $makhadane_image['sizes']['large'] ) || ! empty( $makhadane_image['url'] ) ) ) :
			$makhadane_thumb     = (string) ( $makhadane_image['sizes']['large'] ?? $makhadane_image['url'] );
			$makhadane_alt       = ! empty( $makhadane_image['alt'] ) ? (string) $makhadane_image['alt'] : get_bloginfo( 'name' );
			$makhadane_has_float = ( '' !== $makhadane_link_url && '' !== $makhadane_link_title );
			?>
			<div class="ibt-showcase<?php echo $makhadane_has_float ? ' has-float' : ''; ?>">
				<img src="<?php echo esc_url( $makhadane_thumb ); ?>" width="1260" height="600" alt="<?php echo esc_attr( $makhadane_alt ); ?>" loading="lazy" decoding="async">

				<?php if ( $makhadane_has_float ) : ?>
					<div class="ibt-floating-card">
						<?php if ( '' !== $makhadane_title ) : ?>
							<span class="ibt-float-label"><?php echo esc_html( $makhadane_title ); ?></span>
						<?php endif; ?>
						<a href="<?php echo esc_url( $makhadane_link_url ); ?>" class="ibt-float-btn"<?php echo '_blank' === $makhadane_link_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<span><?php echo esc_html( $makhadane_link_title ); ?></span>
							<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $makhadane_features ) ) : ?>
			<div class="ibt-features-grid">
				<?php foreach ( $makhadane_features as $makhadane_feat ) : ?>
					<article class="ibt-feature-card">
						<?php if ( '' !== $makhadane_feat['icon'] ) : ?>
							<div class="ibt-feature-icon" aria-hidden="true">
								<img src="<?php echo esc_url( $makhadane_feat['icon'] ); ?>" width="24" height="24" alt="" loading="lazy" decoding="async">
							</div>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_feat['title'] ) : ?>
							<h4 class="ibt-feature-title"><?php echo esc_html( $makhadane_feat['title'] ); ?></h4>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_feat['desc'] ) : ?>
							<p class="ibt-feature-desc"><?php echo esc_html( $makhadane_feat['desc'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
