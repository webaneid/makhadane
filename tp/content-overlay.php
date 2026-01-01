<?php
/**
 * Overlay Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-overlay'); ?>>
	<?php ane_get_featured_image_overlay() ?>
	<div class="post-content">
		<?php
			ane_get_title();
			ane_get_meta_content();
		?>
	</div>
</header> <!-- end of head in function -->
</article>
