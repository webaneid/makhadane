<?php
/**
 * Ustadz Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-guru'); ?>>
	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<header class="entry-header">
			<div class="ane-image">
				<?php
				// Get the thumbnail image or fallback to default
				$photo = has_post_thumbnail() ? wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'kotak' )[0] : ane_dummy_thumbnail();
				?>
				<img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		</header><!-- .entry-header -->

		<div class="entry-content text-center">
			<?php ane_get_title()?>
			<p><?php echo esc_html(get_field('ane_jabatan')); ?></p>
		</div><!-- .entry-content -->
	</a>
</article>
