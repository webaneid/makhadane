<?php
/**
 * Image Side Text Section Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_posisi_raw = (string) ( get_sub_field( 'ane_posisi' ) ?? 'gambar-kiri' );
$makhadane_posisi     = ( 'gambar-kanan' === $makhadane_posisi_raw ) ? 'gambar-kanan' : 'gambar-kiri';
$makhadane_image      = get_sub_field( 'ane_image' );
$makhadane_title      = trim( (string) get_sub_field( 'ane_title' ) );
$makhadane_subtitle   = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_subtitle' ) ) );
$makhadane_desc       = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_link       = get_sub_field( 'ane_link' );
$makhadane_link_url   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) ) ? trim( (string) $makhadane_link['url'] ) : '';
$makhadane_link_title = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link['title'] ) ) : '';
$makhadane_link_tgt   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['target'] ) ) ? trim( (string) $makhadane_link['target'] ) : '';

$makhadane_raw_steps = get_sub_field( 'ane_side_steps' );
$makhadane_steps     = array();
if ( is_array( $makhadane_raw_steps ) ) {
	foreach ( $makhadane_raw_steps as $makhadane_step_row ) {
		if ( ! is_array( $makhadane_step_row ) ) {
			continue;
		}
		$st_title = trim( wp_strip_all_tags( (string) ( $makhadane_step_row['ane_step_title'] ?? '' ) ) );
		$st_desc  = trim( wp_strip_all_tags( (string) ( $makhadane_step_row['ane_step_desc'] ?? '' ) ) );
		if ( '' !== $st_title || '' !== $st_desc ) {
			$makhadane_steps[] = array(
				'title' => $st_title,
				'desc'  => $st_desc,
			);
		}
		if ( count( $makhadane_steps ) >= 3 ) {
			break;
		}
	}
}

$makhadane_has_steps = ! empty( $makhadane_steps );
$makhadane_img_url   = '';
$makhadane_img_alt   = get_bloginfo( 'name' );
if ( is_array( $makhadane_image ) ) {
	$makhadane_img_url = wp_is_mobile()
		? (string) ( $makhadane_image['sizes']['medium_large'] ?? ( $makhadane_image['sizes']['medium'] ?? ( $makhadane_image['url'] ?? '' ) ) )
		: (string) ( $makhadane_image['sizes']['large'] ?? ( $makhadane_image['sizes']['persegi'] ?? ( $makhadane_image['url'] ?? '' ) ) );
	if ( ! empty( $makhadane_image['alt'] ) ) {
		$makhadane_img_alt = (string) $makhadane_image['alt'];
	}
}
?>
<section class="image-side-text gp">
	<div class="ane-container">
		<div class="ist-layout <?php echo esc_attr( $makhadane_posisi ); ?><?php echo $makhadane_has_steps ? ' has-steps' : ' is-classic'; ?>">
			<?php if ( '' !== $makhadane_img_url ) : ?>
				<div class="ist-media">
					<div class="ane-image">
						<img src="<?php echo esc_url( $makhadane_img_url ); ?>" width="640" height="720" alt="<?php echo esc_attr( $makhadane_img_alt ); ?>" loading="lazy" decoding="async">
					</div>
				</div>
			<?php endif; ?>

			<div class="ist-body">
				<div class="ane-text">
					<?php if ( '' !== $makhadane_subtitle ) : ?>
						<span class="ist-kicker"><?php echo esc_html( $makhadane_subtitle ); ?></span>
					<?php endif; ?>

					<?php if ( '' !== $makhadane_title ) : ?>
						<h2 class="section-title"><?php echo wp_kses_post( $makhadane_title ); ?></h2>
					<?php endif; ?>

					<?php if ( '' !== $makhadane_desc ) : ?>
						<p class="ist-desc"><?php echo esc_html( $makhadane_desc ); ?></p>
					<?php endif; ?>

					<?php if ( ! $makhadane_has_steps && '' !== $makhadane_link_url && '' !== $makhadane_link_title ) : ?>
						<a href="<?php echo esc_url( $makhadane_link_url ); ?>" class="btn btn-primary ist-classic-btn"<?php echo '_blank' === $makhadane_link_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<span><?php echo esc_html( $makhadane_link_title ); ?></span>
							<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( $makhadane_has_steps ) : ?>
					<div class="ist-steps-grid">
						<?php foreach ( $makhadane_steps as $makhadane_step_idx => $makhadane_step ) : ?>
							<div class="ist-step-card">
								<span class="ist-step-num"><?php echo esc_html( sprintf( '%02d', $makhadane_step_idx + 1 ) ); ?></span>
								<?php if ( '' !== $makhadane_step['title'] ) : ?>
									<h3 class="ist-step-title"><?php echo esc_html( $makhadane_step['title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $makhadane_step['desc'] ) : ?>
									<p class="ist-step-desc"><?php echo esc_html( $makhadane_step['desc'] ); ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>

						<?php if ( '' !== $makhadane_link_url && '' !== $makhadane_link_title ) : ?>
							<a href="<?php echo esc_url( $makhadane_link_url ); ?>" class="ist-cta-card"<?php echo '_blank' === $makhadane_link_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
								<span class="ist-cta-pill">
									<span><?php echo esc_html( $makhadane_link_title ); ?></span>
									<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
								</span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
