<?php
/**
 * Ekstrakurikuler Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-overlay'); ?>>
	<?php
	// Check if post has a featured image
	$img_url = has_post_thumbnail()
		? wp_get_attachment_image_url(get_post_thumbnail_id($post->ID), 'medium')
		: ane_dummy_thumbnail(); // Fallback if no thumbnail exists

	// Apply background image
	echo '<header class="entry-header" style="background-image:url(' . esc_url($img_url) . ')">';

	?>
	<div class="post-content">
		<!-- Display title and link to the post -->
		<?php ane_get_title()?>
	</div>
</article>
