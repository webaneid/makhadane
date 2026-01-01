<?php
/**
 * FAQ Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="image-side-text">
	<div class="ane-container">
		<div class="ane-col-2 ane-home-faq gp">
			<div class="ane-item">
				<div class="ane-text">
					<?php
					$title    = get_sub_field( 'ane_title' );
					$subtitle = get_sub_field( 'ane_subtitle' );
					$desc     = get_sub_field( 'ane_deskripsi' );
					$link     = get_sub_field( 'ane_link' );

					if ( ! empty( $title ) ) :
						echo '<h2>' . wp_kses_post( $title ) . '</h2>';
					endif;

					if ( ! empty( $subtitle ) ) :
						echo '<h3>' . wp_kses_post( $subtitle ) . '</h3>';
					endif;

					if ( ! empty( $desc ) ) :
						echo '<p>' . esc_html( $desc ) . '</p>';
					endif;

					if ( ! empty( $link ) ) :
					?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="btn btn-alternatif">
							<?php echo esc_html( $link['title'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php
				$image = get_sub_field( 'ane_image' );
				if ( ! empty( $image ) ) :
					$url   = esc_url( $image['url'] );
					$alt   = esc_attr( $image['alt'] );
					$thumb = isset( $image['sizes']['medium'] ) ? esc_url( $image['sizes']['medium'] ) : $url;
					?>
					<div class="ane-image">
						<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" />
					</div>
				<?php endif; ?>
			</div>

			<div class="ane-item">
				<div class="ane-text">
					<?php
					$the_query = new WP_Query( array(
						'post_type'      => 'faq',
						'posts_per_page' => 7,
						'post_status'    => 'publish',
					) );

					if ( $the_query->have_posts() ) :
					?>
						<div class="accordion">
							<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
								<div class="accordion-item">
									<button id="accordion-button-<?php the_ID(); ?>" aria-expanded="false">
										<span class="accordion-title"><?php the_title(); ?></span>
										<span class="icon" aria-hidden="true"></span>
									</button>
									<div class="accordion-content">
										<?php echo wpautop( get_the_content() ); ?>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="overlay"></div>
</section>
