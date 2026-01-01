<?php
/**
 * Template Name: Landing Page
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main ane-landingpage" role="main">
	<?php
	if ( wp_is_mobile() ) {
		get_template_part( 'tp/pages/home-mobile' );
	} else {
		get_template_part( 'tp/pages/home-desktop' );
	}
	?>
</main>

<?php get_footer(); ?>
