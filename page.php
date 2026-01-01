<?php
/**
 * Default Page Template
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
			get_template_part( 'tp/content', 'page' );
		endwhile;
	else :
		get_template_part( 'tp/content', 'none' );
	endif;
	?>
</main>

<?php get_footer(); ?>
