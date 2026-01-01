<?php
/**
 * Testimoni Content Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get post thumbnail or fallback to default image
$photo = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'kotak')[0] : ane_dummy_kotak();

// Get the testimoni content and job title
$testimoni = get_field('ane_testimoni');
$jabatan = get_field('ane_jabatan');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ane-konten-testimoni'); ?>>
	<header class="entry-header">
		<?php if ($testimoni) : ?>
			<p><?php echo esc_html($testimoni); ?></p>
		<?php endif; ?>
	</header>

	<div class="post-content">
		<div class="ane-image">
			<img src="<?php echo esc_url($photo); ?>" alt="<?php the_title(); ?>">
		</div>

		<div class="orangnya">
			<h2>
				<a href="/testimoni" rel="bookmark"><?php the_title(); ?></a>
			</h2>
			<?php if ($jabatan) : ?>
				<p><?php echo esc_html($jabatan); ?></p>
			<?php endif; ?>
		</div>
	</div>
</article>
