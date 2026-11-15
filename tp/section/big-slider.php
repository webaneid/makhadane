<?php
/**
 * Big Slider Section Template Part (`home-slider v2`).
 *
 * Mendukung posisi teks (left, center, right — termasuk nilai lama
 * align-content-start/center/end), opsi overlay gelap/asli, opsi lengkungan
 * bawah hero, serta mode gambar murni tanpa teks bila semua field dikosongkan.
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_raw_curve  = get_sub_field( 'ane_slider_curve' );
$makhadane_show_curve = ( null === $makhadane_raw_curve || '' === $makhadane_raw_curve ) ? true : (bool) $makhadane_raw_curve;
?>
<section class="home-slider v2<?php echo $makhadane_show_curve ? ' has-curve' : ' no-curve'; ?>">
	<div class="home-big-slider owl-carousel">
		<?php
		if ( have_rows( 'home_slider' ) ) :
			$makhadane_slide_idx = 0;
			$makhadane_align_map = array(
				'left'                 => 'text-pos-left',
				'center'               => 'text-pos-center',
				'right'                => 'text-pos-right',
				'align-content-start'  => 'text-pos-left',
				'align-content-center' => 'text-pos-center',
				'align-content-end'    => 'text-pos-right',
			);

			while ( have_rows( 'home_slider' ) ) :
				the_row();

				$bg          = trim( (string) get_sub_field( 'ane_background' ) );
				$judul       = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
				$deskripsi   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
				$slider_link = get_sub_field( 'ane_link' );
				$link_url    = ( is_array( $slider_link ) && ! empty( $slider_link['url'] ) ) ? trim( (string) $slider_link['url'] ) : '';
				$link_title  = ( is_array( $slider_link ) && ! empty( $slider_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $slider_link['title'] ) ) : '';
				$link_target = ( is_array( $slider_link ) && ! empty( $slider_link['target'] ) ) ? trim( (string) $slider_link['target'] ) : '';

				$align_raw     = (string) get_sub_field( 'ane_posisi' );
				$align_class   = isset( $makhadane_align_map[ $align_raw ] ) ? $makhadane_align_map[ $align_raw ] : 'text-pos-left';
				$overlay_raw   = (string) get_sub_field( 'ane_overlay' );
				$overlay_class = ( 'none' === $overlay_raw ) ? 'no-overlay' : 'has-overlay';

				$has_text = ( '' !== $judul || '' !== $deskripsi || ( '' !== $link_url && '' !== $link_title ) );
				$item_cls = 'home-slider-item ' . $align_class . ' ' . $overlay_class;
				$img_alt  = '' !== $judul ? $judul : (string) get_bloginfo( 'name' );

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
				<div class="<?php echo esc_attr( $item_cls ); ?>">
					<?php if ( '' !== $img_src ) : ?>
						<?php if ( 0 === $makhadane_slide_idx ) : ?>
							<img class="home-slider-bg" src="<?php echo esc_url( $img_src ); ?>"<?php echo '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 240px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $img_w ); ?>" height="<?php echo esc_attr( (string) $img_h ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" fetchpriority="high" decoding="async">
						<?php else : ?>
							<img class="home-slider-bg" src="<?php echo esc_url( $img_src ); ?>"<?php echo '' !== $img_srcset ? ' srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: 767px) 240px, 100vw"' : ''; ?> width="<?php echo esc_attr( (string) $img_w ); ?>" height="<?php echo esc_attr( (string) $img_h ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" fetchpriority="low" decoding="async">
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( $has_text ) : ?>
						<div class="ane-container">
							<div class="text mplr">
								<div class="text-content">
									<?php if ( '' !== $judul ) : ?>
										<?php if ( 0 === $makhadane_slide_idx ) : ?>
											<h1 class="home-slider-title"><?php echo esc_html( $judul ); ?></h1>
										<?php else : ?>
											<h2 class="home-slider-title"><?php echo esc_html( $judul ); ?></h2>
										<?php endif; ?>
									<?php endif; ?>

									<?php if ( '' !== $deskripsi ) : ?>
										<p class="home-slider-desc"><?php echo esc_html( $deskripsi ); ?></p>
									<?php endif; ?>

									<?php if ( '' !== $link_url && '' !== $link_title ) : ?>
										<a href="<?php echo esc_url( $link_url ); ?>" class="btn btn-primary home-slider-btn"<?php echo '_blank' === $link_target ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
											<span><?php echo esc_html( $link_title ); ?></span>
											<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php
				++$makhadane_slide_idx;
			endwhile;
		endif;
		?>
	</div>

	<?php if ( $makhadane_show_curve ) : ?>
		<div class="pemanis-slider" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="none" viewBox="0 0 100 100" focusable="false">
				<path d="M 0 0 L0 100 L100 100 L100 0 Q 50 200 0 0"></path>
			</svg>
		</div>
	<?php endif; ?>
</section>
