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
	$makhadane_kicker = trim( wp_strip_all_tags( (string) get_sub_field( 'custom_title' ) ) );
	$makhadane_title  = trim( (string) get_sub_field( 'ane_title' ) );
	$makhadane_desc   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
	$makhadane_link   = get_sub_field( 'ane_link' );
	$makhadane_l_url  = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) ) ? trim( (string) $makhadane_link['url'] ) : '';
	$makhadane_l_txt  = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link['title'] ) ) : '';
	$makhadane_l_tgt  = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['target'] ) ) ? trim( (string) $makhadane_link['target'] ) : '';

	$makhadane_cta_src    = $makhadane_bg;
	$makhadane_cta_srcset = '';
	$makhadane_cta_w      = 1000;
	$makhadane_cta_h      = 563;
	$makhadane_cta_alt    = '' !== $makhadane_title ? wp_strip_all_tags( $makhadane_title ) : (string) get_bloginfo( 'name' );

	$makhadane_cta_att_id = makhadane_get_attachment_id_from_url( $makhadane_bg );
	if ( $makhadane_cta_att_id > 0 ) {
		$makhadane_cta_size = wp_is_mobile() ? 'medium' : 'large';
		$makhadane_cta_data = wp_get_attachment_image_src( $makhadane_cta_att_id, $makhadane_cta_size );
		if ( is_array( $makhadane_cta_data ) && ! empty( $makhadane_cta_data[0] ) ) {
			$makhadane_cta_src = (string) $makhadane_cta_data[0];
			$makhadane_cta_w   = ! empty( $makhadane_cta_data[1] ) ? (int) $makhadane_cta_data[1] : 1000;
			$makhadane_cta_h   = ! empty( $makhadane_cta_data[2] ) ? (int) $makhadane_cta_data[2] : 563;
		}
		$makhadane_cta_srcset = (string) wp_get_attachment_image_srcset( $makhadane_cta_att_id, $makhadane_cta_size );
	}
	?>
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

					<?php if ( '' !== $makhadane_l_url && '' !== $makhadane_l_txt ) : ?>
						<div class="cta-action">
							<a href="<?php echo esc_url( $makhadane_l_url ); ?>" class="btn btn-alternatif cta-btn"<?php echo '_blank' === $makhadane_l_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
								<span><?php echo esc_html( $makhadane_l_txt ); ?></span>
								<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
