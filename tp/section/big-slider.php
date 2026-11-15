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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, WordPress.WP.GlobalVariablesOverride.Prohibited
?>
<section class="home-slider v2">
	<div class="home-big-slider owl-carousel">
		<?php
		if ( have_rows( 'home_slider' ) ) :
			$makhadane_slide_idx = 0;
			while ( have_rows( 'home_slider' ) ) :
				the_row();

				$bg        = trim( (string) get_sub_field( 'ane_background' ) );
				$judul     = get_sub_field( 'ane_title' );
				$deskripsi = get_sub_field( 'ane_deskripsi' );
				$link      = get_sub_field( 'ane_link' );
				$posisi    = get_sub_field( 'ane_posisi' );

				$judul      = ! empty( $judul ) ? wp_kses_post( $judul ) : '';
				$deskripsi  = ! empty( $deskripsi ) ? esc_html( $deskripsi ) : '';
				$posisi     = ! empty( $posisi ) ? sanitize_html_class( $posisi ) : '';
				$link_url   = ( is_array( $link ) && ! empty( $link['url'] ) ) ? esc_url( $link['url'] ) : '';
				$link_title = ( is_array( $link ) && ! empty( $link['title'] ) ) ? esc_html( $link['title'] ) : '';
				$img_alt    = '' !== $judul ? wp_strip_all_tags( $judul ) : (string) get_bloginfo( 'name' );

				$img_src    = $bg;
				$img_srcset = '';
				$img_w      = 1280;
				$img_h      = 720;

				if ( '' !== $bg ) {
					$att_id = makhadane_get_attachment_id_from_url( $bg );
					if ( $att_id > 0 ) {
						$size = wp_is_mobile() ? 'medium' : 'large';
						$data = wp_get_attachment_image_src( $att_id, $size );
						if ( is_array( $data ) && ! empty( $data[0] ) ) {
							$img_src = (string) $data[0];
							$img_w   = ! empty( $data[1] ) ? (int) $data[1] : 1000;
							$img_h   = ! empty( $data[2] ) ? (int) $data[2] : 563;
						}
						$img_srcset = (string) wp_get_attachment_image_srcset( $att_id, $size );
					}
				}
				?>
			<div class="home-slider-item <?php echo esc_attr( $posisi . '-top' ); ?>">
				<?php if ( '' !== $img_src ) : ?>
					<?php if ( 0 === $makhadane_slide_idx ) : ?>
						<img class="home-slider-bg" src="<?php echo esc_url( $img_src ); ?>"<?php echo '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 240px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $img_w ); ?>" height="<?php echo esc_attr( (string) $img_h ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" fetchpriority="high" decoding="async">
					<?php else : ?>
						<img class="home-slider-bg" src="<?php echo esc_url( $img_src ); ?>"<?php echo '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 240px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $img_w ); ?>" height="<?php echo esc_attr( (string) $img_h ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" fetchpriority="low" decoding="async">
					<?php endif; ?>
				<?php endif; ?>
				<div class="overlay"></div>
				<div class="ane-container">
					<?php if ( ! empty( $judul ) ) : ?>
						<div class="text <?php echo esc_attr( $posisi ); ?> mplr">
							<div class="text-content">
								<h2><?php echo wp_kses_post( $judul ); ?></h2>
								<?php if ( ! empty( $deskripsi ) ) : ?>
									<p><?php echo esc_html( $deskripsi ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $link_url ) ) : ?>
									<a href="<?php echo esc_url( $link_url ); ?>" class="btn btn-primary" title="<?php echo esc_attr( $link_title ); ?>">
										<?php echo esc_html( $link_title ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
				<?php
				++$makhadane_slide_idx;
			endwhile;
		endif;
		?>
	</div>
</section>
