<?php
/**
 * Featured Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( have_rows( 'ane_featured' ) ) :
	$makhadane_bg_raw        = (string) get_sub_field( 'ane_featured_bg' );
	$makhadane_bg_cls        = ( 'dark' === $makhadane_bg_raw ) ? 'is-dark' : 'is-light';
	$makhadane_section_title = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_featured_title' ) ) );
	$makhadane_section_intro = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_featured_intro' ) ) );

	$makhadane_items = array();
	while ( have_rows( 'ane_featured' ) ) :
		the_row();
		$raw_link    = get_sub_field( 'ane_link' );
		$link_url    = ( is_array( $raw_link ) && ! empty( $raw_link['url'] ) ) ? trim( (string) $raw_link['url'] ) : '';
		$link_title  = ( is_array( $raw_link ) && ! empty( $raw_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $raw_link['title'] ) ) : '';
		$link_target = ( is_array( $raw_link ) && ! empty( $raw_link['target'] ) ) ? trim( (string) $raw_link['target'] ) : '';
		$image_url   = trim( (string) get_sub_field( 'ane_icon' ) );
		$judul       = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
		$deskripsi   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );

		if ( '' !== $judul || '' !== $deskripsi || '' !== $image_url || '' !== $link_url ) {
			$makhadane_items[] = array(
				'image'       => $image_url,
				'title'       => $judul,
				'desc'        => $deskripsi,
				'link_url'    => $link_url,
				'link_title'  => $link_title,
				'link_target' => $link_target,
			);
		}
	endwhile;

	if ( ! empty( $makhadane_items ) ) :
		?>
		<section class="section-featured <?php echo esc_attr( $makhadane_bg_cls ); ?>">
			<div class="ane-container">
				<?php if ( '' !== $makhadane_section_title ) : ?>
					<h2 class="section-title"><?php echo esc_html( $makhadane_section_title ); ?></h2>
				<?php endif; ?>

				<div class="section-featured-grid">
					<?php foreach ( $makhadane_items as $makhadane_item ) : ?>
						<?php
						$has_link = '' !== $makhadane_item['link_url'];
						$is_blank = '_blank' === $makhadane_item['link_target'];
						$alt_text = '' !== $makhadane_item['title'] ? $makhadane_item['title'] : get_bloginfo( 'name' );
						?>
						<article class="section-featured-card">
							<span class="section-featured-watermark" aria-hidden="true">
								<?php if ( '' !== $makhadane_item['image'] ) : ?>
									<img src="<?php echo esc_url( $makhadane_item['image'] ); ?>" width="108" height="108" alt="" loading="lazy" decoding="async">
								<?php else : ?>
									<?php echo wp_kses_post( makhadane_icon( 'star' ) ); ?>
								<?php endif; ?>
							</span>

							<div class="section-featured-icon" aria-hidden="true">
								<?php if ( '' !== $makhadane_item['image'] ) : ?>
									<img src="<?php echo esc_url( $makhadane_item['image'] ); ?>" width="28" height="28" alt="<?php echo esc_attr( $alt_text ); ?>" loading="lazy" decoding="async">
								<?php else : ?>
									<?php echo wp_kses_post( makhadane_icon( 'star' ) ); ?>
								<?php endif; ?>
							</div>

							<?php if ( '' !== $makhadane_item['title'] ) : ?>
								<h3 class="section-featured-card-title">
									<?php if ( $has_link ) : ?>
										<a href="<?php echo esc_url( $makhadane_item['link_url'] ); ?>"<?php echo $is_blank ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
											<?php echo esc_html( $makhadane_item['title'] ); ?>
										</a>
									<?php else : ?>
										<?php echo esc_html( $makhadane_item['title'] ); ?>
									<?php endif; ?>
								</h3>
							<?php endif; ?>

							<?php if ( '' !== $makhadane_item['desc'] ) : ?>
								<p class="section-featured-card-desc"><?php echo esc_html( $makhadane_item['desc'] ); ?></p>
							<?php endif; ?>

							<?php if ( $has_link && '' !== $makhadane_item['link_title'] ) : ?>
								<a class="section-featured-card-link" href="<?php echo esc_url( $makhadane_item['link_url'] ); ?>"<?php echo $is_blank ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
									<span><?php echo esc_html( $makhadane_item['link_title'] ); ?></span>
									<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
								</a>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( '' !== $makhadane_section_intro ) : ?>
					<p class="section-featured-intro"><?php echo esc_html( $makhadane_section_intro ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	endif;
endif;
?>
