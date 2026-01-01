<?php
/**
 * Template Name: Mobile Category
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php
		if ( have_posts() ) :
			ane_set_views( get_the_ID() );
			while ( have_posts() ) :
				the_post();
				get_template_part( 'tp/mobile/kategori' );
			endwhile;
		endif;
		?>
	</main>
</div>

<?php get_footer(); ?>
