<?php
/**
 * Classic Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-klasik'); ?>>
	<header class="entry-header">
		<?php ane_get_featured_image(); ?>
	</header>

	<div class="post-content">
		<!-- Display post title -->
		<?php
			ane_get_title();
			ane_get_meta_content();
		?>

		<!-- Display excerpt -->
		<?php the_excerpt(); ?>
	</div>
</article>
