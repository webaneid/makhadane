<?php
/**
 * List Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-lis'); ?>>
	<header class="entry-header">
		<?php ane_get_featured_image_kotak(); ?>
	</header>

	<div class="entry-content">
		<?php
			ane_get_title();
			ane_get_meta_content();
		?>
	</div>
</article>
