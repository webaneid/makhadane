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

$image = get_sub_field( 'ane_background' );
if ( ! empty( $image ) ) :
	$image_url    = esc_url( $image );
	$custom_title = get_sub_field( 'custom_title' );
	$title        = get_sub_field( 'ane_title' );
	$description  = get_sub_field( 'ane_deskripsi' );
	$link         = get_sub_field( 'ane_link' );
?>
<section class="home-cta" style="background-image: url('<?php echo $image_url; ?>')">
	<div class="ane-calltoaction">
		<div class="ane-container">
			<div class="isi-cta">
				<div class="judul-cta">
					<?php if ( ! empty( $custom_title ) ) : ?>
						<i class="ane-pengumuman"></i>
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

					<?php if ( ! empty( $link ) ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="btn btn-alternatif"><?php echo esc_html( $link['title'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
