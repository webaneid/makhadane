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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_title      = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
$makhadane_subtitle   = trim( (string) get_sub_field( 'ane_subtitle' ) );
$makhadane_desc       = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_deskripsi' ) ) );
$makhadane_link       = get_sub_field( 'ane_link' );
$makhadane_link_url   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['url'] ) ) ? trim( (string) $makhadane_link['url'] ) : '';
$makhadane_link_title = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['title'] ) ) ? trim( wp_strip_all_tags( (string) $makhadane_link['title'] ) ) : '';
$makhadane_link_tgt   = ( is_array( $makhadane_link ) && ! empty( $makhadane_link['target'] ) ) ? trim( (string) $makhadane_link['target'] ) : '';

$makhadane_image   = get_sub_field( 'ane_image' );
$makhadane_img_url = '';
$makhadane_img_alt = get_bloginfo( 'name' );
if ( is_array( $makhadane_image ) ) {
	$makhadane_img_url = (string) ( $makhadane_image['sizes']['medium_large'] ?? ( $makhadane_image['sizes']['medium'] ?? ( $makhadane_image['url'] ?? '' ) ) );
	if ( ! empty( $makhadane_image['alt'] ) ) {
		$makhadane_img_alt = (string) $makhadane_image['alt'];
	}
}

$makhadane_faq_query = new WP_Query(
	array(
		'post_type'      => 'faq',
		'posts_per_page' => 7,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	)
);
?>
<section class="ane-home-faq gp">
	<div class="ane-container">
		<div class="faq-layout">
			<div class="faq-intro">
				<div class="ane-text">
					<?php if ( '' !== $makhadane_title ) : ?>
						<h2 class="section-title"><?php echo esc_html( $makhadane_title ); ?></h2>
					<?php endif; ?>

					<?php if ( '' !== $makhadane_subtitle ) : ?>
						<h3 class="section-tagline"><?php echo wp_kses_post( $makhadane_subtitle ); ?></h3>
					<?php endif; ?>

					<?php if ( '' !== $makhadane_desc ) : ?>
						<p class="section-description"><?php echo esc_html( $makhadane_desc ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $makhadane_link_url && '' !== $makhadane_link_title ) : ?>
						<a href="<?php echo esc_url( $makhadane_link_url ); ?>" class="btn btn-primary faq-cta-btn"<?php echo '_blank' === $makhadane_link_tgt ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<span><?php echo esc_html( $makhadane_link_title ); ?></span>
							<?php echo wp_kses_post( makhadane_icon( 'chevron-right' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $makhadane_img_url ) : ?>
					<div class="faq-media">
						<img src="<?php echo esc_url( $makhadane_img_url ); ?>" width="640" height="400" alt="<?php echo esc_attr( $makhadane_img_alt ); ?>" loading="lazy" decoding="async">
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $makhadane_faq_query->have_posts() ) : ?>
				<div class="faq-list">
					<div class="accordion">
						<?php
						$makhadane_faq_idx = 0;
						while ( $makhadane_faq_query->have_posts() ) :
							$makhadane_faq_query->the_post();
							$makhadane_is_open = ( 0 === $makhadane_faq_idx ) ? 'true' : 'false';
							?>
							<div class="accordion-item">
								<button id="accordion-button-<?php the_ID(); ?>" type="button" aria-expanded="<?php echo esc_attr( $makhadane_is_open ); ?>">
									<span class="accordion-title"><?php echo esc_html( get_the_title() ); ?></span>
									<span class="icon" aria-hidden="true"></span>
								</button>
								<div class="accordion-content">
									<?php echo wp_kses_post( wpautop( get_the_content() ) ); ?>
								</div>
							</div>
							<?php
							++$makhadane_faq_idx;
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
