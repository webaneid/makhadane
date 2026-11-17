<?php
/**
 * Call to Action Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_bg = trim( (string) get_sub_field( 'ane_background' ) );
if ( '' !== $makhadane_bg ) :
	$makhadane_design = ( 'design-2' === (string) get_sub_field( 'ane_cta_design' ) ) ? 'design-2' : 'design-1';
	$makhadane_kicker = trim( wp_strip_all_tags( (string) get_sub_field( 'custom_title' ) ) );
	$makhadane_title  = trim( (string) get_sub_field( 'ane_title' ) );
	$makhadane_desc   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );

	$makhadane_link  = get_sub_field( 'ane_link' );
	$makhadane_l_url = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) ) ? trim( (string) $makhadane_link['url'] ) : '';
	$makhadane_l_txt = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link['title'] ) ) : '';
	$makhadane_l_tgt = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['target'] ) ) ? trim( (string) $makhadane_link['target'] ) : '';

	$makhadane_link2  = get_sub_field( 'ane_link_secondary' );
	$makhadane_l2_url = ( is_array( $makhadane_link2 ) && ! empty( $makhadane_link2['url'] ) ) ? trim( (string) $makhadane_link2['url'] ) : '';
	$makhadane_l2_txt = ( is_array( $makhadane_link2 ) && ! empty( $makhadane_link2['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link2['title'] ) ) : '';
	$makhadane_l2_tgt = ( is_array( $makhadane_link2 ) && ! empty( $makhadane_link2['target'] ) ) ? trim( (string) $makhadane_link2['target'] ) : '';

	$makhadane_cta_src    = $makhadane_bg;
	$makhadane_cta_srcset = '';
	$makhadane_cta_w      = ( 'design-2' === $makhadane_design ) ? 800 : 1000;
	$makhadane_cta_h      = ( 'design-2' === $makhadane_design ) ? 1000 : 563;
	$makhadane_cta_alt    = '' !== $makhadane_title ? wp_strip_all_tags( $makhadane_title ) : (string) get_bloginfo( 'name' );

	$makhadane_cta_att_id = makhadane_get_attachment_id_from_url( $makhadane_bg );
	if ( $makhadane_cta_att_id > 0 ) {
		if ( 'design-2' === $makhadane_design ) {
			$makhadane_cta_size = wp_is_mobile() ? 'medium' : 'persegi';
		} else {
			$makhadane_cta_size = wp_is_mobile() ? 'medium' : 'large';
		}
		$makhadane_cta_data = wp_get_attachment_image_src( $makhadane_cta_att_id, $makhadane_cta_size );
		if ( is_array( $makhadane_cta_data ) && ! empty( $makhadane_cta_data[0] ) ) {
			$makhadane_cta_src = (string) $makhadane_cta_data[0];
			$makhadane_cta_w   = ! empty( $makhadane_cta_data[1] ) ? (int) $makhadane_cta_data[1] : $makhadane_cta_w;
			$makhadane_cta_h   = ! empty( $makhadane_cta_data[2] ) ? (int) $makhadane_cta_data[2] : $makhadane_cta_h;
		}
		if ( 'design-1' === $makhadane_design ) {
			$makhadane_cta_srcset = (string) wp_get_attachment_image_srcset( $makhadane_cta_att_id, $makhadane_cta_size );
		}
	}
	?>
	<?php if ( 'design-2' === $makhadane_design ) : ?>
		<section class="home-cta gp is-design-2">
			<div class="ane-container">
				<div class="cta-split-grid">
					<div class="cta-split-copy ane-text">
						<?php if ( '' !== $makhadane_kicker ) : ?>
							<h2 class="section-title"><?php echo esc_html( $makhadane_kicker ); ?></h2>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_title ) : ?>
							<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_title ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_desc ) : ?>
							<p class="section-description"><?php echo esc_html( $makhadane_desc ); ?></p>
						<?php endif; ?>

						<?php if ( ( '' !== $makhadane_l_url && '' !== $makhadane_l_txt ) || ( '' !== $makhadane_l2_url && '' !== $makhadane_l2_txt ) ) : ?>
							<div class="cta-split-actions">
								<?php if ( '' !== $makhadane_l_url && '' !== $makhadane_l_txt ) : ?>
									<a href="<?php echo esc_url( $makhadane_l_url ); ?>" class="btn btn-primary cta-btn"<?php echo '_blank' === $makhadane_l_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<span><?php echo esc_html( $makhadane_l_txt ); ?></span>
										<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
									</a>
								<?php endif; ?>

								<?php if ( '' !== $makhadane_l2_url && '' !== $makhadane_l2_txt ) : ?>
									<a href="<?php echo esc_url( $makhadane_l2_url ); ?>" class="cta-btn-secondary"<?php echo '_blank' === $makhadane_l2_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<?php echo esc_html( $makhadane_l2_txt ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="cta-split-media">
						<img src="<?php echo esc_url( $makhadane_cta_src ); ?>" width="<?php echo esc_attr( (string) $makhadane_cta_w ); ?>" height="<?php echo esc_attr( (string) $makhadane_cta_h ); ?>" alt="<?php echo esc_attr( $makhadane_cta_alt ); ?>" loading="lazy" decoding="async">
					</div>
				</div>
			</div>
		</section>
	<?php else : ?>
		<section class="home-cta gp">
			<img class="ane-cta-bg" src="<?php echo esc_url( $makhadane_cta_src ); ?>"<?php echo '' !== $makhadane_cta_srcset ? ' srcset="' . esc_attr( $makhadane_cta_srcset ) . '" sizes="(max-width: 767px) 400px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $makhadane_cta_w ); ?>" height="<?php echo esc_attr( (string) $makhadane_cta_h ); ?>" alt="<?php echo esc_attr( $makhadane_cta_alt ); ?>" loading="lazy" fetchpriority="low" decoding="async">
			<div class="ane-calltoaction">
				<div class="ane-container">
					<div class="cta-glass-card">
						<div class="cta-glass-grid">
							<div class="cta-copy ane-text">
								<?php if ( '' !== $makhadane_kicker ) : ?>
									<h2 class="section-title cta-kicker">
										<?php echo wp_kses_post( makhadane_icon( 'pengumuman' ) ); ?>
										<span><?php echo esc_html( $makhadane_kicker ); ?></span>
									</h2>
								<?php endif; ?>

								<?php if ( '' !== $makhadane_title ) : ?>
									<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_title ); ?></h3>
								<?php endif; ?>

								<?php if ( '' !== $makhadane_desc ) : ?>
									<p class="section-description"><?php echo esc_html( $makhadane_desc ); ?></p>
								<?php endif; ?>
							</div>

							<?php if ( ( '' !== $makhadane_l_url && '' !== $makhadane_l_txt ) || ( '' !== $makhadane_l2_url && '' !== $makhadane_l2_txt ) ) : ?>
								<div class="cta-action">
									<?php if ( '' !== $makhadane_l_url && '' !== $makhadane_l_txt ) : ?>
										<a href="<?php echo esc_url( $makhadane_l_url ); ?>" class="btn btn-primary cta-btn"<?php echo '_blank' === $makhadane_l_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
											<span><?php echo esc_html( $makhadane_l_txt ); ?></span>
											<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
										</a>
									<?php endif; ?>

									<?php if ( '' !== $makhadane_l2_url && '' !== $makhadane_l2_txt ) : ?>
										<a href="<?php echo esc_url( $makhadane_l2_url ); ?>" class="cta-btn-secondary is-glass"<?php echo '_blank' === $makhadane_l2_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
											<?php echo esc_html( $makhadane_l2_txt ); ?>
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="cta-curve-mask" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 76" preserveAspectRatio="none" focusable="false">
					<path d="M 0 76 L 0 70 L 580 70 C 642 70 662 6 720 6 C 778 6 798 70 860 70 L 1440 70 L 1440 76 Z"></path>
				</svg>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>
