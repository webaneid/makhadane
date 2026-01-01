<?php
/**
 * Big Slider Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bgcss = 'background-repeat: no-repeat; background-position: center; background-size: cover;';
?>
<section class="home-slider v2">
	<div class="home-big-slider owl-carousel">
		<?php if ( have_rows( 'home_slider' ) ) :
			while ( have_rows( 'home_slider' ) ) : the_row();

				$bg         = get_sub_field( 'ane_background' );
				$judul      = get_sub_field( 'ane_title' );
				$deskripsi  = get_sub_field( 'ane_deskripsi' );
				$link       = get_sub_field( 'ane_link' );
				$posisi     = get_sub_field( 'ane_posisi' );

				$bg         = ! empty( $bg ) ? esc_url( $bg ) : '';
				$judul      = ! empty( $judul ) ? wp_kses_post( $judul ) : '';
				$deskripsi  = ! empty( $deskripsi ) ? esc_html( $deskripsi ) : '';
				$posisi     = ! empty( $posisi ) ? sanitize_html_class( $posisi ) : '';
				$link_url   = ( ! empty( $link['url'] ) ) ? esc_url( $link['url'] ) : '';
				$link_title = ( ! empty( $link['title'] ) ) ? esc_html( $link['title'] ) : '';

		?>
			<div class="home-slider-item <?php echo $posisi . '-' . esc_attr( 'top' ); ?>"<?php echo ! empty( $bg ) ? ' style="background-image: url(' . $bg . '); ' . $bgcss . '"' : ''; ?>>
				<div class="overlay"></div>
				<div class="ane-container">
					<?php if ( ! empty( $judul ) ) : ?>
						<div class="text <?php echo $posisi; ?> mplr">
							<div class="text-content">
								<h2><?php echo $judul; ?></h2>
								<?php if ( ! empty( $deskripsi ) ) : ?>
									<p><?php echo $deskripsi; ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $link_url ) ) : ?>
									<a href="<?php echo $link_url; ?>" title="<?php echo esc_attr( $link_title ); ?>">
										<button class="btn btn-primary"><?php echo $link_title; ?></button>
									</a>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endwhile; endif; ?>
	</div>
</section>
