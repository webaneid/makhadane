<?php
/**
 * Home Desktop Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'landing-page' ); ?>>
	<?php
	if ( have_rows( 'home_content' ) ) :
		while ( have_rows( 'home_content' ) ) :
			the_row();
			$layout = esc_attr( get_row_layout() );

			switch ( $layout ) {
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
</article>
