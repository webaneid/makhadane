<?php
/**
 * Template Name: Kelembagaan
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="site-content">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'tp/pages/kelembagaan' );
		endwhile;
	endif;
	?>
</main>

<?php get_footer(); ?>