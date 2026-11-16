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

$makhadane_bg_class = sanitize_html_class( (string) ( get_sub_field( 'ane_background' ) ?? 'utama' ) );
if ( '' === $makhadane_bg_class ) {
	$makhadane_bg_class = 'utama';
}
$makhadane_title    = trim( (string) get_sub_field( 'ane_title' ) );
$makhadane_subtitle = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_subtitle' ) ) );
$makhadane_desc     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_image    = get_sub_field( 'ane_image' );
$makhadane_link     = get_sub_field( 'ane_link' );
?>
<section class="image-below-text gp <?php echo esc_attr( $makhadane_bg_class ); ?>">
	<div class="ane-container">
		<div class="ane-col-column">
			<div class="ane-item">
				<?php if ( '' !== $makhadane_title || '' !== $makhadane_subtitle || '' !== $makhadane_desc ) : ?>
					<div class="ane-text">
						<?php if ( '' !== $makhadane_title ) : ?>
							<h2 class="section-title"><?php echo wp_kses_post( $makhadane_title ); ?></h2>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_subtitle ) : ?>
							<h3 class="section-tagline"><?php echo wp_kses_post( (string) get_sub_field( 'ane_subtitle' ) ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $makhadane_desc ) : ?>
							<p class="section-description"><?php echo esc_html( $makhadane_desc ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php
				if ( is_array( $makhadane_image ) && ( ! empty( $makhadane_image['sizes']['large'] ) || ! empty( $makhadane_image['url'] ) ) ) :
					$makhadane_thumb = (string) ( $makhadane_image['sizes']['large'] ?? $makhadane_image['url'] );
					$makhadane_alt   = ! empty( $makhadane_image['alt'] ) ? (string) $makhadane_image['alt'] : get_bloginfo( 'name' );
					?>
					<div class="ane-image">
						<img src="<?php echo esc_url( $makhadane_thumb ); ?>" width="1200" height="675" alt="<?php echo esc_attr( $makhadane_alt ); ?>" loading="lazy" decoding="async">
					</div>
				<?php endif; ?>
			</div>

			<?php
			$makhadane_has_repeater = have_rows( 'ane_repeater' );
			$makhadane_has_link     = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) && ! empty( $makhadane_link['title'] ) );
			if ( $makhadane_has_repeater || $makhadane_has_link ) :
				?>
				<div class="ane-item">
					<div class="ane-text ane-list">
						<?php if ( $makhadane_has_repeater ) : ?>
							<ul>
								<?php
								while ( have_rows( 'ane_repeater' ) ) :
									the_row();
									$titler     = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
									$deskripsir = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
									$icon       = trim( (string) get_sub_field( 'ane_icon' ) );
									?>
									<li>
										<?php if ( '' !== $icon ) : ?>
											<figure class="list-image">
												<div class="ane-image">
													<img src="<?php echo esc_url( $icon ); ?>" width="28" height="28" alt="<?php echo esc_attr( '' !== $titler ? $titler : 'icon' ); ?>" loading="lazy" decoding="async">
												</div>
											</figure>
										<?php endif; ?>

										<?php if ( '' !== $titler ) : ?>
											<h5><?php echo esc_html( $titler ); ?></h5>
										<?php endif; ?>

										<?php if ( '' !== $deskripsir ) : ?>
											<p><?php echo esc_html( $deskripsir ); ?></p>
										<?php endif; ?>
									</li>
								<?php endwhile; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $makhadane_has_link ) : ?>
							<a href="<?php echo esc_url( (string) $makhadane_link['url'] ); ?>" class="btn btn-primary"<?php echo ( ! empty( $makhadane_link['target'] ) && '_blank' === $makhadane_link['target'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
								<?php echo esc_html( (string) $makhadane_link['title'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
