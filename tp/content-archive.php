<?php
/**
 * Template Part: Archive Content
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ane-kanan ane-post">
	<?php if ( have_posts() ) : ?>
		<?php
		$counter = 0;

		while ( have_posts() ) :
			the_post();
			$counter++;

			if ( $counter === 1 || $counter === 5 ) :
				get_template_part( 'tp/content', 'overlay' );
			else :
				if ( wp_is_mobile() ) {
					get_template_part( 'tp/content', 'list' );
				} else {
					get_template_part( 'tp/content', get_post_format() ?: 'standard' );
				}
			endif;
		endwhile;
		?>

		<?php ane_post_pagination(); ?>

	<?php else : ?>
		<?php get_template_part( 'tp/content', 'none' ); ?>
	<?php endif; ?>
</div>
