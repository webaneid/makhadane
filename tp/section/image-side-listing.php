<?php
/**
 * Image Side Listing Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="image-side-text gp">
	<div class="ane-container">
		<div class="ane-col-2 align-items-center <?php echo esc_attr( get_sub_field( 'ane_posisi' ) ?? '' ); ?>">
			<div class="ane-item">
				<?php
				$image = get_sub_field( 'ane_image' );
				if ( ! empty( $image ) ) :
					$size   = 'persegi';
					$medium = 'medium';
					$thumb  = wp_is_mobile() ? ( $image['sizes'][ $medium ] ?? $image['url'] ) : ( $image['sizes'][ $size ] ?? $image['url'] );
					?>
					<div class="ane-image">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" title="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" />
					</div>
				<?php endif; ?>
			</div>

			<div class="ane-item">
				<div class="ane-text">
					<?php
					$title = get_sub_field( 'ane_title' );
					if ( ! empty( $title ) ) :
						echo '<h2>' . wp_kses_post( $title ) . '</h2>';
					endif;

					$subtitle = get_sub_field( 'ane_subtitle' );
					if ( ! empty( $subtitle ) ) :
						echo '<h3>' . wp_kses_post( $subtitle ) . '</h3>';
					endif;

					$desc = get_sub_field( 'ane_deskripsi' );
					if ( ! empty( $desc ) ) :
						echo '<p>' . esc_html( $desc ) . '</p>';
					endif;

					if ( have_rows( 'ane_repeater' ) ) : ?>
						<ul>
							<?php while ( have_rows( 'ane_repeater' ) ) : the_row();
								$titler     = get_sub_field( 'ane_title' );
								$deskripsir = get_sub_field( 'ane_deskripsi' );
								?>
								<li>
									<?php if ( ! empty( $titler ) ) : ?>
										<h5><?php echo esc_html( $titler ); ?></h5>
									<?php endif; ?>

									<?php if ( ! empty( $deskripsir ) ) : ?>
										<p><?php echo esc_html( $deskripsir ); ?></p>
									<?php endif; ?>
								</li>
							<?php endwhile; ?>
						</ul>
					<?php endif;

					$link = get_sub_field( 'ane_link' );
					if ( ! empty( $link ) ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="btn btn-primary">
							<?php echo esc_html( $link['title'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
