<?php
/**
 * Profile Page Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

$makhadane_thumb_id = get_post_thumbnail_id( get_the_ID() );
$makhadane_thumb    = $makhadane_thumb_id ? wp_get_attachment_image_src( $makhadane_thumb_id, 'large' ) : false;
$makhadane_photo    = ( is_array( $makhadane_thumb ) && ! empty( $makhadane_thumb[0] ) ) ? (string) $makhadane_thumb[0] : ( function_exists( 'ane_dummy_thumbnail' ) ? ane_dummy_thumbnail() : '' );
$makhadane_headline = trim( wp_strip_all_tags( (string) get_field( 'ane_headline' ) ) );

$makhadane_funfacts = array();
if ( have_rows( 'ane_data_sekolah' ) ) {
	while ( have_rows( 'ane_data_sekolah' ) ) {
		the_row();
		$ff_icon  = trim( (string) get_sub_field( 'ane_icon' ) );
		$ff_title = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_title' ) ) );
		$ff_value = trim( wp_strip_all_tags( (string) get_sub_field( 'ane_jumlah' ) ) );
		if ( '' !== $ff_title || '' !== $ff_value ) {
			$makhadane_funfacts[] = array(
				'icon'  => '' !== $ff_icon ? $ff_icon : 'building',
				'title' => $ff_title,
				'value' => $ff_value,
			);
		}
	}
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'landing-page ane-page-profil' ); ?>>
	<header class="entry-header profil-hero">
		<?php if ( '' !== $makhadane_photo ) : ?>
			<img class="home-slider-bg" src="<?php echo esc_url( $makhadane_photo ); ?>" width="1280" height="720" alt="<?php echo esc_attr( get_the_title() ); ?>" fetchpriority="high" decoding="async">
		<?php endif; ?>
		<div class="ane-container">
			<div class="profil-hero-inner">
				<div class="profil-hero-copy">
					<h1 class="post-title general-title"><?php echo esc_html( get_the_title() ); ?></h1>
					<?php if ( '' !== $makhadane_headline ) : ?>
						<p class="profil-hero-headline"><?php echo esc_html( $makhadane_headline ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $makhadane_funfacts ) ) : ?>
					<div class="home-hero-funfacts-wrap profil-hero-funfacts">
						<?php foreach ( $makhadane_funfacts as $makhadane_ff ) : ?>
							<div class="home-hero-funfact-pill">
								<span class="home-hero-funfact-icon" aria-hidden="true">
									<?php echo wp_kses_post( makhadane_icon( $makhadane_ff['icon'] ) ); ?>
								</span>
								<div class="home-hero-funfact-text">
									<?php if ( '' !== $makhadane_ff['title'] ) : ?>
										<span class="home-hero-funfact-title"><?php echo esc_html( $makhadane_ff['title'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $makhadane_ff['value'] ) : ?>
										<span class="home-hero-funfact-value"><?php echo esc_html( $makhadane_ff['value'] ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<?php
	if ( function_exists( 'ane_display_breadcrumbs' ) ) {
		ane_display_breadcrumbs();
	}
	?>

	<div class="entry-content">
		<div class="ane-container">
			<div class="profil-content-wrap">
				<div class="ane-content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>

		<div class="landing-page">
			<?php
			if ( have_rows( 'home_content' ) ) :
				while ( have_rows( 'home_content' ) ) :
					the_row();
					$makhadane_layout = get_row_layout();

					switch ( $makhadane_layout ) {
						case 'home_box_slider':
							get_template_part( 'tp/section/big-slider' );
							break;
						case 'home_featured':
							get_template_part( 'tp/section/featured' );
							break;
						case 'home_cta':
							get_template_part( 'tp/section/calltoaction' );
							break;
						case 'home_testimoni':
							get_template_part( 'tp/section/testimoni' );
							break;
						case 'home_ekstrakurikuler':
							get_template_part( 'tp/section/ekstra' );
							break;
						case 'home_guru':
							get_template_part( 'tp/section/guru' );
							break;
						case 'home_banner':
							get_template_part( 'tp/section/banner' );
							break;
						case 'home_default':
							get_template_part( 'tp/news/home-news' );
							break;
						case 'home_sliding':
							get_template_part( 'tp/news/home-gallery' );
							break;
						case 'home_about':
							get_template_part( 'tp/section/about-us' );
							break;
						case 'image_side_text':
							get_template_part( 'tp/section/image-side-text' );
							break;
						case 'image_side_listing':
							get_template_part( 'tp/section/image-side-listing' );
							break;
						case 'image_below_text':
							get_template_part( 'tp/section/image-below-text' );
							break;
						case 'home_faq':
							get_template_part( 'tp/section/faq' );
							break;
					}
				endwhile;
			endif;
			?>
		</div>

		<div class="ane-mobile-list">
			<div class="ane-container">
				<?php get_template_part( 'tp/mobile/menu-lembaga' ); ?>
			</div>
		</div>
	</div>
</article>
