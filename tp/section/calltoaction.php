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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.WP.I18n.MissingTranslatorsComment

$image = trim( (string) get_sub_field( 'ane_background' ) );
if ( '' !== $image ) :
	$custom_title = get_sub_field( 'custom_title' );
	$title        = get_sub_field( 'ane_title' );
	$description  = get_sub_field( 'ane_deskripsi' );
	$link         = get_sub_field( 'ane_link' );

	$cta_src    = $image;
	$cta_srcset = '';
	$cta_w      = 1000;
	$cta_h      = 563;
	$cta_alt    = ! empty( $title ) ? wp_strip_all_tags( (string) $title ) : (string) get_bloginfo( 'name' );

	$cta_att_id = makhadane_get_attachment_id_from_url( $image );
	if ( $cta_att_id > 0 ) {
		$cta_size = wp_is_mobile() ? 'thumbnail' : 'large';
		$cta_data = wp_get_attachment_image_src( $cta_att_id, $cta_size );
		if ( is_array( $cta_data ) && ! empty( $cta_data[0] ) ) {
			$cta_src = (string) $cta_data[0];
			$cta_w   = ! empty( $cta_data[1] ) ? (int) $cta_data[1] : 1000;
			$cta_h   = ! empty( $cta_data[2] ) ? (int) $cta_data[2] : 563;
		}
		$cta_srcset = (string) wp_get_attachment_image_srcset( $cta_att_id, $cta_size );
	}
	?>
<section class="home-cta">
	<img class="ane-cta-bg" src="<?php echo esc_url( $cta_src ); ?>"<?php echo '' !== $cta_srcset ? ' srcset="' . esc_attr( $cta_srcset ) . '" sizes="(max-width: 767px) 400px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $cta_w ); ?>" height="<?php echo esc_attr( (string) $cta_h ); ?>" alt="<?php echo esc_attr( $cta_alt ); ?>" loading="lazy" fetchpriority="low" decoding="async">
	<div class="ane-calltoaction">
		<div class="ane-container">
			<div class="isi-cta">
				<div class="judul-cta">
					<?php if ( ! empty( $custom_title ) ) : ?>
						<?php echo wp_kses_post( makhadane_icon( 'pengumuman' ) ); ?>
						<h2><?php echo esc_html( $custom_title ); ?></h2>
					<?php endif; ?>
				</div>

				<div class="ane-text text">
					<?php if ( ! empty( $title ) ) : ?>
						<h3><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>

					<?php if ( ! empty( $description ) ) : ?>
						<p><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $link ) && is_array( $link ) && ! empty( $link['url'] ) ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="btn btn-alternatif"><?php echo esc_html( $link['title'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
