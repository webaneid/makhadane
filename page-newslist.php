<?php
/**
 * Template Name: Blog Type
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main" role="main">
	<?php
	$template = wp_is_mobile() ? 'tp/pages/news-mobile' : 'tp/pages/news-desktop';
	locate_template( $template . '.php', true, true );
	?>
</main>

<?php get_footer(); ?>
