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
?>
<section class="image-below-text gp <?php echo esc_attr( get_sub_field( 'ane_background' ) ?? '' ); ?>">
	<div class="ane-container">
		<div class="ane-col-column">
			<div class="ane-item">
				<div class="ane-text">
					<?php
					$title = get_sub_field( 'ane_title' );
					if ( ! empty( $title ) ) :
						echo '<h2>' . wp_kses_post( $title ) . '</h2>';
					endif;

					$subtitle = get_sub_field( 'ane_subtitle' );
					if ( ! empty( $subtitle ) ) :
						echo '<h3>' . esc_html( $subtitle ) . '</h3>';
					endif;

					$desc = get_sub_field( 'ane_deskripsi' );
					if ( ! empty( $desc ) ) :
						echo '<p>' . esc_html( $desc ) . '</p>';
					endif;
					?>
				</div>

				<?php
				$image = get_sub_field( 'ane_image' );
				if ( ! empty( $image ) ) :
					$thumb = $image['sizes']['large'] ?? $image['url'];
					?>
					<div class="ane-image">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" title="<?php echo esc_attr( $image['alt'] ); ?>" />
					</div>
				<?php endif; ?>
			</div>

			<div class="ane-item">
				<div class="ane-text ane-list">
					<?php if ( have_rows( 'ane_repeater' ) ) : ?>
						<ul>
							<?php while ( have_rows( 'ane_repeater' ) ) : the_row();
								$titler     = get_sub_field( 'ane_title' );
								$deskripsir = get_sub_field( 'ane_deskripsi' );
								$icon       = get_sub_field( 'ane_icon' );
								?>
								<li>
									<?php if ( ! empty( $icon ) ) : ?>
										<figure class="list-image">
											<div class="ane-image">
												<img src="<?php echo esc_url( $icon ); ?>" alt="icon">
											</div>
										</figure>
									<?php endif; ?>

									<?php if ( ! empty( $titler ) ) : ?>
										<h5><?php echo esc_html( $titler ); ?></h5>
									<?php endif; ?>

									<?php if ( ! empty( $deskripsir ) ) : ?>
										<p><?php echo esc_html( $deskripsir ); ?></p>
									<?php endif; ?>
								</li>
							<?php endwhile; ?>
						</ul>
					<?php endif; ?>

					<?php
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
