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
?>
<section class="about-us gp">
	<div class="ane-container">
		<div class="ane-col-46 align-items-center">
			<div class="ane-kiri">
				<?php
				$image = get_sub_field( 'ane_image' );
				if ( ! empty( $image ) && is_array( $image ) ) :
					$thumb = isset( $image['sizes']['large'] ) ? esc_url( $image['sizes']['large'] ) : '';
					$alt   = isset( $image['alt'] ) ? esc_attr( $image['alt'] ) : '';

					if ( $thumb ) : ?>
						<div class="ane-image">
							<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" />
						</div>
					<?php endif;
				endif;
				?>

				<div class="ane-funfact">
					<?php if ( have_rows( 'ane_repeater' ) ) : ?>
						<ul>
							<?php while ( have_rows( 'ane_repeater' ) ) : the_row();
								$angka = get_sub_field( 'ane_angka' );
								$title = get_sub_field( 'ane_title' );
								?>
								<li>
									<?php if ( ! empty( $angka ) ) : ?>
										<h5><?php echo esc_html( $angka ); ?></h5>
									<?php endif; ?>
									<?php if ( ! empty( $title ) ) : ?>
										<p><?php echo esc_html( $title ); ?></p>
									<?php endif; ?>
								</li>
							<?php endwhile; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>

			<div class="ane-kanan">
				<div class="ane-text">
					<?php
					$title = get_sub_field( 'ane_title' );
					if ( ! empty( $title ) ) : ?>
						<h2><?php echo wp_kses_post( $title ); ?></h2>
					<?php endif; ?>

					<?php
					$subtitle = get_sub_field( 'ane_subtitle' );
					if ( ! empty( $subtitle ) ) : ?>
						<h3><?php echo wp_kses_post( $subtitle ); ?></h3>
					<?php endif; ?>

					<?php
					$desc = get_sub_field( 'ane_deskripsi' );
					if ( ! empty( $desc ) ) : ?>
						<p><?php echo wp_kses_post( $desc ); ?></p>
					<?php endif; ?>

					<?php
					$link = get_sub_field( 'ane_link' );
					if ( ! empty( $link ) && is_array( $link ) && isset( $link['url'], $link['title'] ) ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="btn btn-primary"><?php echo esc_html( $link['title'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
