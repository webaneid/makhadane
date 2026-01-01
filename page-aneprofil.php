<?php
/**
 * Template Name: Profil Lembaga
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'tp/pages/profil' );
		endwhile;
	endif;
	?>
</main>

<?php get_footer(); ?>
